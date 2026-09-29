<?php

namespace Database\Seeders;

use App\Models\UnidadMedida;
use Illuminate\Database\Seeder;

class UnidadMedidaSeeder extends Seeder
{
    public function run(): void
    {
        $unidades = [
            [
                'nombre' => 'Unidad',
                'abreviatura' => 'un',
            ],
            [
                'nombre' => 'Caja',
                'abreviatura' => 'caja',
            ],
            [
                'nombre' => 'Paquete',
                'abreviatura' => 'paq',
            ],
            [
                'nombre' => 'Resma',
                'abreviatura' => 'resma',
            ],
            [
                'nombre' => 'Litro',
                'abreviatura' => 'L',
            ],
            [
                'nombre' => 'Kilogramo',
                'abreviatura' => 'kg',
            ],
            [
                'nombre' => 'Metro',
                'abreviatura' => 'm',
            ],
            [
                'nombre' => 'Rollo',
                'abreviatura' => 'rollo',
            ],
        ];

        foreach ($unidades as $unidad) {
            UnidadMedida::firstOrCreate(
                ['nombre' => $unidad['nombre']],
                ['abreviatura' => $unidad['abreviatura']],
            );
        }
    }
}
