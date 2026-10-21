<?php

namespace App\Http\Controllers;

use App\Models\UnidadMedida;
use App\Services\AuditoriaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UnidadMedidaController extends Controller
{
    public function __construct(
        private AuditoriaService $auditoriaService
    ) {}

    public function index()
    {
        $unidadesMedida = UnidadMedida::orderBy('nombre')->get();

        return view('unidades-medida.index', compact('unidadesMedida'));
    }

    public function create()
    {
        return view('unidades-medida.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:50',
                'unique:unidad_medidas,nombre',
            ],
            'abreviatura' => [
                'nullable',
                'string',
                'max:10',
            ],
        ]);

        DB::transaction(function () use ($datos, $request) {
            $unidadMedida = UnidadMedida::create($datos);

            $this->auditoriaService->registrar(
                modulo: 'unidades-medida',
                accion: 'CREAR',
                modelo: $unidadMedida,
                descripcion: 'Unidad de medida creada.',
                datos: $datos,
                usuarioId: $request->user()->id,
            );
        });

        return redirect()
            ->route('unidades-medida.index')
            ->with('success', 'Unidad de medida creada correctamente.');
    }

    public function edit(UnidadMedida $unidadMedida)
    {
        return view('unidades-medida.edit', compact('unidadMedida'));
    }

    public function update(Request $request, UnidadMedida $unidadMedida)
    {
        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:50',
                Rule::unique('unidad_medidas', 'nombre')
                    ->ignore($unidadMedida->id),
            ],
            'abreviatura' => [
                'nullable',
                'string',
                'max:10',
            ],
            'activo' => [
                'required',
                'boolean',
            ],
        ]);

        $antes = $unidadMedida->only([
            'nombre',
            'abreviatura',
            'activo',
        ]);

        DB::transaction(function () use ($unidadMedida, $datos, $antes, $request) {
            $unidadMedida->update($datos);

            $cambios = $unidadMedida->getChanges();

            unset($cambios['updated_at']);

            $this->auditoriaService->registrar(
                modulo: 'unidades-medida',
                accion: 'ACTUALIZAR',
                modelo: $unidadMedida,
                descripcion: 'Unidad de medida actualizada.',
                datos: [
                    'antes' => $antes,
                    'cambios' => $cambios,
                ],
                usuarioId: $request->user()->id,
            );
        });

        return redirect()
            ->route('unidades-medida.index')
            ->with('success', 'Unidad de medida actualizada correctamente.');
    }

    public function destroy(UnidadMedida $unidadMedida, Request $request)
    {
        DB::transaction(function () use ($unidadMedida, $request) {
            $unidadMedida->update([
                'activo' => false,
            ]);

            $this->auditoriaService->registrar(
                modulo: 'unidades-medida',
                accion: 'DESACTIVAR',
                modelo: $unidadMedida,
                descripcion: 'Unidad de medida desactivada.',
                datos: [
                    'activo' => false,
                ],
                usuarioId: $request->user()->id,
            );
        });

        return redirect()
            ->route('unidades-medida.index')
            ->with('success', 'Unidad de medida desactivada correctamente.');
    }
}
