<?php

use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\UnidadMedida;
use App\Models\User;
use App\Services\ImportacionArticulosService;
use Illuminate\Http\UploadedFile;

/*
 * Importación masiva segura.
 *
 * Importar es una operación de bloque sobre el catálogo. Tres reglas la
 * vuelven segura:
 *
 *  1. No es destructiva: solo toca las columnas que el archivo trae.
 *  2. Es atómica: si algo falla, no queda el catálogo a medias.
 *  3. Es acotada: rechaza duplicados y valores fuera de los límites.
 */

function catalogoParaImportar(): array
{
    $categoria = Categoria::factory()->create(['nombre' => 'Informática']);
    $unidad = UnidadMedida::factory()->create(['nombre' => 'Unidad']);

    return [$categoria, $unidad];
}

test('importar no borra la descripción ni el precio que el archivo no trae', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    [$categoria, $unidad] = catalogoParaImportar();

    $articulo = Articulo::factory()->create([
        'codigo' => 'MANT-001',
        'nombre' => 'Artículo que ya existe',
        'categoria_id' => $categoria->id,
        'unidad_medida_id' => $unidad->id,
        'descripcion' => 'Descripción cargada a mano',
        'precio_venta' => 1500,
    ]);

    $csv = implode("\r\n", [
        'codigo;nombre;categoria;unidad;stock_minimo',
        'MANT-001;Nombre nuevo;Informática;Unidad;7',
    ]);

    $this->actingAs($admin)
        ->post(route('articulos.importar.store'), [
            'archivo' => UploadedFile::fake()->createWithContent('articulos.csv', $csv),
        ]);

    $articulo->refresh();

    expect($articulo->nombre)->toBe('Nombre nuevo')
        ->and($articulo->descripcion)->toBe('Descripción cargada a mano')
        ->and((float) $articulo->precio_venta)->toBe(1500.0)
        ->and((float) $articulo->stock_minimo)->toBe(7.0);
});

test('importar no reactiva un artículo desactivado', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    [$categoria, $unidad] = catalogoParaImportar();

    $articulo = Articulo::factory()->create([
        'codigo' => 'INACT-001',
        'nombre' => 'Artículo desactivado',
        'categoria_id' => $categoria->id,
        'unidad_medida_id' => $unidad->id,
        'activo' => false,
    ]);

    $csv = implode("\r\n", [
        'codigo;nombre;categoria;unidad;stock_minimo',
        'INACT-001;Artículo desactivado;Informática;Unidad;3',
    ]);

    $this->actingAs($admin)
        ->post(route('articulos.importar.store'), [
            'archivo' => UploadedFile::fake()->createWithContent('articulos.csv', $csv),
        ]);

    expect($articulo->refresh()->activo)->toBeFalse();
});

test('una celda vacía no borra el dato que ya estaba', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    [$categoria, $unidad] = catalogoParaImportar();

    $articulo = Articulo::factory()->create([
        'codigo' => 'VACIA-001',
        'nombre' => 'Artículo con descripción',
        'categoria_id' => $categoria->id,
        'unidad_medida_id' => $unidad->id,
        'descripcion' => 'No se debe borrar',
    ]);

    $csv = implode("\r\n", [
        'codigo;nombre;categoria;unidad;stock_minimo;descripcion',
        'VACIA-001;Artículo con descripción;Informática;Unidad;1;',
    ]);

    $this->actingAs($admin)
        ->post(route('articulos.importar.store'), [
            'archivo' => UploadedFile::fake()->createWithContent('articulos.csv', $csv),
        ]);

    expect($articulo->refresh()->descripcion)->toBe('No se debe borrar');
});

test('un código repetido en el mismo archivo se informa en la segunda línea', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    catalogoParaImportar();

    $csv = implode("\r\n", [
        'codigo;nombre;categoria;unidad;stock_minimo',
        'DUP-001;Primero;Informática;Unidad;1',
        'DUP-001;Segundo;Informática;Unidad;2',
    ]);

    $this->actingAs($admin)
        ->post(route('articulos.importar.store'), [
            'archivo' => UploadedFile::fake()->createWithContent('articulos.csv', $csv),
        ]);

    $resultado = session('importacion');

    expect($resultado['creados'])->toBe(1)
        ->and($resultado['errores'])->toHaveCount(1)
        ->and($resultado['errores'][3])->toContain('DUP-001');

    $this->assertDatabaseMissing('articulos', ['nombre' => 'Segundo']);
});

test('un stock mínimo fuera de los límites se informa y no se guarda', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    catalogoParaImportar();

    $csv = implode("\r\n", [
        'codigo;nombre;categoria;unidad;stock_minimo',
        'LIM-001;Demasiado;Informática;Unidad;99999999999',
    ]);

    $this->actingAs($admin)
        ->post(route('articulos.importar.store'), [
            'archivo' => UploadedFile::fake()->createWithContent('articulos.csv', $csv),
        ]);

    $resultado = session('importacion');

    expect($resultado['creados'])->toBe(0)
        ->and($resultado['errores'])->toHaveCount(1)
        ->and($resultado['errores'][2])->toContain('máximo');

    $this->assertDatabaseMissing('articulos', ['codigo' => 'LIM-001']);
});

test('una importación que falla a mitad se revierte entera', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    catalogoParaImportar();

    // Se provoca un fallo de base de datos en la segunda fila.
    Articulo::creating(function (Articulo $articulo) {
        if ($articulo->codigo === 'FALLA-002') {
            throw new RuntimeException('Fallo simulado.');
        }
    });

    $csv = implode("\r\n", [
        'codigo;nombre;categoria;unidad;stock_minimo',
        'OK-001;Va bien;Informática;Unidad;1',
        'FALLA-002;Explota;Informática;Unidad;1',
    ]);

    expect(fn () => app(ImportacionArticulosService::class)->importar($csv))
        ->toThrow(RuntimeException::class);

    $this->assertDatabaseMissing('articulos', ['codigo' => 'OK-001']);
    $this->assertDatabaseMissing('articulos', ['codigo' => 'FALLA-002']);

    // El escucha de eventos se queda en el modelo para todo el proceso de
    // pruebas: se retira para no molestar a los tests siguientes.
    Articulo::flushEventListeners();
});

test('la previsualización no toca el catálogo', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    catalogoParaImportar();

    $csv = implode("\r\n", [
        'codigo;nombre;categoria;unidad;stock_minimo',
        'PREV-001;Previsualizado;Informática;Unidad;1',
    ]);

    $this->actingAs($admin)
        ->post(route('articulos.importar.previsualizar'), [
            'archivo' => UploadedFile::fake()->createWithContent('articulos.csv', $csv),
        ])
        ->assertOk();

    $this->assertDatabaseMissing('articulos', ['codigo' => 'PREV-001']);
});

test('confirmar la previsualización aplica el archivo', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    catalogoParaImportar();

    $csv = implode("\r\n", [
        'codigo;nombre;categoria;unidad;stock_minimo',
        'CONF-001;Confirmado;Informática;Unidad;1',
    ]);

    $this->actingAs($admin)
        ->post(route('articulos.importar.previsualizar'), [
            'archivo' => UploadedFile::fake()->createWithContent('articulos.csv', $csv),
        ]);

    $token = session('importacion_previa');

    expect($token)->not->toBeNull();

    $this->actingAs($admin)
        ->post(route('articulos.importar.confirmar'), ['token' => $token])
        ->assertRedirect(route('articulos.importar.create'));

    $this->assertDatabaseHas('articulos', ['codigo' => 'CONF-001']);
});

test('confirmar sin una previsualización en curso no hace nada', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    catalogoParaImportar();

    $this->actingAs($admin)
        ->post(route('articulos.importar.confirmar'), ['token' => 'inventado'])
        ->assertRedirect(route('articulos.importar.create'))
        ->assertSessionHas('error');

    $this->assertDatabaseCount('articulos', 0);
});
