<?php

namespace App\Http\Requests;

use App\Enums\Permiso;
use App\Models\Articulo;
use App\Support\Limites;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreArticuloRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', Articulo::class);
    }

    public function rules(): array
    {
        return [
            'codigo' => [
                'required',
                'string',
                'max:50',
                'unique:articulos,codigo',
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
                Rule::exists('categorias', 'id')
                    ->where(fn ($query) => $query->where('activo', true)),
            ],

            'unidad_medida_id' => [
                'required',
                Rule::exists('unidad_medidas', 'id')
                    ->where(fn ($query) => $query->where('activo', true)),
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
        ];
    }

    /**
     * El precio de venta es un dato de costos: quien no puede administrarlos
     * no debería poder fijarlos ni al crear el artículo.
     */
    protected function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! $this->filled('precio_venta')) {
                return;
            }

            if (! $this->user()->puede(Permiso::CostosEditar)) {
                $validator->errors()->add(
                    'precio_venta',
                    'No tienes permiso para fijar el precio de venta.'
                );
            }
        });
    }
}
