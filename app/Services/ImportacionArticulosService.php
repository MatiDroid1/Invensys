<?php

namespace App\Services;

use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\UnidadMedida;

class ImportacionArticulosService
{
    /**
     * Máximo de filas de datos aceptadas en un solo archivo.
     */
    public const MAX_FILAS = 500;

    public const COLUMNAS_OBLIGATORIAS = [
        'codigo',
        'nombre',
        'categoria',
        'unidad',
        'stock_minimo',
    ];

    public const COLUMNAS_OPCIONALES = [
        'descripcion',
        'control_individual',
    ];

    /**
     * Importa artículos desde el contenido de un CSV.
     *
     * Las filas válidas se aplican aunque otras fallen: cada error se reporta
     * con su número de línea para que se pueda corregir el archivo y volver
     * a subir solo lo que faltó.
     *
     * @return array{creados: int, actualizados: int, errores: array<int, string>}
     */
    public function importar(string $contenido): array
    {
        $lineas = $this->lineas($contenido);

        if (count($lineas) < 2) {
            return [
                'creados' => 0,
                'actualizados' => 0,
                'errores' => [1 => 'El archivo no tiene filas de datos.'],
            ];
        }

        $numeroEncabezados = array_key_first($lineas);
        $encabezados = $this->encabezados($lineas[$numeroEncabezados]);
        unset($lineas[$numeroEncabezados]);

        $faltantes = array_diff(
            self::COLUMNAS_OBLIGATORIAS,
            array_keys($encabezados)
        );

        if ($faltantes !== []) {
            return [
                'creados' => 0,
                'actualizados' => 0,
                'errores' => [$numeroEncabezados => 'Faltan columnas obligatorias: '.implode(', ', $faltantes).'.'],
            ];
        }

        if (count($lineas) > self::MAX_FILAS) {
            return [
                'creados' => 0,
                'actualizados' => 0,
                'errores' => [$numeroEncabezados => 'El archivo supera el máximo de '.self::MAX_FILAS.' filas de datos.'],
            ];
        }

        $creados = 0;
        $actualizados = 0;
        $errores = [];

        foreach ($lineas as $linea => $fila) {
            if (trim(implode('', $fila)) === '') {
                continue;
            }

            try {
                $datos = $this->datosFila($fila, $encabezados);
                $resultado = $this->guardar($datos);

                if ($resultado === 'creado') {
                    $creados++;
                } else {
                    $actualizados++;
                }
            } catch (\InvalidArgumentException $e) {
                $errores[$linea] = $e->getMessage();
            }
        }

        return [
            'creados' => $creados,
            'actualizados' => $actualizados,
            'errores' => $errores,
        ];
    }

    /**
     * Plantilla en blanco para que el usuario la complete en Excel.
     *
     * Se entrega en el mismo formato de exportación del sistema (UTF-16LE con
     * `sep=;`) para que Excel la abra con los acentos correctos y el
     * separador español.
     */
    public function plantilla(): string
    {
        $filas = [
            [
                'codigo',
                'nombre',
                'categoria',
                'unidad',
                'stock_minimo',
                'descripcion',
                'control_individual',
            ],
            [
                'ART-001',
                'Notebook Dell 14"',
                'Informática',
                'Unidad',
                '5',
                'Equipo de trabajo',
                'no',
            ],
        ];

        $csv = "sep=;\r\n";

        foreach ($filas as $campos) {
            $csv .= implode(';', array_map(
                static fn ($campo) => '"'.str_replace('"', '""', $campo).'"',
                $campos
            ))."\r\n";
        }

        return "\xFF\xFE".mb_convert_encoding($csv, 'UTF-16LE', 'UTF-8');
    }

    /**
     * Decodifica el archivo a UTF-8 y devuelve las filas por su número de
     * línea original, sin la marca `sep=` que escribe Excel.
     *
     * @return array<int, array<int, string>>
     */
    private function lineas(string $contenido): array
    {
        $contenido = $this->aUtf8($contenido);

        $separador = $this->separador($contenido);

        $lineas = [];
        $numero = 0;

        foreach (preg_split('/\r\n|\n|\r/', $contenido) as $linea) {
            $numero++;

            if (trim($linea) === '') {
                continue;
            }

            // "sep=;" es una instrucción para Excel, no una fila de datos.
            if (preg_match('/^sep=.$/i', trim($linea))) {
                continue;
            }

            $lineas[$numero] = str_getcsv($linea, $separador, '"');
        }

        return $lineas;
    }

    private function aUtf8(string $contenido): string
    {
        if (str_starts_with($contenido, "\xFF\xFE")) {
            return mb_convert_encoding(
                substr($contenido, 2),
                'UTF-8',
                'UTF-16LE'
            );
        }

        if (str_starts_with($contenido, "\xFE\xFF")) {
            return mb_convert_encoding(
                substr($contenido, 2),
                'UTF-8',
                'UTF-16BE'
            );
        }

        if (str_starts_with($contenido, "\xEF\xBB\xBF")) {
            $contenido = substr($contenido, 3);
        }

        return $contenido;
    }

    /**
     * Detecta si el archivo separa con `;` (Excel en español) o con `,`.
     */
    private function separador(string $contenido): string
    {
        $primeraLinea = strtok($contenido, "\r\n");

        if ($primeraLinea === false) {
            return ';';
        }

        if (substr_count($primeraLinea, ';') >= substr_count($primeraLinea, ',')) {
            return ';';
        }

        return ',';
    }

    /**
     * Normaliza los encabezados y devuelve la posición de cada columna.
     *
     * @return array<string, int>
     */
    private function encabezados(array $fila): array
    {
        $columnas = [];

        foreach ($fila as $posicion => $titulo) {
            $clave = $this->normalizar($titulo);

            if (in_array($clave, self::COLUMNAS_OBLIGATORIAS, true)
                || in_array($clave, self::COLUMNAS_OPCIONALES, true)
            ) {
                $columnas[$clave] = $posicion;
            }
        }

        return $columnas;
    }

    private function normalizar(string $texto): string
    {
        $texto = mb_strtolower(trim($texto), 'UTF-8');
        $texto = strtr($texto, [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'ü' => 'u',
            'ñ' => 'n',
        ]);

        return str_replace([' ', '-'], '_', $texto);
    }

    /**
     * Lee una fila del CSV y devuelve los datos ya normalizados.
     *
     * @param  array<int, string>  $fila
     * @param  array<string, int>  $encabezados
     * @return array<string, mixed>
     */
    private function datosFila(array $fila, array $encabezados): array
    {
        $valor = function (string $columna) use ($fila, $encabezados): ?string {
            $posicion = $encabezados[$columna] ?? null;

            if ($posicion === null || ! isset($fila[$posicion])) {
                return null;
            }

            $texto = trim((string) $fila[$posicion]);

            return $texto === '' ? null : $texto;
        };

        $codigo = $valor('codigo');
        $nombre = $valor('nombre');
        $categoria = $valor('categoria');
        $unidad = $valor('unidad');
        $stockMinimo = $valor('stock_minimo');
        $descripcion = $valor('descripcion');
        $controlIndividual = $valor('control_individual');

        if ($codigo === null || $nombre === null) {
            throw new \InvalidArgumentException('Código y nombre son obligatorios.');
        }

        if (mb_strlen($codigo) > 50) {
            throw new \InvalidArgumentException('El código supera los 50 caracteres.');
        }

        if (mb_strlen($nombre) > 150) {
            throw new \InvalidArgumentException('El nombre supera los 150 caracteres.');
        }

        if ($stockMinimo === null) {
            throw new \InvalidArgumentException('Falta el stock mínimo.');
        }

        // Excel en español escribe los decimales con coma.
        $stockMinimo = str_replace('.', '', $stockMinimo);
        $stockMinimo = str_replace(',', '.', $stockMinimo);

        if (! is_numeric($stockMinimo) || (float) $stockMinimo < 0) {
            throw new \InvalidArgumentException('El stock mínimo debe ser un número igual o mayor a cero.');
        }

        if ($categoria === null || $unidad === null) {
            throw new \InvalidArgumentException('Falta la categoría o la unidad de medida.');
        }

        $categoriaModelo = Categoria::where('nombre', $categoria)
            ->where('activo', true)
            ->first();

        if ($categoriaModelo === null) {
            throw new \InvalidArgumentException("La categoría \"{$categoria}\" no existe o está inactiva.");
        }

        $unidadModelo = UnidadMedida::where('nombre', $unidad)
            ->where('activo', true)
            ->first();

        if ($unidadModelo === null) {
            throw new \InvalidArgumentException("La unidad de medida \"{$unidad}\" no existe o está inactiva.");
        }

        return [
            'codigo' => $codigo,
            'nombre' => $nombre,
            'categoria_id' => $categoriaModelo->id,
            'unidad_medida_id' => $unidadModelo->id,
            'stock_minimo' => (float) $stockMinimo,
            'descripcion' => $descripcion,
            'control_individual' => $this->booleano($controlIndividual),
            'activo' => true,
        ];
    }

    private function booleano(?string $valor): bool
    {
        if ($valor === null) {
            return false;
        }

        return in_array(
            $this->normalizar($valor),
            ['si', 'sí', '1', 'true', 'verdadero', 's'],
            true
        );
    }

    /**
     * Crea o actualiza el artículo según su código.
     *
     * @param  array<string, mixed>  $datos
     * @return string 'creado' | 'actualizado'
     */
    private function guardar(array $datos): string
    {
        $existente = Articulo::where('codigo', $datos['codigo'])->first();

        if ($existente === null) {
            Articulo::create($datos);

            return 'creado';
        }

        $existente->update($datos);

        return 'actualizado';
    }
}
