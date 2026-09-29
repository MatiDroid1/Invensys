<?php

namespace App\Http\Controllers;

use App\AuditoriaService;
use App\Models\Contacto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactoController extends Controller
{
    public function __construct(
        private AuditoriaService $auditoriaService
    ) {}

    public function index()
    {
        $contactos = Contacto::with('usuario')
            ->orderByRaw("CASE WHEN estado = 'PENDIENTE' THEN 0 ELSE 1 END")
            ->orderByDesc('created_at')
            ->get();

        return view('contacto.index', compact('contactos'));
    }

    public function create()
    {
        return view('contacto.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'asunto' => ['required', 'string', 'max:150'],
            'mensaje' => ['required', 'string'],
        ]);

        $contacto = DB::transaction(function () use ($data, $request) {
            $contacto = Contacto::create([
                'usuario_id' => $request->user()->id,
                'nombre' => $data['nombre'],
                'email' => $data['email'],
                'asunto' => $data['asunto'],
                'mensaje' => $data['mensaje'],
                'estado' => 'PENDIENTE',
            ]);

            $this->auditoriaService->registrar(
                modulo: 'contactos',
                accion: 'CREAR',
                modelo: $contacto,
                descripcion: 'Mensaje de contacto registrado.',
                datos: [
                    'nombre' => $contacto->nombre,
                    'email' => $contacto->email,
                    'asunto' => $contacto->asunto,
                    'estado' => $contacto->estado,
                ],
                usuarioId: $request->user()->id,
            );

            return $contacto;
        });

        return redirect()
            ->route('contacto.create')
            ->with('success', 'Mensaje enviado correctamente.');
    }

    public function atender(string $id, Request $request)
    {
        $contacto = Contacto::findOrFail($id);

        DB::transaction(function () use ($contacto, $request) {
            $estadoAnterior = $contacto->estado;

            $contacto->update([
                'estado' => 'ATENDIDO',
            ]);

            $this->auditoriaService->registrar(
                modulo: 'contactos',
                accion: 'ATENDER',
                modelo: $contacto,
                descripcion: 'Mensaje de contacto marcado como atendido.',
                datos: [
                    'estado_anterior' => $estadoAnterior,
                    'estado_nuevo' => 'ATENDIDO',
                ],
                usuarioId: $request->user()->id,
            );
        });

        return redirect()
            ->route('contacto.index')
            ->with('success', 'Mensaje marcado como atendido.');
    }
}
