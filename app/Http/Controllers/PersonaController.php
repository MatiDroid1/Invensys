<?php

namespace App\Http\Controllers;

use App\AuditoriaService;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PersonaController extends Controller
{
    public function __construct(
        private AuditoriaService $auditoriaService
    ) {}

    public function index()
    {
        $personas = Persona::orderBy('apellido')
            ->orderBy('nombre')
            ->get();

        return view('personas.index', compact('personas'));
    }

    public function create()
    {
        return view('personas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'identificador' => ['nullable', 'string', 'max:50', 'unique:personas,identificador'],
            'email' => ['nullable', 'email', 'max:150'],
            'area' => ['nullable', 'string', 'max:100'],
            'cargo' => ['nullable', 'string', 'max:100'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $data['activo'] = $request->boolean('activo', true);

        $persona = DB::transaction(function () use ($data, $request) {
            $persona = Persona::create($data);

            $this->auditoriaService->registrar(
                modulo: 'personas',
                accion: 'CREAR',
                modelo: $persona,
                descripcion: 'Persona creada.',
                datos: $data,
                usuarioId: $request->user()->id,
            );

            return $persona;
        });

        return redirect()
            ->route('personas.index')
            ->with('success', 'Persona creada correctamente.');
    }

    public function edit(Persona $persona)
    {
        return view('personas.edit', compact('persona'));
    }

    public function update(Request $request, Persona $persona)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'identificador' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('personas', 'identificador')->ignore($persona->id),
            ],
            'email' => ['nullable', 'email', 'max:150'],
            'area' => ['nullable', 'string', 'max:100'],
            'cargo' => ['nullable', 'string', 'max:100'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $data['activo'] = $request->boolean('activo');

        $antes = $persona->only([
            'nombre',
            'apellido',
            'identificador',
            'email',
            'area',
            'cargo',
            'activo',
        ]);

        DB::transaction(function () use ($persona, $data, $antes, $request) {
            $persona->update($data);

            $cambios = $persona->getChanges();

            unset($cambios['updated_at']);

            $this->auditoriaService->registrar(
                modulo: 'personas',
                accion: 'ACTUALIZAR',
                modelo: $persona,
                descripcion: 'Persona actualizada.',
                datos: [
                    'antes' => $antes,
                    'cambios' => $cambios,
                ],
                usuarioId: $request->user()->id,
            );
        });

        return redirect()
            ->route('personas.index')
            ->with('success', 'Persona actualizada correctamente.');
    }

    public function destroy(Persona $persona, Request $request)
    {
        DB::transaction(function () use ($persona, $request) {
            $persona->update([
                'activo' => false,
            ]);

            $this->auditoriaService->registrar(
                modulo: 'personas',
                accion: 'DESACTIVAR',
                modelo: $persona,
                descripcion: 'Persona desactivada.',
                datos: [
                    'activo' => false,
                ],
                usuarioId: $request->user()->id,
            );
        });

        return redirect()
            ->route('personas.index')
            ->with('success', 'Persona desactivada correctamente.');
    }
}
