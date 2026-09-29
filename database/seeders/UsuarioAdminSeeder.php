<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@invensys.cl');
        $password = env('ADMIN_PASSWORD', 'password');

        $usuario = User::firstOrNew(['email' => $email]);

        $usuario->fill([
            'name' => env('ADMIN_NAME', 'Administrador'),
            'rol' => 'admin',
            'activo' => true,
        ]);

        if (! $usuario->exists) {
            $usuario->email_verified_at = now();
            $usuario->password = Hash::make($password);
        }

        $usuario->save();

        $this->command?->info("Usuario administrador listo: {$email}");
    }
}
