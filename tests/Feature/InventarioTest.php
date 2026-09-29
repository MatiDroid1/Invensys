<?php

use App\Models\Articulo;
use App\Models\Auditoria;
use App\Models\Categoria;
use App\Models\Movimiento;
use App\Models\Persona;
use App\Models\UnidadMedida;
use App\Models\User;
use App\Services\MovimientoService;
use Illuminate\Validation\ValidationException;

/**
 * El stock nunca se almacena: se calcula sumando los movimientos del artículo.
 * Estos tests fijan ese comportamiento.
 */
test('un artículo sin movimientos tiene stock cero', function () {
    $articulo = Articulo::factory()->create();

    expect((float) $articulo->stock_actual)->toBe(0.0);
});

test('el stock suma entradas y resta salidas', function () {
    $articulo = Articulo::factory()->create();

    foreach ([['ENTRADA', 100], ['SALIDA', 30], ['ENTRADA', 5]] as [$tipo, $cantidad]) {
        Movimiento::create([
            'articulo_id' => $articulo->id,
            'tipo' => $tipo,
            'cantidad' => $cantidad,
            'fecha_movimiento' => now(),
            'usuario_id' => User::factory()->create()->id,
        ]);
    }

    expect((float) $articulo->fresh()->stock_actual)->toBe(75.0);
});

test('los ajustes positivos y negativos afectan el stock', function () {
    $articulo = Articulo::factory()->create();
    $usuarioId = User::factory()->create()->id;

    foreach ([['AJUSTE_POSITIVO', 20], ['AJUSTE_NEGATIVO', 8]] as [$tipo, $cantidad]) {
        Movimiento::create([
            'articulo_id' => $articulo->id,
            'tipo' => $tipo,
            'cantidad' => $cantidad,
            'fecha_movimiento' => now(),
            'usuario_id' => $usuarioId,
        ]);
    }

    expect((float) $articulo->fresh()->stock_actual)->toBe(12.0);
});

test('registrar una entrada aumenta el stock y deja auditoría', function () {
    $articulo = Articulo::factory()->create();
    $usuario = User::factory()->create();

    app(MovimientoService::class)->registrarEntrada(
        articuloId: $articulo->id,
        cantidad: 50.0,
        fechaMovimiento: now()->toDateTimeString(),
        referencia: 'OC-001',
        observaciones: null,
        usuarioId: $usuario->id,
    );

    expect((float) $articulo->fresh()->stock_actual)->toBe(50.0);

    expect(Auditoria::where('accion', 'ENTRADA')->exists())->toBeTrue();
});

test('registrar una salida descuenta el stock', function () {
    $articulo = Articulo::factory()->create();
    $usuario = User::factory()->create();
    $persona = Persona::create(['nombre' => 'Ana', 'apellido' => 'Soto']);

    $servicio = app(MovimientoService::class);

    $servicio->registrarEntrada(
        articuloId: $articulo->id,
        cantidad: 10.0,
        fechaMovimiento: now()->toDateTimeString(),
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

    expect((float) $articulo->fresh()->stock_actual)->toBe(6.0);
});

test('no permite una salida mayor al stock disponible', function () {
    $articulo = Articulo::factory()->create();
    $usuario = User::factory()->create();
    $persona = Persona::create(['nombre' => 'Ana', 'apellido' => 'Soto']);

    app(MovimientoService::class)->registrarEntrada(
        articuloId: $articulo->id,
        cantidad: 5.0,
        fechaMovimiento: now()->toDateTimeString(),
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    );

    expect(fn () => app(MovimientoService::class)->registrarSalida(
        articuloId: $articulo->id,
        personaId: $persona->id,
        cantidad: 6.0,
        fechaMovimiento: now()->toDateTimeString(),
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    ))->toThrow(ValidationException::class);

    // La transacción se revierte: el stock no cambia.
    expect((float) $articulo->fresh()->stock_actual)->toBe(5.0);
});

test('no permite un ajuste negativo mayor al stock disponible', function () {
    $articulo = Articulo::factory()->create();
    $usuario = User::factory()->create();

    app(MovimientoService::class)->registrarEntrada(
        articuloId: $articulo->id,
        cantidad: 3.0,
        fechaMovimiento: now()->toDateTimeString(),
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    );

    expect(fn () => app(MovimientoService::class)->registrarAjuste(
        articuloId: $articulo->id,
        tipo: 'AJUSTE_NEGATIVO',
        cantidad: 9.0,
        fechaMovimiento: now()->toDateTimeString(),
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    ))->toThrow(ValidationException::class);

    expect((float) $articulo->fresh()->stock_actual)->toBe(3.0);
});

test('desactivar un artículo no elimina sus movimientos', function () {
    $articulo = Articulo::factory()->create();
    $usuario = User::factory()->create();

    app(MovimientoService::class)->registrarEntrada(
        articuloId: $articulo->id,
        cantidad: 2.0,
        fechaMovimiento: now()->toDateTimeString(),
        referencia: null,
        observaciones: null,
        usuarioId: $usuario->id,
    );

    $this->actingAs($usuario)
        ->delete(route('articulos.destroy', $articulo))
        ->assertRedirect(route('articulos.index'));

    $articulo->refresh();

    expect($articulo->activo)->toBeFalse()
        ->and($articulo->movimientos()->count())->toBe(1)
        ->and((float) $articulo->stock_actual)->toBe(2.0);
});

test('crear un artículo registra la auditoría y valida los datos', function () {
    $usuario = User::factory()->create();
    $categoria = Categoria::factory()->create();
    $unidad = UnidadMedida::factory()->create();

    $this->actingAs($usuario)
        ->post(route('articulos.store'), [
            'codigo' => 'MED-001',
            'nombre' => 'Jeringa 5ml',
            'descripcion' => 'Jeringa descartable',
            'categoria_id' => $categoria->id,
            'unidad_medida_id' => $unidad->id,
            'stock_minimo' => 10,
            'control_individual' => 1,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('articulos.index'));

    $this->assertDatabaseHas('articulos', ['codigo' => 'MED-001', 'control_individual' => 1]);
    expect(Auditoria::where('accion', 'CREAR')->exists())->toBeTrue();
});

test('rechaza un artículo sin categoría válida', function () {
    $usuario = User::factory()->create();

    $this->actingAs($usuario)
        ->post(route('articulos.store'), [
            'codigo' => 'XXX-000',
            'nombre' => 'Artículo incompleto',
            'categoria_id' => 99999,
            'unidad_medida_id' => 99999,
            'stock_minimo' => 1,
        ])
        ->assertSessionHasErrors(['categoria_id', 'unidad_medida_id']);

    expect(Articulo::where('codigo', 'XXX-000')->exists())->toBeFalse();
});
