<?php

use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\Movimiento;
use App\Models\Persona;
use App\Models\User;

test('los CSV de reportes son compatibles con Excel en español', function () {
    $usuario = User::factory()->create();
    $categoria = Categoria::factory()->create(['nombre' => 'Informática']);
    $articulo = Articulo::factory()->create([
        'codigo' => 'ART-001',
        'nombre' => 'Artículo; "especial"',
        'categoria_id' => $categoria->id,
    ]);
    $persona = Persona::create([
        'nombre' => 'Ana',
        'apellido' => 'Pérez',
        'identificador' => '12345678-9',
        'activo' => true,
    ]);

    Movimiento::create([
        'articulo_id' => $articulo->id,
        'tipo' => 'SALIDA',
        'cantidad' => 12.5,
        'fecha_movimiento' => '2026-10-03 14:30:00',
        'persona_id' => $persona->id,
        'usuario_id' => $usuario->id,
        'referencia' => 'Entrega; "urgente"',
    ]);

    $this->actingAs($usuario);

    $reportes = [
        route('reportes.stock.csv') => [['Código', 'Artículo', 'Stock actual'], false, true],
        route('reportes.movimientos.csv') => [
            ['Fecha', 'Hora', 'Artículo (código)', 'Artículo'],
            true,
            false,
        ],
        route('reportes.entregas-persona.csv') => [
            ['Persona', 'Rut', 'Fecha', 'Hora'],
            true,
            false,
        ],
    ];

    foreach ($reportes as $url => [$encabezadosEsperados, $incluyeReferencia, $incluyeCategoria]) {
        $respuesta = $this->get($url)->assertOk();
        $respuesta->assertHeader('content-type', 'text/csv; charset=UTF-16LE');
        $respuesta->assertHeader('content-disposition');

        $contenido = $respuesta->getContent();
        expect(substr($contenido, 0, 2))->toBe("\xFF\xFE");

        $contenidoUtf8 = mb_convert_encoding(substr($contenido, 2), 'UTF-8', 'UTF-16LE');
        $filas = explode("\r\n", $contenidoUtf8);
        expect($filas[0])->toBe('sep=;');

        $encabezados = str_getcsv($filas[1], ';', '"', '');
        foreach ($encabezadosEsperados as $encabezado) {
            expect($encabezados)->toContain($encabezado);
        }

        $filaDatos = str_getcsv($filas[2], ';', '"', '');
        expect($filaDatos)->toContain('Artículo; "especial"');
        if ($incluyeCategoria) {
            expect($contenidoUtf8)->toContain('Informática');
        }

        expect($contenidoUtf8)->not->toContain('InformÃ¡tica');

        if ($incluyeReferencia) {
            expect($filaDatos)->toContain('Entrega; "urgente"');
        }
    }
});
