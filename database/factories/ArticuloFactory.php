<?php

namespace Database\Factories;

use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\UnidadMedida;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Articulo>
 */
class ArticuloFactory extends Factory
{
    protected $model = Articulo::class;

    public function definition(): array
    {
        return [
            'codigo' => Str::upper(Str::random(3)).'-'.fake()->unique()->numberBetween(1000, 9999),
            'nombre' => fake()->words(3, true),
            'descripcion' => fake()->sentence(),
            'categoria_id' => Categoria::factory(),
            'unidad_medida_id' => UnidadMedida::factory(),
            'stock_minimo' => 5,
            'control_individual' => false,
            'activo' => true,
        ];
    }
}
