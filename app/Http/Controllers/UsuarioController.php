<?php

namespace App\Http\Controllers;

use App\AuditoriaService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function __construct(
        private AuditoriaService $auditoriaService
    ) {}

    public function index()
    {
        $usuarios = User::orderBy('name')->get();

        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'rol' => ['required', 'in:admin,usuario'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['activo'] = $request->boolean('activo', true);

        $datosAuditoria = $data;
        unset($datosAuditoria['password']);

        DB::transaction(function () use ($data, $datosAuditoria, $request) {
            $usuario = User::create($data);

            $this->auditoriaService->registrar(
                modulo: 'usuarios',
                accion: 'CREAR',
                modelo: $usuario,
                descripcion: 'Usuario creado.',
                datos: $datosAuditoria,
                usuarioId: $request->user()->id,
            );
        });

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, User $usuario)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($usuario->id),
            ],
            'password' => ['nullable', 'confirmed', 'min:8'],
            'rol' => ['required', 'in:admin,usuario'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $antes = $usuario->only([
            'name',
            'email',
            'rol',
            'activo',
        ]);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['activo'] = $request->boolean('activo');

        DB::transaction(function () use ($usuario, $data, $antes, $request) {
            $usuario->update($data);

            $cambios = $usuario->getChanges();

            unset(
                $cambios['updated_at'],
                $cambios['password']
            );

            $this->auditoriaService->registrar(
                modulo: 'usuarios',
                accion: 'ACTUALIZAR',
                modelo: $usuario,
                descripcion: 'Usuario actualizado.',
                datos: [
                    'antes' => $antes,
                    'cambios' => $cambios,
                ],
                usuarioId: $request->user()->id,
            );
        });

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario, Request $request)
    {
        if ($usuario->id === $request->user()->id) {
            return redirect()
                ->route('usuarios.index')
                ->with('error', 'No puedes desactivarte a ti mismo.');
        }

        DB::transaction(function () use ($usuario, $request) {
            $usuario->update([
                'activo' => false,
            ]);

            $this->auditoriaService->registrar(
                modulo: 'usuarios',
                accion: 'DESACTIVAR',
                modelo: $usuario,
                descripcion: 'Usuario desactivado.',
                datos: [
                    'activo' => false,
                ],
                usuarioId: $request->user()->id,
            );
        });

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario desactivado correctamente.');
    }
}
