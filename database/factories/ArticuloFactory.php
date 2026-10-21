<?php

namespace Database\Factories;

use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\UnidadMedida;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * @extends Factory<Articulo>
 */
class ArticuloFactory extends Factory
{
    protected $model = Articulo::class;

    public function definition(): array
    {
        $organizacionId = null;

        try {
            if (DB::getSchemaBuilder()->hasTable('organizaciones')) {
                $organizacionId = DB::table('organizaciones')->orderBy('id')->value('id');
            }
        } catch (\Throwable $e) {
        }

        if (! $organizacionId) {
            try {
                DB::statement('PRAGMA foreign_keys = OFF;');
                $organizacionId = DB::table('organizaciones')->insertGetId([
                    'nombre' => 'Organización Principal',
                    'slug' => 'principal',
                    'perfil' => 'GENERAL',
                    'activo' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                DB::statement('PRAGMA foreign_keys = ON;');
            } catch (\Throwable $e) {
                try {
                    DB::statement('PRAGMA foreign_keys = ON;');
                } catch (\Throwable $ex) {
                }
            }
        }

        return [
            'codigo' => Str::upper(Str::random(3)).'-'.fake()->unique()->numberBetween(1000, 9999),
            'nombre' => fake()->words(3, true),
            'descripcion' => fake()->sentence(),
            'categoria_id' => Categoria::factory(),
            'unidad_medida_id' => UnidadMedida::factory(),
            'stock_minimo' => 5,
            'control_individual' => false,
            'activo' => true,
            'organizacion_id' => $organizacionId,
        ];
    }
}
