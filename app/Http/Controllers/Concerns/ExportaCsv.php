<?php

namespace App\Http\Controllers\Concerns;

trait ExportaCsv
{
    /**
     * Arma el CSV para Excel en español.
     *
     * `fputcsv` con sus valores por defecto escribe coma como separador, salto
     * `\n` y sin marca de orden de bytes: abierto en Excel en español eso
     * significa una sola columna con todo el archivo amontonado, acentos rotos
     * y las filas pegadas en un renglón. Aquí se usa `;`, CRLF y marca UTF-16LE
     * para que Excel de escritorio respete los acentos incluso cuando ignora el
     * BOM UTF-8.
     *
     * @param  array<int, array<int, mixed>>  $lineas
     */
    private function respuestaCsv(array $lineas, string $nombreArchivo)
    {
        $archivo = fopen('php://temp', 'w+');

        if ($archivo === false) {
            throw new \RuntimeException('No se pudo preparar el archivo CSV.');
        }

        try {
            if (fwrite($archivo, "sep=;\r\n") === false) {
                throw new \RuntimeException('No se pudo escribir el separador del archivo CSV.');
            }

            foreach ($lineas as $campos) {
                $campos = array_map(
                    static fn ($campo) => (string) $campo,
                    $campos
                );

                if (fputcsv($archivo, $campos, ';', '"', '', "\r\n") === false) {
                    throw new \RuntimeException('No se pudo escribir una fila del archivo CSV.');
                }
            }

            rewind($archivo);
            $contenido = stream_get_contents($archivo);

            if ($contenido === false) {
                throw new \RuntimeException('No se pudo leer el archivo CSV generado.');
            }
        } finally {
            fclose($archivo);
        }

        $contenido = mb_convert_encoding($contenido, 'UTF-16LE', 'UTF-8');

        return response("\xFF\xFE".$contenido, 200, [
            'Content-Type' => 'text/csv; charset=UTF-16LE',
            'Content-Disposition' => "attachment; filename=\"{$nombreArchivo}\"",
        ]);
    }

    /**
     * Cantidad en formato español: punto de miles y coma decimal.
     *
     * Los decimales de relleno se omiten cuando el valor es entero, para que
     * `12` no aparezca como `12,00` en un campo de cantidad.
     */
    private function numeroEs($valor, int $decimales = 2): string
    {
        $numero = (float) $valor;

        if ($decimales > 0 && fmod($numero, 1.0) === 0.0) {
            $decimales = 0;
        }

        return number_format($numero, $decimales, ',', '.');
    }
}
