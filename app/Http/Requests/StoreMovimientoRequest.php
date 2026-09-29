<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovimientoRequest extends FormRequest
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
                'exists:articulos,id',
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
