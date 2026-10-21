<?php

use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\UnidadMedida;
use App\Models\User;
use App\Support\Limites;

/*
 * Límites numéricos.
 *
 * Las columnas del inventario son decimal(12,2) y decimal(12,4). Si un valor
 * las desborda, la base de datos lanza un error y el usuario ve una pantalla
 * de error 500. Con estos topes, ve un mensaje de validación.
 */

test('una cantidad por encima del máximo se rechaza con un mensaje claro', function () {
    $usuario = User::factory()->create();
    $articulo = Articulo::factory()->create();

    $this->actingAs($usuario)
        ->post(route('movimientos.store'), [
            'articulo_id' => $articulo->id,
            'cantidad' => 99999999999,
            'fecha_movimiento' => now()->toDateString(),
        ])
        ->assertSessionHasErrors('cantidad');

    expect($articulo->movimientos()->count())->toBe(0);
});

test('una cantidad con más de dos decimales se rechaza', function () {
    $usuario = User::factory()->create();
    $articulo = Articulo::factory()->create();

    $this->actingAs($usuario)
        ->post(route('movimientos.store'), [
            'articulo_id' => $articulo->id,
            'cantidad' => '1.005',
            'fecha_movimiento' => now()->toDateString(),
        ])
        ->assertSessionHasErrors('cantidad');
});

test('un stock mínimo por encima del máximo se rechaza', function () {
    $usuario = User::factory()->create();
    $categoria = Categoria::factory()->create();
    $unidad = UnidadMedida::factory()->create();

    $this->actingAs($usuario)
        ->post(route('articulos.store'), [
            'codigo' => 'LIM-001',
            'nombre' => 'Artículo con stock imposible',
            'categoria_id' => $categoria->id,
            'unidad_medida_id' => $unidad->id,
            'stock_minimo' => 99999999999,
        ])
        ->assertSessionHasErrors('stock_minimo');

    $this->assertDatabaseMissing('articulos', ['codigo' => 'LIM-001']);
});

test('un precio de venta negativo se rechaza', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $categoria = Categoria::factory()->create();
    $unidad = UnidadMedida::factory()->create();

    $this->actingAs($admin)
        ->post(route('articulos.store'), [
            'codigo' => 'PRE-001',
            'nombre' => 'Artículo con precio negativo',
            'categoria_id' => $categoria->id,
            'unidad_medida_id' => $unidad->id,
            'stock_minimo' => 1,
            'precio_venta' => -5,
        ])
        ->assertSessionHasErrors('precio_venta');
});

test('un costo unitario con más de cuatro decimales se rechaza', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $articulo = Articulo::factory()->create();

    $this->actingAs($admin)
        ->post(route('movimientos.store'), [
            'articulo_id' => $articulo->id,
            'cantidad' => 1,
            'fecha_movimiento' => now()->toDateString(),
            'costo_unitario' => '10.12345',
        ])
        ->assertSessionHasErrors('costo_unitario');
});

test('los límites coinciden con el tamaño de las columnas', function () {
    expect((float) Limites::CANTIDAD_MAX)->toBe(9999999999.99)
        ->and((float) Limites::COSTO_MAX)->toBe(99999999.9999)
        ->and((float) Limites::PRECIO_MAX)->toBe(9999999999.99)
        ->and((float) Limites::STOCK_MINIMO_MAX)->toBe(9999999999.99);
});
