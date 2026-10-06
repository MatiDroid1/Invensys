<?php

use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\Movimiento;
use App\Models\User;

test('el kardex se descarga en PDF con el saldo acumulado', function () {
    $usuario = User::factory()->create();
    $categoria = Categoria::factory()->create();
    $articulo = Articulo::factory()->create([
        'codigo' => 'PDF-001',
        'categoria_id' => $categoria->id,
    ]);

    Movimiento::create([
        'articulo_id' => $articulo->id,
        'tipo' => 'ENTRADA',
        'cantidad' => 10,
        'fecha_movimiento' => '2026-10-01 09:00:00',
        'usuario_id' => $usuario->id,
    ]);

    Movimiento::create([
        'articulo_id' => $articulo->id,
        'tipo' => 'SALIDA',
        'cantidad' => 4,
        'fecha_movimiento' => '2026-10-02 10:00:00',
        'usuario_id' => $usuario->id,
    ]);

    $respuesta = $this->actingAs($usuario)
        ->get(route('kardex.pdf', ['articulo_id' => $articulo->id]))
        ->assertOk();

    $respuesta->assertHeader('content-type', 'application/pdf');
    $respuesta->assertHeader('content-disposition');
    expect($respuesta->headers->get('content-disposition'))
        ->toContain('kardex_PDF-001')
        ->toContain('.pdf');

    expect(substr($respuesta->getContent(), 0, 5))->toBe('%PDF-');
});

test('el kardex en PDF exige un artículo válido', function () {
    $usuario = User::factory()->create();

    $this->actingAs($usuario)
        ->get(route('kardex.pdf'))
        ->assertStatus(302);

    $this->actingAs($usuario)
        ->get(route('kardex.pdf', ['articulo_id' => 999999]))
        ->assertStatus(302);
});

test('el CSV de auditoría usa el formato de Excel en español y es solo de administradores', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $usuario = User::factory()->create(['rol' => 'usuario']);

    $respuesta = $this->actingAs($admin)
        ->get(route('auditoria.csv'))
        ->assertOk();

    $respuesta->assertHeader('content-type', 'text/csv; charset=UTF-16LE');
    $respuesta->assertHeader('content-disposition');

    $contenido = $respuesta->getContent();
    expect(substr($contenido, 0, 2))->toBe("\xFF\xFE");

    $contenidoUtf8 = mb_convert_encoding(substr($contenido, 2), 'UTF-8', 'UTF-16LE');
    $filas = explode("\r\n", $contenidoUtf8);
    expect($filas[0])->toBe('sep=;');

    $encabezados = str_getcsv($filas[1], ';', '"', '');
    expect($encabezados)->toContain('Fecha', 'Usuario', 'Módulo', 'Acción');

    $this->actingAs($usuario)
        ->get(route('auditoria.csv'))
        ->assertForbidden();
});
