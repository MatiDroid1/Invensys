<?php

use App\Models\Articulo;
use App\Models\Movimiento;
use App\Models\Persona;
use App\Models\User;
use App\Services\MovimientoService;

/*
 * Valorización de inventario por costo promedio ponderado.
 *
 * Regla de negocio: el costo del stock es el promedio ponderado de las
 * entradas y ajustes positivos. Cada movimiento congela el costo vigente
 * (`movimientos.costo_unitario`) para que el histórico no se mueva cuando el
 * promedio cambia. Todo es opcional: un artículo sin costo queda "sin
 * valorizar".
 */

test('una entrada con costo siembra el costo promedio', function () {
    $articulo = Articulo::factory()->create();
    $usuario = User::factory()->create();

    app(MovimientoService::class)->registrarEntrada(
        articuloId: $articulo->id,
        cantidad: 10.0,
        fechaMovimiento: now()->toDateTimeString(),
        costoUnitario: 1500.0,
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    );

    $articulo->refresh();

    expect((float) $articulo->costo_promedio)->toBe(1500.0)
        ->and($articulo->valor_inventario)->toBe(15000.0);
});

test('el costo promedio pondera entradas de distinto precio', function () {
    $articulo = Articulo::factory()->create();
    $usuario = User::factory()->create();
    $servicio = app(MovimientoService::class);

    // 10 unidades a 1000 y 30 unidades a 2000 = (10.000 + 60.000) / 40 = 1750.
    $servicio->registrarEntrada(
        articuloId: $articulo->id,
        cantidad: 10.0,
        fechaMovimiento: now()->toDateTimeString(),
        costoUnitario: 1000.0,
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    );

    $servicio->registrarEntrada(
        articuloId: $articulo->id,
        cantidad: 30.0,
        fechaMovimiento: now()->toDateTimeString(),
        costoUnitario: 2000.0,
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    );

    expect((float) $articulo->fresh()->costo_promedio)->toBe(1750.0);
});

test('una salida congela el costo vigente y no altera el promedio', function () {
    $articulo = Articulo::factory()->create();
    $usuario = User::factory()->create();
    $persona = Persona::create(['nombre' => 'Ana', 'apellido' => 'Soto']);
    $servicio = app(MovimientoService::class);

    $servicio->registrarEntrada(
        articuloId: $articulo->id,
        cantidad: 10.0,
        fechaMovimiento: now()->toDateTimeString(),
        costoUnitario: 1000.0,
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    );

    $servicio->registrarSalida(
        articuloId: $articulo->id,
        personaId: $persona->id,
        cantidad: 4.0,
        fechaMovimiento: now()->toDateTimeString(),
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    );

    $salida = Movimiento::where('tipo', 'SALIDA')->first();

    expect((float) $salida->costo_unitario)->toBe(1000.0)
        ->and($salida->valor)->toBe(4000.0)
        ->and((float) $articulo->fresh()->costo_promedio)->toBe(1000.0);
});

test('un ajuste positivo con costo recalcula el promedio', function () {
    $articulo = Articulo::factory()->create();
    $usuario = User::factory()->create();
    $servicio = app(MovimientoService::class);

    $servicio->registrarEntrada(
        articuloId: $articulo->id,
        cantidad: 10.0,
        fechaMovimiento: now()->toDateTimeString(),
        costoUnitario: 1000.0,
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    );

    // 10 a 1000 + 10 a 3000 = 40.000 / 20 = 2000.
    $servicio->registrarAjuste(
        articuloId: $articulo->id,
        tipo: 'AJUSTE_POSITIVO',
        cantidad: 10.0,
        fechaMovimiento: now()->toDateTimeString(),
        costoUnitario: 3000.0,
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    );

    expect((float) $articulo->fresh()->costo_promedio)->toBe(2000.0);
});

test('un ajuste negativo no cambia el costo promedio', function () {
    $articulo = Articulo::factory()->create();
    $usuario = User::factory()->create();
    $servicio = app(MovimientoService::class);

    $servicio->registrarEntrada(
        articuloId: $articulo->id,
        cantidad: 10.0,
        fechaMovimiento: now()->toDateTimeString(),
        costoUnitario: 1000.0,
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    );

    $servicio->registrarAjuste(
        articuloId: $articulo->id,
        tipo: 'AJUSTE_NEGATIVO',
        cantidad: 5.0,
        fechaMovimiento: now()->toDateTimeString(),
        costoUnitario: 9999.0,
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    );

    expect((float) $articulo->fresh()->costo_promedio)->toBe(1000.0);
});

test('un artículo sin costos queda sin valorizar', function () {
    $articulo = Articulo::factory()->create();
    $usuario = User::factory()->create();

    app(MovimientoService::class)->registrarEntrada(
        articuloId: $articulo->id,
        cantidad: 10.0,
        fechaMovimiento: now()->toDateTimeString(),
        costoUnitario: null,
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    );

    $articulo->refresh();

    expect($articulo->costo_promedio)->toBeNull()
        ->and($articulo->valor_inventario)->toBeNull();

    $entrada = Movimiento::where('tipo', 'ENTRADA')->first();

    expect($entrada->costo_unitario)->toBeNull()
        ->and($entrada->valor)->toBeNull();
});

test('el reporte de stock incluye la valorización', function () {
    $usuario = User::factory()->create();
    $articulo = Articulo::factory()->create(['precio_venta' => 2500]);

    app(MovimientoService::class)->registrarEntrada(
        articuloId: $articulo->id,
        cantidad: 10.0,
        fechaMovimiento: now()->toDateTimeString(),
        costoUnitario: 1000.0,
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    );

    $this->actingAs($usuario)
        ->get(route('reportes.stock'))
        ->assertOk()
        ->assertSee('Valor total del inventario')
        ->assertSee('10.000');

    $this->actingAs($usuario)
        ->get(route('reportes.stock.csv'))
        ->assertOk();
});

test('el margen combina costo promedio y precio de venta', function () {
    $articulo = Articulo::factory()->create(['precio_venta' => 1500]);
    $usuario = User::factory()->create();

    app(MovimientoService::class)->registrarEntrada(
        articuloId: $articulo->id,
        cantidad: 10.0,
        fechaMovimiento: now()->toDateTimeString(),
        costoUnitario: 1000.0,
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    );

    $articulo->refresh();

    // 1500 − 1000 = 500, que sobre 1500 es 33,33 %.
    expect($articulo->margen)->toBe(500.0)
        ->and($articulo->margen_porcentaje)->toBe(33.33)
        ->and($articulo->valor_venta)->toBe(15000.0);
});

test('sin costo o sin precio el margen queda sin calcular', function () {
    $sinCosto = Articulo::factory()->create(['precio_venta' => 1500]);
    $sinPrecio = Articulo::factory()->create(['precio_venta' => null]);
    $usuario = User::factory()->create();

    app(MovimientoService::class)->registrarEntrada(
        articuloId: $sinPrecio->id,
        cantidad: 5.0,
        fechaMovimiento: now()->toDateTimeString(),
        costoUnitario: 800.0,
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    );

    expect($sinCosto->fresh()->margen)->toBeNull()
        ->and($sinPrecio->fresh()->margen)->toBeNull();
});

test('el reporte de stock muestra el margen', function () {
    $usuario = User::factory()->create();
    $articulo = Articulo::factory()->create(['precio_venta' => 1500]);

    app(MovimientoService::class)->registrarEntrada(
        articuloId: $articulo->id,
        cantidad: 10.0,
        fechaMovimiento: now()->toDateTimeString(),
        costoUnitario: 1000.0,
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    );

    $this->actingAs($usuario)
        ->get(route('reportes.stock'))
        ->assertOk()
        ->assertSee('Margen')
        ->assertSee('33,3%');
});

test('la exportación de inventario valorizado totaliza y excluye lo no valorizado', function () {
    $usuario = User::factory()->create();

    $valorizado = Articulo::factory()->create(['nombre' => 'Con costo']);
    $sinCosto = Articulo::factory()->create(['nombre' => 'Sin costo']);
    $sinStock = Articulo::factory()->create(['nombre' => 'Con costo pero sin stock']);

    $servicio = app(MovimientoService::class);

    foreach ([[$valorizado, 4.0], [$sinCosto, 3.0], [$sinStock, 2.0]] as [$articulo, $cantidad]) {
        $servicio->registrarEntrada(
            articuloId: $articulo->id,
            cantidad: $cantidad,
            fechaMovimiento: now()->toDateTimeString(),
            costoUnitario: $articulo->is($sinCosto) ? null : 1000.0,
            referencia: null,
            observaciones: null,
            usuarioId: $usuario->id,
        );
    }

    $respuesta = $this->actingAs($usuario)
        ->get(route('reportes.inventario-valorizado.csv'))
        ->assertOk();

    $contenido = mb_convert_encoding(
        substr($respuesta->getContent(), 2),
        'UTF-8',
        'UTF-16LE'
    );

    // Solo el primero tiene stock y costo: 4 × 1000 = 4000.
    expect($contenido)->toContain('Con costo')
        ->and($contenido)->toContain('TOTAL')
        ->and($contenido)->toContain('4.000')
        ->and($contenido)->not->toContain('Sin costo');
});
