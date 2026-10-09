<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAjusteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'articulo_id' => [
                'required',
                Rule::exists('articulos', 'id')->where('activo', true),
            ],

            'tipo' => [
                'required',
                'in:AJUSTE_POSITIVO,AJUSTE_NEGATIVO',
            ],

            'cantidad' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'fecha_movimiento' => [
                'required',
                'date',
            ],

            'referencia' => [
                'nullable',
                'string',
                'max:100',
            ],

            'observaciones' => [
                'nullable',
                'string',
            ],
        ];
    }
}
