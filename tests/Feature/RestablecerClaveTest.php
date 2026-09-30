<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Cobertura del comando `usuario:clave`, que es la única vía real de
 * recuperación de contraseña mientras no haya servidor de correo.
 */
function crearSesion(User $usuario): void
{
    DB::table('sessions')->insert([
        'id' => 'sesion-de-prueba',
        'user_id' => $usuario->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Pest',
        'payload' => 'x',
        'last_activity' => now()->timestamp,
    ]);
}

test('restablece la contraseña del usuario', function () {
    $usuario = User::factory()->create(['email' => 'ana@example.com']);

    $this->artisan('usuario:clave', [
        'email' => 'ana@example.com',
        '--clave' => 'NuevaClave123',
    ])->assertSuccessful();

    expect(Hash::check('NuevaClave123', $usuario->fresh()->password))->toBeTrue();
});

test('no importa mayusculas ni espacios en el correo', function () {
    $usuario = User::factory()->create(['email' => 'beto@example.com']);

    $this->artisan('usuario:clave', [
        'email' => '  BETO@Example.com ',
        '--clave' => 'NuevaClave123',
    ])->assertSuccessful();

    expect(Hash::check('NuevaClave123', $usuario->fresh()->password))->toBeTrue();
});

test('cierra las sesiones abiertas del usuario', function () {
    $usuario = User::factory()->create();
    crearSesion($usuario);

    $this->artisan('usuario:clave', [
        'email' => $usuario->email,
        '--clave' => 'NuevaClave123',
    ])->assertSuccessful();

    // Es lo que evita que alguien con una sesión robada siga dentro después
    // de que se cambie la contraseña.
    expect(DB::table('sessions')->where('user_id', $usuario->id)->count())->toBe(0);
});

test('invalida los tokens de recuperacion pendientes', function () {
    $usuario = User::factory()->create();

    DB::table('password_reset_tokens')->insert([
        'email' => $usuario->email,
        'token' => 'token-antiguo',
        'created_at' => now(),
    ]);

    $this->artisan('usuario:clave', [
        'email' => $usuario->email,
        '--clave' => 'NuevaClave123',
    ])->assertSuccessful();

    expect(DB::table('password_reset_tokens')->where('email', $usuario->email)->count())->toBe(0);
});

test('falla si el correo no existe', function () {
    User::factory()->create();

    $this->artisan('usuario:clave', [
        'email' => 'nadie@example.com',
        '--clave' => 'NuevaClave123',
    ])->assertFailed();
});

test('rechaza contraseñas demasiado cortas', function () {
    $usuario = User::factory()->create();
    $original = $usuario->password;

    $this->artisan('usuario:clave', [
        'email' => $usuario->email,
        '--clave' => 'corta',
    ])->assertFailed();

    expect($usuario->fresh()->password)->toBe($original);
});

test('no toca usuarios desactivados salvo que se pida --todo', function () {
    $usuario = User::factory()->create(['activo' => false]);
    $original = $usuario->password;

    $this->artisan('usuario:clave', [
        'email' => $usuario->email,
        '--clave' => 'NuevaClave123',
    ])->assertFailed();

    expect($usuario->fresh()->password)->toBe($original);

    $this->artisan('usuario:clave', [
        'email' => $usuario->email,
        '--clave' => 'NuevaClave123',
        '--todo' => true,
    ])->assertSuccessful();

    expect(Hash::check('NuevaClave123', $usuario->fresh()->password))->toBeTrue();
});

test('la vista de recuperacion explica que no hay correo configurado', function () {
    config(['mail.default' => 'log']);

    User::factory()->create();

    $this->get(route('password.request'))
        ->assertOk()
        ->assertSee('no está disponible')
        ->assertDontSee('Enviar enlace de recuperación');
});

test('la vista de recuperacion vuelve a ofrecer el formulario con correo real', function () {
    config(['mail.default' => 'smtp']);

    $this->get(route('password.request'))
        ->assertOk()
        ->assertSee('Enviar enlace de recuperación')
        ->assertDontSee('no está disponible');
});
