<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            [
                'nombre' => 'Médico',
                'descripcion' => 'Artículos e insumos relacionados con atención médica.',
            ],
            [
                'nombre' => 'Limpieza',
                'descripcion' => 'Artículos y productos de limpieza.',
            ],
            [
                'nombre' => 'Informática',
                'descripcion' => 'Artículos y accesorios informáticos.',
            ],
            [
                'nombre' => 'Oficina',
                'descripcion' => 'Artículos de oficina y papelería.',
            ],
            [
                'nombre' => 'Mantención',
                'descripcion' => 'Artículos utilizados para mantenimiento y reparaciones.',
            ],
        ];

        foreach ($categorias as $categoria) {
            Categoria::firstOrCreate(
                ['nombre' => $categoria['nombre']],
                ['descripcion' => $categoria['descripcion']],
            );
        }
    }
}
