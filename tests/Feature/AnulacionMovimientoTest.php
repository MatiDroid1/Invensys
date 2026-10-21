<?php

use App\Models\Articulo;
use App\Models\Movimiento;
use App\Models\Persona;
use App\Models\User;
use App\Services\MovimientoService;
use Illuminate\Validation\ValidationException;

/*
 * Anulación de movimientos.
 *
 * Un movimiento confirmado no se borra ni se edita: se anula. La anulación
 * genera un movimiento compensatorio, de modo que el historial cuadra sin
 * que nadie tenga que corregir el stock a mano.
 */

test('anular una entrada deja el stock en cero y genera el compensatorio', function () {
    $usuario = User::factory()->create();
    $articulo = Articulo::factory()->create();

    $servicio = app(MovimientoService::class);

    $entrada = $servicio->registrarEntrada(
        articuloId: $articulo->id,
        cantidad: 10.0,
        fechaMovimiento: now()->toDateTimeString(),
        costoUnitario: 1000.0,
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    );

    $compensatorio = $servicio->anularMovimiento(
        movimientoId: $entrada->id,
        motivo: 'Entrada duplicada por error.',
        usuarioId: $usuario->id,
    );

    expect($compensatorio->tipo)->toBe('SALIDA')
        ->and((float) $compensatorio->cantidad)->toBe(10.0)
        ->and($compensatorio->reversa_de_id)->toBe($entrada->id);

    $entrada->refresh();

    expect($entrada->estado)->toBe(Movimiento::ESTADO_ANULADO)
        ->and($entrada->motivo)->toBe('Entrada duplicada por error.')
        ->and($entrada->anulado_por_id)->toBe($usuario->id)
        ->and($entrada->anulado_at)->not->toBeNull();

    $articulo->refresh();

    expect((float) $articulo->stock_actual)->toBe(0.0);
});

test('anular una salida devuelve las existencias', function () {
    $usuario = User::factory()->create();
    $articulo = Articulo::factory()->create();
    $persona = Persona::create(['nombre' => 'Ana', 'apellido' => 'Soto']);
    $servicio = app(MovimientoService::class);

    $servicio->registrarEntrada(
        articuloId: $articulo->id,
        cantidad: 10.0,
        fechaMovimiento: now()->toDateTimeString(),
        costoUnitario: null,
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    );

    $salida = $servicio->registrarSalida(
        articuloId: $articulo->id,
        personaId: $persona->id,
        cantidad: 4.0,
        fechaMovimiento: now()->toDateTimeString(),
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    );

    $compensatorio = $servicio->anularMovimiento(
        movimientoId: $salida->id,
        motivo: 'La entrega no se realizó.',
        usuarioId: $usuario->id,
    );

    expect($compensatorio->tipo)->toBe('ENTRADA');

    $articulo->refresh();

    expect((float) $articulo->stock_actual)->toBe(10.0);
});

test('un movimiento anulado no se puede anular otra vez', function () {
    $usuario = User::factory()->create();
    $articulo = Articulo::factory()->create();
    $servicio = app(MovimientoService::class);

    $entrada = $servicio->registrarEntrada(
        articuloId: $articulo->id,
        cantidad: 5.0,
        fechaMovimiento: now()->toDateTimeString(),
        costoUnitario: null,
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    );

    $servicio->anularMovimiento($entrada->id, 'Primer motivo.', $usuario->id);

    expect(fn () => $servicio->anularMovimiento($entrada->id, 'Segundo motivo.', $usuario->id))
        ->toThrow(ValidationException::class);

    expect(Movimiento::count())->toBe(2);
});

test('anular un movimiento exige un motivo', function () {
    $usuario = User::factory()->create();
    $articulo = Articulo::factory()->create();
    $servicio = app(MovimientoService::class);

    $entrada = $servicio->registrarEntrada(
        articuloId: $articulo->id,
        cantidad: 5.0,
        fechaMovimiento: now()->toDateTimeString(),
        costoUnitario: null,
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    );

    expect(fn () => $servicio->anularMovimiento($entrada->id, '   ', $usuario->id))
        ->toThrow(ValidationException::class);
});

test('no se puede anular una entrada si ya no hay existencias que compensar', function () {
    $usuario = User::factory()->create();
    $articulo = Articulo::factory()->create();
    $persona = Persona::create(['nombre' => 'Ana', 'apellido' => 'Soto']);
    $servicio = app(MovimientoService::class);

    $entrada = $servicio->registrarEntrada(
        articuloId: $articulo->id,
        cantidad: 10.0,
        fechaMovimiento: now()->toDateTimeString(),
        costoUnitario: null,
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    );

    // El stock se consume por otra vía: la anulación ya no tiene con qué
    // compensarse.
    $servicio->registrarSalida(
        articuloId: $articulo->id,
        personaId: $persona->id,
        cantidad: 8.0,
        fechaMovimiento: now()->toDateTimeString(),
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    );

    expect(fn () => $servicio->anularMovimiento($entrada->id, 'Error de registro.', $usuario->id))
        ->toThrow(ValidationException::class);

    $entrada->refresh();

    expect($entrada->estado)->toBe(Movimiento::ESTADO_CONFIRMADO);
});

test('la anulación queda registrada en la auditoría', function () {
    $usuario = User::factory()->create();
    $articulo = Articulo::factory()->create();
    $servicio = app(MovimientoService::class);

    $entrada = $servicio->registrarEntrada(
        articuloId: $articulo->id,
        cantidad: 5.0,
        fechaMovimiento: now()->toDateTimeString(),
        costoUnitario: null,
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    );

    $servicio->anularMovimiento($entrada->id, 'Error de carga.', $usuario->id);

    $this->assertDatabaseHas('auditorias', [
        'modulo' => 'movimientos',
        'accion' => 'ANULAR',
    ]);
});

test('anular un movimiento desde la interfaz exige el motivo', function () {
    $usuario = User::factory()->create();
    $articulo = Articulo::factory()->create();

    $entrada = Movimiento::create([
        'articulo_id' => $articulo->id,
        'tipo' => 'ENTRADA',
        'cantidad' => 5,
        'fecha_movimiento' => now(),
        'usuario_id' => $usuario->id,
    ]);

    $this->actingAs($usuario)
        ->post(route('movimientos.anular', $entrada), [])
        ->assertSessionHasErrors('motivo');

    $entrada->refresh();

    expect($entrada->estado)->toBe(Movimiento::ESTADO_CONFIRMADO);
});

test('anular un movimiento desde la interfaz deja el stock en cero', function () {
    $usuario = User::factory()->create();
    $articulo = Articulo::factory()->create();

    $entrada = Movimiento::create([
        'articulo_id' => $articulo->id,
        'tipo' => 'ENTRADA',
        'cantidad' => 7,
        'fecha_movimiento' => now(),
        'usuario_id' => $usuario->id,
    ]);

    $this->actingAs($usuario)
        ->post(route('movimientos.anular', $entrada), [
            'motivo' => 'Entrada duplicada.',
        ])
        ->assertRedirect(route('movimientos.index'));

    $articulo->refresh();

    expect((float) $articulo->stock_actual)->toBe(0.0);
});
