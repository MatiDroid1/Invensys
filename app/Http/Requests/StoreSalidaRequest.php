<?php

namespace App\Http\Requests;

use App\Models\Movimiento;
use App\Support\Limites;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreSalidaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('registrarSalida', Movimiento::class);
    }

    public function rules(): array
    {
        return [
            'articulo_id' => [
                'required',
                Rule::exists('articulos', 'id')->where('activo', true),
            ],

            'persona_id' => [
                'required',
                Rule::exists('personas', 'id')->where('activo', true),
            ],

            'cantidad' => [
                'required',
                ...Limites::cantidad(),
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
                'max:1000',
            ],

            // La genera el formulario: identifica la operación para que un
            // doble envío no la registre dos veces.
            'idempotency_key' => [
                'nullable',
                'string',
                'max:64',
            ],
        ];
    }
}
