<?php

namespace App\Http\Controllers;

use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnidadMedidaController extends Controller
{
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

        UnidadMedida::create($datos);

        return redirect()
            ->route('unidades-medida.index')
            ->with('success', 'Unidad de medida creada correctamente.');
    }

    public function edit(string $id)
    {
        $unidadMedida = UnidadMedida::findOrFail($id);

        return view('unidades-medida.edit', compact('unidadMedida'));
    }

    public function update(Request $request, string $id)
    {
        $unidadMedida = UnidadMedida::findOrFail($id);

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

        $unidadMedida->update($datos);

        return redirect()
            ->route('unidades-medida.index')
            ->with('success', 'Unidad de medida actualizada correctamente.');
    }

    public function destroy(string $id)
    {
        $unidadMedida = UnidadMedida::findOrFail($id);

        $unidadMedida->update([
            'activo' => false,
        ]);

        return redirect()
            ->route('unidades-medida.index')
            ->with('success', 'Unidad de medida desactivada correctamente.');
    }
}
