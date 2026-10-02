<?php

use App\Models\User;

/**
 * El registro público está desactivado.
 *
 * Estas pruebas existen para que nadie reactive la ruta sin darse cuenta de
 * lo que significa: con ella abierta, cualquiera que conociera la URL podía
 * crear su propia cuenta activa y entrar a inventario, mensajería y reportes.
 *
 * Las cuentas se crean desde Administración (resources/views/usuarios).
 */
test('la pantalla de registro no existe', function () {
    $this->get('/register')->assertNotFound();
});

test('no se pueden crear cuentas por la web', function () {
    $this->post('/register', [
        'name' => 'Intruso',
        'email' => 'intruso@example.com',
        'password' => 'ClaveSegura123',
        'password_confirmation' => 'ClaveSegura123',
    ])->assertNotFound();

    expect(User::where('email', 'intruso@example.com')->exists())->toBeFalse();
});

test('la ruta register no aparece en el listado de rutas', function () {
    $rutas = collect(app('router')->getRoutes())->map(fn ($ruta) => $ruta->getName());

    expect($rutas->filter(fn ($nombre) => str_contains((string) $nombre, 'register')))->toBeEmpty();
});
