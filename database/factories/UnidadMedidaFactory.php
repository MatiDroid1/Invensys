<?php

namespace Database\Factories;

use App\Models\UnidadMedida;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * @extends Factory<UnidadMedida>
 */
class UnidadMedidaFactory extends Factory
{
    protected $model = UnidadMedida::class;

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
            'nombre' => fake()->unique()->word(),
            'abreviatura' => Str::substr(fake()->unique()->word(), 0, 3),
            'activo' => true,
            'organizacion_id' => $organizacionId,
        ];
    }
}
