<?php

use App\Models\User;

/**
 * Tema claro/oscuro y página "Acerca de".
 *
 * El tema no es solo decoración: antes de activarlo, Tailwind usaba el modo
 * `media`, es decir el tema dependía del sistema operativo y no del usuario.
 */
test('el script anti-parpadeo se incluye en todas las paginas', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertSee('invensys.tema', false);
});

// Va aparte porque `actingAs` mantiene la sesión para el resto del test: si se
// pidiera /login con sesión activa, el middleware `guest` redirige y la
// comprobación compararía contra un redirect en vez del formulario.
test('el script anti-parpadeo también está en las páginas de acceso', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('invensys.tema', false);

    $this->get(route('password.request'))
        ->assertOk()
        ->assertSee('invensys.tema', false);
});

test('la barra de navegacion ofrece el interruptor de tema', function () {
    $usuario = User::factory()->create();

    $this->actingAs($usuario)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Cambiar a modo oscuro')
        ->assertSee('Cambiar a modo claro');
});

test('tailwind usa el modo oscuro por clase y no por preferencia del sistema', function () {
    $config = file_get_contents(base_path('tailwind.config.js'));

    // Con 'media' el tema lo manda el sistema operativo y el botón no
    // serviría de nada.
    expect($config)->toContain("darkMode: 'class'");
});

test('la pagina acerca de explica el proposito del proyecto', function () {
    $usuario = User::factory()->create();

    $this->actingAs($usuario)
        ->get(route('acerca'))
        ->assertOk()
        ->assertSee('Acerca de Invensys')
        ->assertSee('control de inventario')
        ->assertSee('Trazabilidad')
        ->assertSee('kardex')
        // Es importante decir lo que el sistema NO es, para evitar que
        // alguien lo use como si fuera un sistema contable.
        ->assertSee('No es un sistema contable');
});

test('acerca de la barra de navegacion es visible para cualquier usuario', function () {
    $this->actingAs(User::factory()->create(['rol' => 'usuario']))
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Acerca de');

    $this->actingAs(User::factory()->create(['rol' => 'admin']))
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Acerca de');
});

test('la pagina acerca de exige sesion iniciada', function () {
    $this->get(route('acerca'))->assertRedirect(route('login'));
});
