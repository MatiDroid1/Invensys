<?php

use App\Models\User;

test('el diagnóstico muestra el estado de la infraestructura', function () {
    $admin = User::factory()->create(['rol' => 'admin']);

    $respuesta = $this->actingAs($admin)
        ->get(route('diagnostico'))
        ->assertOk();

    $respuesta->assertSee('Diagnóstico del sistema');
    $respuesta->assertSee('Base de datos');
    $respuesta->assertSee('Migraciones');
    $respuesta->assertSee('Assets compilados');
    $respuesta->assertSee('Correcto');
});

test('el diagnóstico es solo de administradores', function () {
    $usuario = User::factory()->create(['rol' => 'usuario']);

    $this->actingAs($usuario)
        ->get(route('diagnostico'))
        ->assertForbidden()
        ->assertSee('No tienes permiso')
        ->assertSee('403');
});

test('las rutas inexistentes muestran la página 404 personalizada', function () {
    $this->get('/ruta-que-no-existe')
        ->assertNotFound()
        ->assertSee('404')
        ->assertSee('No encontramos esa página')
        ->assertSee('Volver al panel');
});

test('la página de error 403 está personalizada', function () {
    $usuario = User::factory()->create(['rol' => 'usuario']);

    $this->actingAs($usuario)
        ->get('/usuarios')
        ->assertForbidden()
        ->assertSee('403')
        ->assertSee('No tienes permiso para ver esta página');
});
