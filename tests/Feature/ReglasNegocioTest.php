<?php

use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\Movimiento;
use App\Models\Persona;
use App\Models\UnidadMedida;
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

test('un artículo se puede editar aunque su categoría esté desactivada', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $categoria = Categoria::factory()->create(['activo' => false]);
    $unidad = UnidadMedida::factory()->create();

    $articulo = Articulo::factory()->create([
        'categoria_id' => $categoria->id,
        'unidad_medida_id' => $unidad->id,
    ]);

    $this->actingAs($admin)
        ->put(route('articulos.update', $articulo), [
            'codigo' => $articulo->codigo,
            'nombre' => 'Nombre corregido',
            'categoria_id' => $categoria->id,
            'unidad_medida_id' => $unidad->id,
            'stock_minimo' => 2,
            'activo' => 1,
        ])
        ->assertSessionHasNoErrors();

    expect($articulo->refresh()->nombre)->toBe('Nombre corregido');
});

test('desactivar una persona no borra su historial', function () {
    $usuario = User::factory()->create();
    $articulo = Articulo::factory()->create();
    $persona = Persona::create(['nombre' => 'Ana', 'apellido' => 'Soto']);

    $this->actingAs($usuario)
        ->post(route('movimientos.store'), [
            'articulo_id' => $articulo->id,
            'cantidad' => 5,
            'fecha_movimiento' => now()->toDateString(),
        ]);

    $this->actingAs($usuario)
        ->post(route('movimientos.salida.store'), [
            'articulo_id' => $articulo->id,
            'persona_id' => $persona->id,
            'cantidad' => 1,
            'fecha_movimiento' => now()->toDateString(),
        ]);

    expect(Movimiento::where('persona_id', $persona->id)->count())->toBe(1);

    $this->actingAs($usuario)->delete(route('personas.destroy', $persona));

    expect($persona->refresh()->activo)->toBeFalse()
        ->and(Movimiento::where('persona_id', $persona->id)->count())->toBe(1);
});

test('el listado de personas ofrece la acción de desactivar', function () {
    $usuario = User::factory()->create();
    $persona = Persona::create(['nombre' => 'Ana', 'apellido' => 'Soto']);

    $this->actingAs($usuario)
        ->get(route('personas.index'))
        ->assertOk()
        ->assertSee(route('personas.destroy', $persona));
});
