<?php

use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\UnidadMedida;
use App\Models\User;
use Illuminate\Http\UploadedFile;

test('un administrador importa artículos desde un CSV', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $categoria = Categoria::factory()->create(['nombre' => 'Informática']);
    $unidad = UnidadMedida::factory()->create(['nombre' => 'Unidad']);

    $csv = implode("\r\n", [
        'codigo;nombre;categoria;unidad;stock_minimo;descripcion;control_individual',
        'IMP-001;Teclado mecánico;Informática;Unidad;5;Teclado;no',
        'IMP-002;Mouse inalámbrico;Informática;Unidad;2,5;;sí',
    ]);

    $respuesta = $this->actingAs($admin)
        ->post(route('articulos.importar.store'), [
            'archivo' => UploadedFile::fake()->createWithContent('articulos.csv', $csv),
        ])
        ->assertRedirect(route('articulos.importar.create'));

    $respuesta->assertSessionHas('importacion');

    $resultado = session('importacion');
    expect($resultado['creados'])->toBe(2);
    expect($resultado['actualizados'])->toBe(0);
    expect($resultado['errores'])->toBeEmpty();

    $this->assertDatabaseHas('articulos', [
        'codigo' => 'IMP-001',
        'nombre' => 'Teclado mecánico',
        'categoria_id' => $categoria->id,
        'unidad_medida_id' => $unidad->id,
        'stock_minimo' => 5,
    ]);

    $articulo = Articulo::where('codigo', 'IMP-002')->first();
    expect((float) $articulo->stock_minimo)->toBe(2.5);
    expect($articulo->control_individual)->toBeTrue();

    $this->assertDatabaseHas('auditorias', [
        'usuario_id' => $admin->id,
        'modulo' => 'articulos',
        'accion' => 'IMPORTAR_CSV',
    ]);
});

test('una fila con categoría desconocida se reporta sin frenar la importación', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    Categoria::factory()->create(['nombre' => 'Informática']);
    UnidadMedida::factory()->create(['nombre' => 'Unidad']);

    $csv = implode("\r\n", [
        'codigo;nombre;categoria;unidad;stock_minimo',
        'ERR-001;Artículo bueno;Informática;Unidad;3',
        'ERR-002;Artículo malo;NoExiste;Unidad;3',
    ]);

    $this->actingAs($admin)
        ->post(route('articulos.importar.store'), [
            'archivo' => UploadedFile::fake()->createWithContent('articulos.csv', $csv),
        ])
        ->assertRedirect(route('articulos.importar.create'));

    $resultado = session('importacion');

    expect($resultado['creados'])->toBe(1);
    expect($resultado['errores'])->toHaveCount(1);
    expect($resultado['errores'][3])->toContain('NoExiste');

    $this->assertDatabaseHas('articulos', ['codigo' => 'ERR-001']);
    $this->assertDatabaseMissing('articulos', ['codigo' => 'ERR-002']);
});

test('la importación exige las columnas obligatorias', function () {
    $admin = User::factory()->create(['rol' => 'admin']);

    $csv = "codigo;nombre\r\nIMP-001;Sin más";

    $this->actingAs($admin)
        ->post(route('articulos.importar.store'), [
            'archivo' => UploadedFile::fake()->createWithContent('articulos.csv', $csv),
        ])
        ->assertRedirect(route('articulos.importar.create'));

    $resultado = session('importacion');

    expect($resultado['creados'])->toBe(0);
    expect($resultado['errores'][1])->toContain('categoria');
});

test('la importación de artículos es solo de administradores', function () {
    $usuario = User::factory()->create(['rol' => 'usuario']);

    $this->actingAs($usuario)
        ->get(route('articulos.importar.create'))
        ->assertForbidden();

    $this->actingAs($usuario)
        ->post(route('articulos.importar.store'), [])
        ->assertForbidden();

    $this->actingAs($usuario)
        ->get(route('articulos.importar.plantilla'))
        ->assertForbidden();
});

test('la plantilla de importación descarga en formato Excel en español', function () {
    $admin = User::factory()->create(['rol' => 'admin']);

    $respuesta = $this->actingAs($admin)
        ->get(route('articulos.importar.plantilla'))
        ->assertOk();

    $respuesta->assertHeader('content-type', 'text/csv; charset=UTF-16LE');
    $respuesta->assertHeader('content-disposition');

    $contenido = $respuesta->getContent();
    expect(substr($contenido, 0, 2))->toBe("\xFF\xFE");

    $contenidoUtf8 = mb_convert_encoding(substr($contenido, 2), 'UTF-8', 'UTF-16LE');
    expect(explode("\r\n", $contenidoUtf8)[0])->toBe('sep=;');
    expect($contenidoUtf8)->toContain('codigo');
    expect($contenidoUtf8)->toContain('stock_minimo');
});

test('la importación acepta el archivo de plantilla con acentos y BOM UTF-16', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    Categoria::factory()->create(['nombre' => 'Informática']);
    UnidadMedida::factory()->create(['nombre' => 'Unidad']);

    $csv = implode("\r\n", [
        'sep=;',
        'codigo;nombre;categoria;unidad;stock_minimo',
        'ACC-001;Teclado con acentos rápidos;Informática;Unidad;1',
    ]);

    $contenido = "\xFF\xFE".mb_convert_encoding($csv, 'UTF-16LE', 'UTF-8');

    $this->actingAs($admin)
        ->post(route('articulos.importar.store'), [
            'archivo' => UploadedFile::fake()->createWithContent('articulos.csv', $contenido),
        ])
        ->assertRedirect(route('articulos.importar.create'));

    $resultado = session('importacion');

    expect($resultado['creados'])->toBe(1);
    expect($resultado['errores'])->toBeEmpty();

    $this->assertDatabaseHas('articulos', [
        'codigo' => 'ACC-001',
        'nombre' => 'Teclado con acentos rápidos',
    ]);
});
