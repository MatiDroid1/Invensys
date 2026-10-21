<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

/**
 * Crea la cuenta de administración inicial.
 *
 * Antes este seeder usaba `env('ADMIN_PASSWORD', 'password')`. Como nadie
 * definió esa variable, la cuenta admin quedó con la contraseña literal
 * `password`, que cualquiera podía adivinar. Ahora la variable es
 * obligatoria: si falta, el seeder se detiene en vez de crear una puerta
 * abierta.
 */
class UsuarioAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@invensys.cl');
        $password = env('ADMIN_PASSWORD');

        // Solo es obligatorio cuando la cuenta se crea de verdad. En una
        // reejecución sobre una base ya inicializada no tiene sentido exigir
        // una contraseña que nadie va a usar.
        if (($password === null || $password === '') && ! User::where('email', $email)->exists()) {
            throw new RuntimeException(
                'Falta definir ADMIN_PASSWORD en el archivo .env antes de sembrar datos. '
                ."Sin esa variable se crearía la cuenta {$email} con una contraseña adivinable. "
                .'Genera una con: php artisan usuario:clave '.$email
            );
        }

        $usuario = User::firstOrNew(['email' => $email]);

        $usuario->forceFill([
            'name' => env('ADMIN_NAME', 'Administrador'),
            'rol' => 'admin',
            'activo' => true,
        ]);

        if (! $usuario->exists) {
            $this->rechazarClaveInsegura((string) $password);

            $usuario->email_verified_at = now();
            $usuario->password = Hash::make((string) $password);
        } else {
            // La cuenta ya existía: no se toca su contraseña, pero sí se
            // comprueba que no sea una de las conocidas por estar publicadas
            // en la documentación. Es el loophole que dejó viva la cuenta con
            // contraseña "password".
            $this->advertirClaveInseguraExistente($usuario, $email);
        }

        $usuario->save();

        $this->command?->info("Usuario administrador listo: {$email}");
    }

    /**
     * Contraseñas que aparecen en la documentación o en ejemplos, y que por
     * tanto cualquiera puede adivinar.
     *
     * @return array<int, string>
     */
    private function clavesInseguras(): array
    {
        return ['password', 'admin', 'admin123', '12345678', 'invensys', 'secret'];
    }

    /**
     * Comprueba la contraseña actual de una cuenta que ya existe.
     *
     * No se puede deducir del hash, así que se prueba cada candidata conocida
     * contra el hash guardado.
     */
    private function advertirClaveInseguraExistente(User $usuario, string $email): void
    {
        foreach ($this->clavesInseguras() as $candidata) {
            if (! Hash::check($candidata, (string) $usuario->password)) {
                continue;
            }

            throw new RuntimeException(
                "La cuenta {$email} ya existe y su contraseña es de las conocidas. "
                .'Cámbiala antes de seguir con: php artisan usuario:clave '.$email
            );
        }
    }

    /**
     * Corta la pasada de las contraseñas que aparecen en la documentación y
     * que por tanto ya son conocidas.
     */
    private function rechazarClaveInsegura(string $password): void
    {
        if (mb_strlen($password) < 8) {
            throw new RuntimeException('ADMIN_PASSWORD debe tener al menos 8 caracteres.');
        }

        if (in_array(strtolower($password), $this->clavesInseguras(), true)) {
            throw new RuntimeException(
                'ADMIN_PASSWORD es demasiado obvia. Elige una que no se adivine.'
            );
        }
    }
}
