<?php

namespace App\Http\Controllers;

use App\AuditoriaService;
use App\Models\Conversacion;
use App\Models\Mensaje;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ConversacionController extends Controller
{
    public function __construct(
        private AuditoriaService $auditoriaService
    ) {}

    /**
     * Bandeja de conversaciones. Las que tienen mensajes sin responder se
     * listan primero.
     */
    public function index(Request $request)
    {
        $usuarioId = $request->user()->id;

        $conversaciones = Conversacion::delUsuario($usuarioId)
            ->with(['emisor', 'receptor', 'ultimoMensaje.usuario'])
            ->withCount([
                'mensajes as mensajes_no_leidos' => function (Builder $query) use ($usuarioId) {
                    $query->whereNull('leido_en')
                        ->where('usuario_id', '!=', $usuarioId);
                },
            ])
            ->orderByRaw('COALESCE(ultimo_mensaje_en, created_at) DESC')
            ->get()
            ->sortByDesc(fn (Conversacion $conversacion) => $conversacion->mensajes_no_leidos)
            ->values();

        return view('mensajes.index', [
            'conversaciones' => $conversaciones,
            'sinResponder' => $conversaciones->filter(
                fn (Conversacion $conversacion) => $conversacion->mensajes_no_leidos > 0
            ),
        ]);
    }

    /**
     * Formulario para iniciar una conversación con otro usuario.
     */
    public function create(Request $request)
    {
        $destinatario = null;

        if ($request->filled('usuario_id')) {
            $destinatario = User::where('activo', true)
                ->whereKey($request->input('usuario_id'))
                ->whereKeyNot($request->user()->id)
                ->first();
        }

        $disponibles = User::where('activo', true)
            ->whereKeyNot($request->user()->id)
            ->orderBy('name')
            ->get();

        return view('mensajes.create', compact('disponibles', 'destinatario'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'usuario_receptor_id' => [
                'required',
                Rule::exists('users', 'id')->where('activo', true),
            ],
            'cuerpo' => ['nullable', 'string', 'max:2000'],
        ]);

        $usuarioId = $request->user()->id;
        $receptorId = (int) $data['usuario_receptor_id'];

        if ($receptorId === $usuarioId) {
            return back()
                ->withErrors(['usuario_receptor_id' => 'No puedes enviarte mensajes a ti mismo.'])
                ->withInput();
        }

        $conversacion = Conversacion::entre($usuarioId, $receptorId);

        DB::transaction(function () use (&$conversacion, $usuarioId, $receptorId, $data) {
            if (! $conversacion) {
                $conversacion = Conversacion::create([
                    'usuario_emisor_id' => $usuarioId,
                    'usuario_receptor_id' => $receptorId,
                ]);

                $this->auditoriaService->registrar(
                    modulo: 'mensajes',
                    accion: 'CREAR',
                    modelo: $conversacion,
                    descripcion: 'Conversación iniciada con otro usuario.',
                    datos: [
                        'usuario_receptor_id' => $receptorId,
                    ],
                    usuarioId: $usuarioId,
                );
            }

            if (filled($data['cuerpo'] ?? null)) {
                Mensaje::create([
                    'conversacion_id' => $conversacion->id,
                    'usuario_id' => $usuarioId,
                    'cuerpo' => $data['cuerpo'],
                ]);

                $conversacion->update([
                    'ultimo_mensaje_en' => now(),
                ]);
            }
        });

        return redirect()
            ->route('mensajes.show', $conversacion)
            ->with('success', 'Conversación iniciada.');
    }

    /**
     * Chat de una conversación. Al abrirla se marcan como leídos los mensajes
     * recibidos que seguían pendientes.
     */
    public function show(Request $request, Conversacion $conversacion)
    {
        $usuarioId = $request->user()->id;

        abort_unless($conversacion->participa($usuarioId), 403);

        $conversacion->mensajes()
            ->whereNull('leido_en')
            ->where('usuario_id', '!=', $usuarioId)
            ->update(['leido_en' => now()]);

        $conversacion->load([
            'emisor',
            'receptor',
            'mensajes.usuario',
        ]);

        return view('mensajes.show', [
            'conversacion' => $conversacion,
            'interlocutor' => $conversacion->interlocutor($request->user()),
        ]);
    }
}
