<?php

namespace App\Http\Requests;

use App\Enums\Permiso;
use App\Models\Movimiento;
use App\Support\Limites;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreAjusteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('registrarAjuste', Movimiento::class);
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
                ...Limites::cantidad(),
            ],

            'costo_unitario' => [
                'nullable',
                ...Limites::costo(),
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

    /**
     * Un ajuste positivo con costo mueve el promedio ponderado del artículo,
     * así que fijar ese costo queda reservado a quien administra los costos.
     */
    protected function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! $this->filled('costo_unitario')) {
                return;
            }

            if (! $this->user()->puede(Permiso::CostosEditar)) {
                $validator->errors()->add(
                    'costo_unitario',
                    'No tienes permiso para fijar el costo unitario.'
                );
            }
        });
    }
}
