<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizacionPrincipalSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('organizaciones')->exists()) {
            return;
        }

        $organizacionId = DB::table('organizaciones')->insertGetId([
            'nombre' => 'Organización Principal',
            'slug' => 'principal',
            'perfil' => 'GENERAL',
            'activo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $tablas = [
            'categorias',
            'unidad_medidas',
            'articulos',
            'personas',
            'movimientos',
            'contactos',
            'auditorias',
            'conversaciones',
            'mensajes',
        ];

        foreach ($tablas as $tabla) {
            if (! DB::getSchemaBuilder()->hasTable($tabla)) {
                continue;
            }

            if (! DB::getSchemaBuilder()->hasColumn($tabla, 'organizacion_id')) {
                continue;
            }

            DB::table($tabla)->whereNull('organizacion_id')->update([
                'organizacion_id' => $organizacionId,
            ]);
        }

        $usuarios = User::all();

        foreach ($usuarios as $usuario) {
            $rol = $usuario->rol === 'admin' ? 'propietario' : 'consulta';

            if (DB::table('organizacion_usuario')->where('user_id', $usuario->id)->exists()) {
                continue;
            }

            $yaHayPredeterminada = DB::table('organizacion_usuario')
                ->where('user_id', $usuario->id)
                ->where('predeterminada', true)
                ->exists();

            DB::table('organizacion_usuario')->insert([
                'organizacion_id' => $organizacionId,
                'user_id' => $usuario->id,
                'rol' => $rol,
                'predeterminada' => ! $yaHayPredeterminada,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
