<?php

namespace App\Http\Controllers;

use App\Models\Conversacion;
use App\Models\Mensaje;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Endpoints que usa el chat para enviar mensajes y refrescarlos por sondeo,
 * sin recargar la página.
 */
class MensajeController extends Controller
{
    /**
     * Sondeo global: funciona en cualquier pantalla (artículos, movimientos,
     * reportes...) para saber si llegó un mensaje sin entrar a Mensajes.
     *
     * El cliente manda `desde` con el último id que ya conocía. En la primera
     * llamada no lo manda, y por eso nunca notificamos mensajes que ya
     * existían antes de abrir la aplicación.
     */
    public function estado(Request $request): JsonResponse
    {
        $usuario = $request->user();
        $usuarioId = $usuario->id;
        $desde = $request->filled('desde') ? (int) $request->input('desde') : null;

        $conversaciones = Conversacion::delUsuario($usuarioId)
            ->with(['emisor:id,name', 'receptor:id,name', 'ultimoMensaje'])
            ->withCount([
                'mensajes as no_leidos' => fn (Builder $query) => $query
                    ->whereNull('leido_en')
                    ->where('usuario_id', '!=', $usuarioId),
            ])
            ->orderByDesc('ultimo_mensaje_en')
            ->get();

        $conversacionesIds = $conversaciones->pluck('id');

        $cursor = (int) Mensaje::query()
            ->whereIn('conversacion_id', $conversacionesIds)
            ->max('id');

        // Solo se notifica lo que llegó después de la última respuesta. En la
        // primera llamada `desde` es null y por tanto nunca hay novedades:
        // así no avisa por mensajes que ya existían al abrir la aplicación.
        $nuevos = $desde === null
            ? collect()
            : Mensaje::query()
                ->whereIn('conversacion_id', $conversacionesIds)
                ->where('usuario_id', '!=', $usuarioId)
                ->whereNull('leido_en')
                ->where('id', '>', $desde)
                ->with(['usuario:id,name', 'conversacion'])
                ->orderBy('id')
                ->get();

        return response()->json([
            'sin_responder' => $usuario->mensajesNoLeidos(),
            'cursor' => $cursor,
            'nuevos' => $nuevos
                ->map(fn (Mensaje $mensaje) => $this->serializarNotificacion($mensaje, $usuario))
                ->all(),
            'conversaciones' => $conversaciones
                ->map(fn (Conversacion $conversacion) => $this->serializarConversacion($conversacion, $usuario))
                ->all(),
        ]);
    }

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

    /**
     * Guarda la preferencia de notificación sonora del usuario, para que se
     * mantenga aunque cierre sesión o cambie de dispositivo.
     */
    public function sonido(Request $request): JsonResponse
    {
        $data = $request->validate([
            'sonido' => ['required', 'boolean'],
        ]);

        $request->user()->update([
            'sonido_mensajes' => (bool) $data['sonido'],
        ]);

        return response()->json([
            'sonido' => $request->user()->sonido_mensajes,
        ]);
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

    /**
     * Datos de un mensaje entrante, para la notificación global.
     */
    private function serializarNotificacion(Mensaje $mensaje, User $usuario): array
    {
        $conversacion = $mensaje->conversacion;

        return [
            'id' => $mensaje->id,
            'conversacion_id' => $conversacion->id,
            'remitente' => $mensaje->usuario?->name,
            'cuerpo' => $mensaje->cuerpo,
            'resumen' => Str::limit($mensaje->cuerpo, 90),
            'creado_en' => $mensaje->created_at->format('d/m/Y H:i'),
            'url' => route('mensajes.show', $conversacion),
            'interlocutor' => $conversacion->interlocutor($usuario)->name,
        ];
    }

    /**
     * Resumen de una conversación para el desplegable de la barra.
     */
    private function serializarConversacion(Conversacion $conversacion, User $usuario): array
    {
        $ultimo = $conversacion->ultimoMensaje;

        return [
            'id' => $conversacion->id,
            'interlocutor' => $conversacion->interlocutor($usuario)->name,
            'no_leidos' => (int) $conversacion->no_leidos,
            'resumen' => $ultimo ? Str::limit($ultimo->cuerpo, 60) : 'Sin mensajes',
            'creado_en' => $conversacion->ultimo_mensaje_en?->format('d/m/Y H:i'),
            'url' => route('mensajes.show', $conversacion),
        ];
    }
}
