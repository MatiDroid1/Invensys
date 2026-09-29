<?php

namespace App\Http\Controllers;

use App\Models\Conversacion;
use App\Models\Mensaje;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Endpoints que usa el chat para enviar mensajes y refrescarlos por sondeo,
 * sin recargar la página.
 */
class MensajeController extends Controller
{
    /**
     * Devuelve los mensajes posteriores a $despues y marca como leídos los que
     * me corresponden.
     */
    public function index(Request $request, Conversacion $conversacion): JsonResponse
    {
        $usuarioId = $request->user()->id;

        abort_unless($conversacion->participa($usuarioId), 403);

        $mensajes = $conversacion->mensajes()
            ->with('usuario')
            ->when(
                $request->filled('despues'),
                fn (Builder $query) => $query->where('id', '>', (int) $request->input('despues'))
            )
            ->orderBy('id')
            ->get();

        $conversacion->mensajes()
            ->whereNull('leido_en')
            ->where('usuario_id', '!=', $usuarioId)
            ->update(['leido_en' => now()]);

        return response()->json([
            'mensajes' => $mensajes
                ->map(fn (Mensaje $mensaje) => $this->serializar($mensaje, $usuarioId))
                ->all(),
            'sin_responder' => $request->user()->mensajesNoLeidos(),
        ]);
    }

    public function store(Request $request, Conversacion $conversacion): JsonResponse
    {
        $usuarioId = $request->user()->id;

        abort_unless($conversacion->participa($usuarioId), 403);

        $data = $request->validate([
            'cuerpo' => ['required', 'string', 'max:2000'],
        ]);

        $mensaje = DB::transaction(function () use ($conversacion, $usuarioId, $data) {
            $mensaje = Mensaje::create([
                'conversacion_id' => $conversacion->id,
                'usuario_id' => $usuarioId,
                'cuerpo' => $data['cuerpo'],
            ]);

            $conversacion->update([
                'ultimo_mensaje_en' => now(),
            ]);

            return $mensaje;
        });

        $mensaje->load('usuario');

        return response()->json([
            'mensaje' => $this->serializar($mensaje, $usuarioId),
            'sin_responder' => $request->user()->mensajesNoLeidos(),
        ], 201);
    }

    private function serializar(Mensaje $mensaje, int $usuarioId): array
    {
        return [
            'id' => $mensaje->id,
            'cuerpo' => $mensaje->cuerpo,
            'propio' => $mensaje->fueEnviadoPor($usuarioId),
            'nombre' => $mensaje->usuario?->name,
            'creado_en' => $mensaje->created_at->format('d/m/Y H:i'),
        ];
    }
}
