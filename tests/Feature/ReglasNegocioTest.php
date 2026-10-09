<?php

use App\Models\Articulo;
use App\Models\Movimiento;
use App\Models\Persona;
use App\Models\User;

/**
 * Reglas de negocio de inventario.
 *
 * Un artículo desactivado queda fuera de operación: no puede recibir nuevos
 * movimientos (entradas, salidas ni ajustes). Del mismo modo, una persona
 * desactivada no puede recibir salidas.
 */
test('un artículo activo puede recibir una entrada', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('movimientos.store'), [
            'articulo_id' => Articulo::factory()->create()->id,
            'cantidad' => 5,
            'fecha_movimiento' => now()->toDateString(),
        ])
        ->assertSessionHasNoErrors();

    expect(Movimiento::where('tipo', 'ENTRADA')->count())->toBe(1);
});

test('un artículo inactivo no puede recibir entradas', function () {
    $articulo = Articulo::factory()->create(['activo' => false]);

    $this->actingAs(User::factory()->create())
        ->post(route('movimientos.store'), [
            'articulo_id' => $articulo->id,
            'cantidad' => 5,
            'fecha_movimiento' => now()->toDateString(),
        ])
        ->assertSessionHasErrors('articulo_id');

    expect(Movimiento::count())->toBe(0);
});

test('un artículo inactivo no puede recibir salidas', function () {
    $articulo = Articulo::factory()->create(['activo' => false]);
    $persona = Persona::create(['nombre' => 'Ana', 'apellido' => 'Soto']);

    $this->actingAs(User::factory()->create())
        ->post(route('movimientos.salida.store'), [
            'articulo_id' => $articulo->id,
            'persona_id' => $persona->id,
            'cantidad' => 1,
            'fecha_movimiento' => now()->toDateString(),
        ])
        ->assertSessionHasErrors('articulo_id');

    expect(Movimiento::count())->toBe(0);
});

test('un artículo inactivo no puede recibir ajustes', function () {
    $articulo = Articulo::factory()->create(['activo' => false]);

    $this->actingAs(User::factory()->create())
        ->post(route('movimientos.ajuste.store'), [
            'articulo_id' => $articulo->id,
            'tipo' => 'AJUSTE_POSITIVO',
            'cantidad' => 2,
            'fecha_movimiento' => now()->toDateString(),
        ])
        ->assertSessionHasErrors('articulo_id');

    expect(Movimiento::count())->toBe(0);
});

test('una persona inactiva no puede recibir salidas', function () {
    $articulo = Articulo::factory()->create();
    $persona = Persona::create(['nombre' => 'Ana', 'apellido' => 'Soto', 'activo' => false]);

    $this->actingAs(User::factory()->create())
        ->post(route('movimientos.salida.store'), [
            'articulo_id' => $articulo->id,
            'persona_id' => $persona->id,
            'cantidad' => 1,
            'fecha_movimiento' => now()->toDateString(),
        ])
        ->assertSessionHasErrors('persona_id');

    expect(Movimiento::count())->toBe(0);
});
