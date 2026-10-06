<?php

use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\Movimiento;
use App\Models\User;

test('el panel y la barra de navegación avisan cuando hay stock bajo', function () {
    $usuario = User::factory()->create();
    $categoria = Categoria::factory()->create();

    $bajo = Articulo::factory()->create([
        'codigo' => 'BAJO-001',
        'categoria_id' => $categoria->id,
        'stock_minimo' => 10,
    ]);

    $suficiente = Articulo::factory()->create([
        'codigo' => 'OK-001',
        'categoria_id' => $categoria->id,
        'stock_minimo' => 2,
    ]);

    Movimiento::create([
        'articulo_id' => $bajo->id,
        'tipo' => 'SALIDA',
        'cantidad' => 10,
        'fecha_movimiento' => '2026-10-01 10:00:00',
        'usuario_id' => $usuario->id,
    ]);

    Movimiento::create([
        'articulo_id' => $suficiente->id,
        'tipo' => 'ENTRADA',
        'cantidad' => 5,
        'fecha_movimiento' => '2026-10-01 10:00:00',
        'usuario_id' => $usuario->id,
    ]);

    $respuesta = $this->actingAs($usuario)
        ->get(route('dashboard'))
        ->assertOk();

    $respuesta->assertSee('Alertas de stock');
    $respuesta->assertSee('BAJO-001');

    $html = $respuesta->getContent();
    preg_match('/Alertas de stock.*?Últimos movimientos/s', $html, $seccionAlertas);
    expect($seccionAlertas[0] ?? '')
        ->toContain('BAJO-001')
        ->not->toContain('OK-001');

    expect($html)
        ->toMatch('/title="Artículos con stock[^"]*"[^>]*>\s*1\s*</');
});

test('sin artículos en mínimo la barra de navegación no muestra el contador', function () {
    $usuario = User::factory()->create();
    $categoria = Categoria::factory()->create();

    $articulo = Articulo::factory()->create([
        'categoria_id' => $categoria->id,
        'stock_minimo' => 1,
    ]);

    Movimiento::create([
        'articulo_id' => $articulo->id,
        'tipo' => 'ENTRADA',
        'cantidad' => 50,
        'fecha_movimiento' => '2026-10-01 10:00:00',
        'usuario_id' => $usuario->id,
    ]);

    $respuesta = $this->actingAs($usuario)
        ->get(route('dashboard'))
        ->assertOk();

    $respuesta->assertSee('No hay artículos con stock igual o inferior al mínimo.');

    // El badge de la barra de navegación queda oculto sin alertas.
    expect($respuesta->getContent())
        ->toMatch('/title="Artículos con stock[^"]*"[^>]*class="[^"]*hidden[^"]*"\s*>\s*0\s*</')
        ->not->toMatch('/title="Artículos con stock[^"]*"[^>]*>\s*[1-9]\d*\s*</');
});
