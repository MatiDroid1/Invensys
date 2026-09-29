<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreArticuloRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
                'numeric',
                'min:0',
            ],

            'control_individual' => [
                'nullable',
                'boolean',
            ],
        ];
    }
}
