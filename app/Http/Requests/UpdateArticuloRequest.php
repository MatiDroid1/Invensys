<?php

namespace App\Http\Requests;

use App\Enums\Permiso;
use App\Models\Articulo;
use App\Support\Limites;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateArticuloRequest extends FormRequest
{
    public function authorize(): bool
    {
        $articulo = $this->route('articulo');

        if (! $articulo instanceof Articulo) {
            return false;
        }

        return Gate::allows('update', $articulo);
    }

    public function rules(): array
    {
        $articuloId = $this->route('articulo')->id;

        return [
            'codigo' => [
                'required',
                'string',
                'max:50',
                Rule::unique('articulos', 'codigo')
                    ->ignore($articuloId),
            ],

            'nombre' => [
                'required',
                'string',
                'max:150',
            ],

            'descripcion' => [
                'nullable',
                'string',
            ],

            'categoria_id' => [
                'required',
                // La categoría propia del artículo sigue siendo válida aunque
                // esté desactivada: si no, el artículo quedaría sin poder
                // editar y sin forma de cambiar de categoría.
                Rule::exists('categorias', 'id')
                    ->where(fn ($query) => $query
                        ->where('activo', true)
                        ->orWhere('id', $this->route('articulo')->categoria_id)),
            ],

            'unidad_medida_id' => [
                'required',
                Rule::exists('unidad_medidas', 'id')
                    ->where(fn ($query) => $query
                        ->where('activo', true)
                        ->orWhere('id', $this->route('articulo')->unidad_medida_id)),
            ],

            'stock_minimo' => [
                'required',
                ...Limites::stockMinimo(),
            ],

            'precio_venta' => [
                'nullable',
                ...Limites::precio(),
            ],

            'control_individual' => [
                'nullable',
                'boolean',
            ],

            'activo' => [
                'required',
                'boolean',
            ],
        ];
    }

    /**
     * El precio de venta solo puede cambiarlo quien administra los costos.
     *
     * Reenviar el mismo precio no es un cambio, así que se permite: de otro
     * modo un operador no podría editar siquiera el nombre de un artículo.
     */
    protected function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $articulo = $this->route('articulo');

            if (! $articulo instanceof Articulo) {
                return;
            }

            if (! $this->precioVentaCambia($articulo)) {
                return;
            }

            if (! $this->user()->puede(Permiso::CostosEditar)) {
                $validator->errors()->add(
                    'precio_venta',
                    'No tienes permiso para modificar el precio de venta.'
                );
            }
        });
    }

    private function precioVentaCambia(Articulo $articulo): bool
    {
        if (! $this->has('precio_venta')) {
            return false;
        }

        $actual = $articulo->precio_venta !== null
            ? (float) $articulo->precio_venta
            : null;

        $enviado = $this->input('precio_venta');

        if ($enviado === null || $enviado === '') {
            return $actual !== null;
        }

        if ($actual === null) {
            return true;
        }

        return abs((float) $enviado - $actual) > 0.000001;
    }
}
