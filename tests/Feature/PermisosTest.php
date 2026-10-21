<?php

use App\Enums\Permiso;
use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\UnidadMedida;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

/*
 * Control de permisos.
 *
 * El objetivo de esta fase es que la autorización deje de ser "¿es
 * administrador?" y pase a ser "¿qué puede hacer esta persona?". Estos
 * pruebas fijan ese contrato para que no se rompa por accidente.
 */

test('el administrador tiene todos los permisos', function () {
    $admin = User::factory()->create(['rol' => 'admin']);

    foreach (Permiso::cases() as $permiso) {
        expect($admin->puede($permiso))->toBeTrue();
    }
});

test('el operador puede operar el inventario pero no administrar el sistema', function () {
    $operador = User::factory()->create(['rol' => 'usuario']);

    expect($operador->puede(Permiso::MovimientosEntrada))->toBeTrue()
        ->and($operador->puede(Permiso::MovimientosSalida))->toBeTrue()
        ->and($operador->puede(Permiso::MovimientosAnular))->toBeTrue()
        ->and($operador->puede(Permiso::ArticulosCrear))->toBeTrue()
        ->and($operador->puede(Permiso::PersonasEditar))->toBeTrue();

    // Lo que es de administración queda fuera.
    expect($operador->puede(Permiso::CostosEditar))->toBeFalse()
        ->and($operador->puede(Permiso::UsuariosGestionar))->toBeFalse()
        ->and($operador->puede(Permiso::AuditoriaVer))->toBeFalse()
        ->and($operador->puede(Permiso::CategoriasGestionar))->toBeFalse()
        ->and($operador->puede(Permiso::UnidadesGestionar))->toBeFalse()
        ->and($operador->puede(Permiso::Importar))->toBeFalse()
        ->and($operador->puede(Permiso::ConfiguracionEditar))->toBeFalse();
});

test('un usuario desactivado no tiene ningún permiso', function () {
    $usuario = User::factory()->inactivo()->create(['rol' => 'admin']);

    expect($usuario->puede(Permiso::MovimientosVer))->toBeFalse()
        ->and($usuario->puede(Permiso::ArticulosVer))->toBeFalse();
});

test('cada permiso del vocabulario tiene su gate', function () {
    foreach (Permiso::cases() as $permiso) {
        expect(Gate::has($permiso->value))->toBeTrue();
    }
});

test('un operador no puede entrar en las pantallas de administración', function () {
    $operador = User::factory()->create(['rol' => 'usuario']);

    $this->actingAs($operador)->get(route('usuarios.index'))->assertForbidden();
    $this->actingAs($operador)->get(route('auditoria.index'))->assertForbidden();
    $this->actingAs($operador)->get(route('articulos.importar.create'))->assertForbidden();
});

test('un operador no puede fijar el costo unitario de una entrada', function () {
    $operador = User::factory()->create(['rol' => 'usuario']);
    $articulo = Articulo::factory()->create();

    $this->actingAs($operador)
        ->post(route('movimientos.store'), [
            'articulo_id' => $articulo->id,
            'cantidad' => 5,
            'fecha_movimiento' => now()->toDateString(),
            'costo_unitario' => 1000,
        ])
        ->assertSessionHasErrors('costo_unitario');

    expect($articulo->movimientos()->count())->toBe(0);
});

test('un administrador sí puede fijar el costo unitario de una entrada', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $articulo = Articulo::factory()->create();

    $this->actingAs($admin)
        ->post(route('movimientos.store'), [
            'articulo_id' => $articulo->id,
            'cantidad' => 5,
            'fecha_movimiento' => now()->toDateString(),
            'costo_unitario' => 1000,
        ])
        ->assertSessionHasNoErrors();

    expect($articulo->movimientos()->count())->toBe(1);
});

test('un operador no puede cambiar el precio de venta de un artículo', function () {
    $operador = User::factory()->create(['rol' => 'usuario']);
    $categoria = Categoria::factory()->create();
    $unidad = UnidadMedida::factory()->create();

    $articulo = Articulo::factory()->create([
        'categoria_id' => $categoria->id,
        'unidad_medida_id' => $unidad->id,
        'precio_venta' => 1500,
    ]);

    $this->actingAs($operador)
        ->put(route('articulos.update', $articulo), [
            'codigo' => $articulo->codigo,
            'nombre' => $articulo->nombre,
            'categoria_id' => $categoria->id,
            'unidad_medida_id' => $unidad->id,
            'stock_minimo' => $articulo->stock_minimo,
            'precio_venta' => 9999,
            'activo' => 1,
        ])
        ->assertSessionHasErrors('precio_venta');

    expect((float) $articulo->refresh()->precio_venta)->toBe(1500.0);
});

test('un operador puede editar un artículo sin tocar su precio', function () {
    $operador = User::factory()->create(['rol' => 'usuario']);
    $categoria = Categoria::factory()->create();
    $unidad = UnidadMedida::factory()->create();

    $articulo = Articulo::factory()->create([
        'categoria_id' => $categoria->id,
        'unidad_medida_id' => $unidad->id,
        'precio_venta' => 1500,
    ]);

    $this->actingAs($operador)
        ->put(route('articulos.update', $articulo), [
            'codigo' => $articulo->codigo,
            'nombre' => 'Nombre corregido',
            'categoria_id' => $categoria->id,
            'unidad_medida_id' => $unidad->id,
            'stock_minimo' => $articulo->stock_minimo,
            'activo' => 1,
        ])
        ->assertSessionHasNoErrors();

    $articulo->refresh();

    expect($articulo->nombre)->toBe('Nombre corregido')
        ->and((float) $articulo->precio_venta)->toBe(1500.0);
});
