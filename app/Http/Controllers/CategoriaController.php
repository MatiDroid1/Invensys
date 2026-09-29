<?php

namespace App\Http\Controllers;

use App\AuditoriaService;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    public function __construct(
        private AuditoriaService $auditoriaService
    ) {}

    public function index()
    {
        $categorias = Categoria::orderBy('nombre')->get();

        return view('categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:100', 'unique:categorias,nombre'],
            'descripcion' => ['nullable', 'string'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $data['activo'] = $request->boolean('activo', true);

        $categoria = DB::transaction(function () use ($data, $request) {
            $categoria = Categoria::create($data);

            $this->auditoriaService->registrar(
                modulo: 'categorias',
                accion: 'CREAR',
                modelo: $categoria,
                descripcion: 'Categoría creada.',
                datos: $data,
                usuarioId: $request->user()->id,
            );

            return $categoria;
        });

        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $data = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categorias', 'nombre')->ignore($categoria->id),
            ],
            'descripcion' => ['nullable', 'string'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $data['activo'] = $request->boolean('activo');

        $antes = $categoria->only([
            'nombre',
            'descripcion',
            'activo',
        ]);

        DB::transaction(function () use ($categoria, $data, $antes, $request) {
            $categoria->update($data);

            $cambios = $categoria->getChanges();

            unset($cambios['updated_at']);

            $this->auditoriaService->registrar(
                modulo: 'categorias',
                accion: 'ACTUALIZAR',
                modelo: $categoria,
                descripcion: 'Categoría actualizada.',
                datos: [
                    'antes' => $antes,
                    'cambios' => $cambios,
                ],
                usuarioId: $request->user()->id,
            );
        });

        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Categoria $categoria, Request $request)
    {
        DB::transaction(function () use ($categoria, $request) {
            $categoria->update([
                'activo' => false,
            ]);

            $this->auditoriaService->registrar(
                modulo: 'categorias',
                accion: 'DESACTIVAR',
                modelo: $categoria,
                descripcion: 'Categoría desactivada.',
                datos: [
                    'activo' => false,
                ],
                usuarioId: $request->user()->id,
            );
        });

        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoría desactivada correctamente.');
    }
}
