<?php

use App\Models\Articulo;
use App\Models\Movimiento;
use App\Models\Persona;
use App\Models\User;
use App\Services\MovimientoService;

/*
 * Idempotencia de los movimientos.
 *
 * Un doble clic, un botón que se queda pulsado o un reintento del navegador
 * no pueden dejar dos entradas donde solo había una. La clave la genera el
 * formulario y viaja con la petición.
 */

test('el formulario de entrada incluye una clave de idempotencia', function () {
    $usuario = User::factory()->create();

    $contenido = $this->actingAs($usuario)
        ->get(route('movimientos.create'))
        ->assertOk()
        ->getContent();

    expect(preg_replace('/\s+/', ' ', $contenido))
        ->toContain('name="idempotency_key"');
});

test('un doble envío con la misma clave registra un solo movimiento', function () {
    $usuario = User::factory()->create();
    $articulo = Articulo::factory()->create();

    $datos = [
        'articulo_id' => $articulo->id,
        'cantidad' => 5,
        'fecha_movimiento' => now()->toDateString(),
        'idempotency_key' => 'clave-de-prueba-1',
    ];

    $this->actingAs($usuario)->post(route('movimientos.store'), $datos);
    $this->actingAs($usuario)->post(route('movimientos.store'), $datos);

    expect(Movimiento::count())->toBe(1);

    $articulo->refresh();

    expect((float) $articulo->stock_actual)->toBe(5.0);
});

test('la clave de idempotencia queda guardada en el movimiento', function () {
    $usuario = User::factory()->create();
    $articulo = Articulo::factory()->create();

    $this->actingAs($usuario)->post(route('movimientos.store'), [
        'articulo_id' => $articulo->id,
        'cantidad' => 3,
        'fecha_movimiento' => now()->toDateString(),
        'idempotency_key' => 'clave-de-prueba-2',
    ]);

    $this->assertDatabaseHas('movimientos', [
        'idempotency_key' => 'clave-de-prueba-2',
    ]);
});

test('envíos con claves distintas registran movimientos distintos', function () {
    $usuario = User::factory()->create();
    $articulo = Articulo::factory()->create();

    foreach (['clave-a', 'clave-b'] as $clave) {
        $this->actingAs($usuario)->post(route('movimientos.store'), [
            'articulo_id' => $articulo->id,
            'cantidad' => 5,
            'fecha_movimiento' => now()->toDateString(),
            'idempotency_key' => $clave,
        ]);
    }

    expect(Movimiento::count())->toBe(2);
});

test('la salida también es idempotente', function () {
    $usuario = User::factory()->create();
    $articulo = Articulo::factory()->create();
    $persona = Persona::create(['nombre' => 'Ana', 'apellido' => 'Soto']);

    app(MovimientoService::class)->registrarEntrada(
        articuloId: $articulo->id,
        cantidad: 10.0,
        fechaMovimiento: now()->toDateTimeString(),
        costoUnitario: null,
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    );

    $datos = [
        'articulo_id' => $articulo->id,
        'persona_id' => $persona->id,
        'cantidad' => 4,
        'fecha_movimiento' => now()->toDateString(),
        'idempotency_key' => 'clave-salida-1',
    ];

    $this->actingAs($usuario)->post(route('movimientos.salida.store'), $datos);
    $this->actingAs($usuario)->post(route('movimientos.salida.store'), $datos);

    expect(Movimiento::where('tipo', 'SALIDA')->count())->toBe(1);

    $articulo->refresh();

    expect((float) $articulo->stock_actual)->toBe(6.0);
});

test('el servicio no duplica cuando recibe la misma clave', function () {
    $usuario = User::factory()->create();
    $articulo = Articulo::factory()->create();
    $servicio = app(MovimientoService::class);

    $primero = $servicio->registrarEntrada(
        articuloId: $articulo->id,
        cantidad: 5.0,
        fechaMovimiento: now()->toDateTimeString(),
        costoUnitario: null,
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
        idempotencyKey: 'clave-servicio',
    );

    $segundo = $servicio->registrarEntrada(
        articuloId: $articulo->id,
        cantidad: 5.0,
        fechaMovimiento: now()->toDateTimeString(),
        costoUnitario: null,
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
        idempotencyKey: 'clave-servicio',
    );

    expect($primero->id)->toBe($segundo->id)
        ->and(Movimiento::count())->toBe(1);
});
