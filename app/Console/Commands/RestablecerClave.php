<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Restablece la contraseña de un usuario desde la terminal.
 *
 * Existe porque Invensys no tiene servidor de correo configurado: el flujo de
 * "olvidé mi contraseña" no puede enviar nada, así que si alguien pierde su
 * clave no hay forma de recuperarla desde la interfaz.
 *
 * A diferencia del correo, esto no queda expuesto en la web y por lo tanto
 * tampoco se puede usar para forzar un cambio de contraseña ajeno.
 *   php artisan usuario:clave PERSONA@gmail.com
 *   php artisan usuario:clave admin@invensys.cl --clave=AlgoMuySeguro123
 */
class RestablecerClave extends Command
{
    protected $signature = 'usuario:clave
                            {email : Correo del usuario}
                            {--clave= : Nueva contraseña. Si se omite, se pregunta de forma oculta}
                            {--todo : Aplica el cambio aunque el usuario esté desactivado}';

    protected $description = 'Restablece la contraseña de un usuario y cierra sus sesiones abiertas';

    public function handle(): int
    {
        $email = Str::lower(trim((string) $this->argument('email')));

        $usuario = User::query()->where('email', $email)->first();

        if (! $usuario) {
            $this->error("No existe ningún usuario con el correo {$email}.");

            return self::FAILURE;
        }

        if (! $usuario->activo && ! $this->option('todo')) {
            $this->error("El usuario {$usuario->name} está desactivado.");
            $this->line('  Si igual quieres cambiarle la clave, agrega --todo.');

            return self::FAILURE;
        }

        $this->components->info("Usuario: {$usuario->name} <{$usuario->email}> ({$usuario->rol})");

        $clave = $this->pedirClave();

        if ($clave === null) {
            return self::FAILURE;
        }

        $problema = $this->validarClave($clave);

        if ($problema !== null) {
            $this->error($problema);

            return self::FAILURE;
        }

        // La confirmación solo tiene sentido cuando hay alguien delante: si la
        // contraseña viene por `--clave` se está ejecutando desde un script y
        // `confirm()` se quedaría esperando una respuesta que no existe.
        if ($this->esInteractivo() && ! $this->confirm('¿Cambiar la contraseña de '.$usuario->name.'?')) {
            $this->line('Cancelado. No se cambió nada.');

            return self::SUCCESS;
        }

        $this->aplicar($usuario, $clave);

        return self::SUCCESS;
    }

    /**
     * Si el comando se está escribiendo a mano, y no invocado desde un script.
     */
    private function esInteractivo(): bool
    {
        return $this->input->isInteractive() && $this->option('clave') === null;
    }

    /**
     * Pregunta la contraseña de forma oculta. `--clave` existe para poder
     * ejecutarla desde scripts sin interacción.
     */
    private function pedirClave(): ?string
    {
        $clave = $this->option('clave');

        if ($clave !== null && $clave !== '') {
            return (string) $clave;
        }

        if (! $this->input->isInteractive()) {
            $this->error('Falta la contraseña. Usa --clave=... cuando no hay terminal interactiva.');

            return null;
        }

        return $this->secret('Nueva contraseña (mínimo 8 caracteres)');
    }

    /**
     * Devuelve el motivo por el que la contraseña no sirve, o null si es
     * válida.
     */
    private function validarClave(string $clave): ?string
    {
        if (mb_strlen($clave) < 8) {
            return 'La contraseña debe tener al menos 8 caracteres.';
        }

        if (mb_strlen($clave) > 72) {
            return 'La contraseña es demasiado larga (máximo 72 caracteres).';
        }

        return null;
    }

    private function aplicar(User $usuario, string $clave): void
    {
        $sesiones = DB::table('sessions')->where('user_id', $usuario->id)->count();

        DB::transaction(function () use ($usuario, $clave) {
            $usuario->forceFill([
                'password' => Hash::make($clave),
            ])->save();

            // Cerrar las sesiones abiertas es lo más importante: si la
            // contraseña se cambia porque se filtró, la sesión de quien la
            // 获得 sea válida tiene que morir aquí.
            DB::table('sessions')->where('user_id', $usuario->id)->delete();

            // Los tokens de recuperación quedan sin efecto, para que un link
            // de correo antiguo no sirva para volver a tomar la cuenta.
            DB::table('password_reset_tokens')
                ->where('email', $usuario->email)
                ->delete();
        });

        $this->components->info('Contraseña actualizada.');

        if ($sesiones > 0) {
            $this->components->info("Se cerraron {$sesiones} sesión(es) abierta(s).");
        } else {
            $this->line('  No tenía sesiones abiertas.');
        }

        $this->line('');
        $this->line('  Comunica la contraseña por un canal seguro y pide que la cambie al entrar.');
    }
}
