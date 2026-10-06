<?php

use App\Models\Conversacion;
use App\Models\User;

/**
 * Endurecimiento de seguridad aplicado en la auditoría previa a producción.
 *
 * El objetivo de estas pruebas es que ninguna de estas defensas se pueda
 * desactivar sin que la suite se ponga roja.
 */
test('un usuario desactivado pierde el acceso en el request siguiente', function () {
    $usuario = User::factory()->create();

    $this->actingAs($usuario)->get(route('dashboard'))->assertOk();

    // Un administrador lo desactiva mientras sigue adentro.
    $usuario->forceFill(['activo' => false])->save();

    $this->actingAs($usuario)
        ->get(route('dashboard'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

test('un usuario desactivado no puede escribir movimientos', function () {
    $usuario = User::factory()->inactivo()->create();

    $this->actingAs($usuario)
        ->post(route('movimientos.store'), [
            'articulo_id' => 1,
            'cantidad' => 1,
        ])
        ->assertRedirect(route('login'));
});

test('un usuario normal sigue operando con normalidad', function () {
    $this->actingAs(User::factory()->create(['activo' => true]))
        ->get(route('dashboard'))
        ->assertOk();
});

test('el login tiene limite de intentos', function () {
    $usuario = User::factory()->create();

    // Se agotan los 5 intentos permitidos por minuto.
    foreach (range(1, 5) as $intento) {
        $this->post(route('login'), [
            'email' => $usuario->email,
            'password' => 'incorrecta-'.$intento,
        ]);
    }

    // El sexto ya no se procesa, aunque la contraseña sea correcta.
    $this->post(route('login'), [
        'email' => $usuario->email,
        'password' => 'password',
    ])->assertStatus(429);

    $this->assertGuest();
});

test('la recuperacion de contrasena tambien tiene limite', function () {
    $usuario = User::factory()->create();

    foreach (range(1, 3) as $intento) {
        $this->post(route('password.email'), ['email' => $usuario->email]);
    }

    $this->post(route('password.email'), ['email' => $usuario->email])
        ->assertStatus(429);
});

test('el envio de mensajes tiene limite por minuto', function () {
    [$ana, $beto] = [
        User::factory()->create(),
        User::factory()->create(),
    ];

    $conversacion = Conversacion::create([
        'usuario_emisor_id' => $ana->id,
        'usuario_receptor_id' => $beto->id,
    ]);

    // 30 permitidos por minuto; el 31º se rechaza.
    foreach (range(1, 30) as $intento) {
        $this->actingAs($beto)
            ->postJson(route('mensajes.enviar', $conversacion), ['cuerpo' => 'Mensaje '.$intento])
            ->assertSuccessful();
    }

    $this->actingAs($beto)
        ->postJson(route('mensajes.enviar', $conversacion), ['cuerpo' => 'Excedido'])
        ->assertStatus(429);
});

test('el seeder del administrador se niega a crear una clave debil', function () {
    putenv('ADMIN_PASSWORD=password');
    $_ENV['ADMIN_PASSWORD'] = 'password';

    try {
        $this->expectException(RuntimeException::class);

        // El seeder lanza, en vez de crear una cuenta con una clave adivinable.
        $this->artisan('db:seed', ['--class' => 'UsuarioAdminSeeder'])->run();
    } finally {
        putenv('ADMIN_PASSWORD');
        unset($_ENV['ADMIN_PASSWORD']);
    }
});

test('el seeder del administrador exige que se defina ADMIN_PASSWORD', function () {
    putenv('ADMIN_PASSWORD');
    unset($_ENV['ADMIN_PASSWORD']);

    try {
        $this->expectException(RuntimeException::class);

        $this->artisan('db:seed', ['--class' => 'UsuarioAdminSeeder'])->run();
    } finally {
        putenv('ADMIN_PASSWORD');
        unset($_ENV['ADMIN_PASSWORD']);
    }
});

test('el archivo htaccess bloquea el acceso web a archivos sensibles', function () {
    $contenido = file_get_contents(base_path('.htaccess'));

    // Sin esto, /.env se descarga como texto plano con APP_KEY y la
    // contraseña de la base de datos.
    expect($contenido)
        ->toContain('.env')
        ->toContain('app|bootstrap|database|routes|storage|tests|vendor')
        ->toContain('[F,L]');
});

test('el archivo htaccess no es ignorado por git', function () {
    // Si quedara fuera del repositorio, el servidor nuevo quedaría expuesto.
    $salida = shell_exec('cd '.escapeshellarg(base_path()).' && git check-ignore .htaccess');

    expect(trim((string) $salida))->toBe('');
});
