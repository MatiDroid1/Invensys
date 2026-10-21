<?php

namespace App\Services;

use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\UnidadMedida;
use App\Support\Limites;
use Illuminate\Support\Facades\DB;

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
        'precio_venta',
    ];

    /**
     * Importa artículos desde el contenido de un CSV.
     *
     * Las filas válidas se aplican aunque otras fallen: cada error se reporta
     * con su número de línea para que se pueda corregir el archivo y volver
     * a subir solo lo que faltó.
     *
     * Las filas válidas se aplican todas juntas en una única transacción: si
     * alguna revienta a nivel de base de datos, no queda el catálogo a medias.
     *
     * @return array{creados: int, actualizados: int, errores: array<int, string>}
     */
    public function importar(string $contenido, bool $permitirCostos = true): array
    {
        $plan = $this->analizar($contenido, $permitirCostos);

        if ($plan['filas'] === []) {
            return [
                'creados' => 0,
                'actualizados' => 0,
                'errores' => $plan['errores'],
            ];
        }

        DB::transaction(function () use ($plan) {
            foreach ($plan['filas'] as $fila) {
                $this->guardar($fila['datos']);
            }
        });

        return [
            'creados' => $plan['creados'],
            'actualizados' => $plan['actualizados'],
            'errores' => $plan['errores'],
        ];
    }

    /**
     * Analiza el archivo y devuelve el plan de importación sin tocar la base
     * de datos.
     *
     * Sirve para dos cosas: avisar de los errores antes de aplicar nada, y
     * poder mostrar en pantalla qué va a cambiar exactamente.
     *
     * @return array{
     *     creados: int,
     *     actualizados: int,
     *     errores: array<int, string>,
     *     avisos: array<int, string>,
     *     filas: array<int, array{linea: int, codigo: string, nombre: string, accion: string, datos: array<string, mixed>}>
     * }
     */
    public function analizar(string $contenido, bool $permitirCostos = true): array
    {
        $lineas = $this->lineas($contenido);

        if (count($lineas) < 2) {
            return $this->planVacio([1 => 'El archivo no tiene filas de datos.']);
        }

        $numeroEncabezados = array_key_first($lineas);
        $encabezados = $this->encabezados($lineas[$numeroEncabezados]);
        unset($lineas[$numeroEncabezados]);

        $faltantes = array_diff(
            self::COLUMNAS_OBLIGATORIAS,
            array_keys($encabezados)
        );

        if ($faltantes !== []) {
            return $this->planVacio([
                $numeroEncabezados => 'Faltan columnas obligatorias: '.implode(', ', $faltantes).'.',
            ]);
        }

        if (count($lineas) > self::MAX_FILAS) {
            return $this->planVacio([
                $numeroEncabezados => 'El archivo supera el máximo de '.self::MAX_FILAS.' filas de datos.',
            ]);
        }

        $separador = $this->separador($contenido);
        $errores = [];
        $filas = [];
        $codigos = [];

        foreach ($lineas as $linea => $fila) {
            if (trim(implode('', $fila)) === '') {
                continue;
            }

            try {
                $datos = $this->datosFila($fila, $encabezados, $separador, $permitirCostos);

                if (isset($codigos[$datos['codigo']])) {
                    throw new \InvalidArgumentException(
                        'El código "'.$datos['codigo'].'" ya aparece en la línea '.$codigos[$datos['codigo']].'.'
                    );
                }

                $codigos[$datos['codigo']] = $linea;

                $existente = Articulo::where('codigo', $datos['codigo'])->first();

                $filas[] = [
                    'linea' => $linea,
                    'codigo' => $datos['codigo'],
                    'nombre' => $datos['nombre'],
                    'accion' => $existente === null ? 'creado' : 'actualizado',
                    'datos' => $datos,
                ];
            } catch (\InvalidArgumentException $e) {
                $errores[$linea] = $e->getMessage();
            }
        }

        return [
            'creados' => count(array_filter($filas, fn (array $f) => $f['accion'] === 'creado')),
            'actualizados' => count(array_filter($filas, fn (array $f) => $f['accion'] === 'actualizado')),
            'errores' => $errores,
            'avisos' => $this->avisos($encabezados, $permitirCostos),
            'filas' => $filas,
        ];
    }

    /**
     * Avisos sobre columnas del archivo que no se van a aplicar.
     *
     * @param  array<string, int>  $encabezados
     * @return array<int, string>
     */
    private function avisos(array $encabezados, bool $permitirCostos): array
    {
        $avisos = [];

        if (! $permitirCostos && isset($encabezados['precio_venta'])) {
            $avisos[] = 'La columna precio_venta se ha ignorado: no tienes permiso para modificar costos.';
        }

        return $avisos;
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
                'precio_venta',
            ],
            [
                'ART-001',
                'Notebook Dell 14"',
                'Informática',
                'Unidad',
                '5',
                'Equipo de trabajo',
                'no',
                '899990',
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
     * Plan vacío: solo hay errores que informar.
     *
     * @param  array<int, string>  $errores
     * @return array{creados: int, actualizados: int, errores: array<int, string>, avisos: array<int, string>, filas: array<int, array<string, mixed>>}
     */
    private function planVacio(array $errores): array
    {
        return [
            'creados' => 0,
            'actualizados' => 0,
            'errores' => $errores,
            'avisos' => [],
            'filas' => [],
        ];
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
     * Solo devuelve las columnas que el archivo trae y whose celda tiene
     * valor. Una columna que no aparece en el archivo no se toca en el
     * artículo existente: importar un CSV con cinco columnas no puede
     * borrar la descripción o el precio que alguien cargó a mano.
     *
     * @param  array<int, string>  $fila
     * @param  array<string, int>  $encabezados
     * @return array<string, mixed>
     */
    private function datosFila(
        array $fila,
        array $encabezados,
        string $separador,
        bool $permitirCostos
    ): array {
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
        $precioVenta = $valor('precio_venta');

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

        if ($categoria === null || $unidad === null) {
            throw new \InvalidArgumentException('Falta la categoría o la unidad de medida.');
        }

        $stockMinimoNumero = $this->numero($stockMinimo, $separador);

        if ($stockMinimoNumero === null || (float) $stockMinimoNumero < 0) {
            throw new \InvalidArgumentException('El stock mínimo debe ser un número igual o mayor a cero.');
        }

        if ((float) $stockMinimoNumero > (float) Limites::STOCK_MINIMO_MAX) {
            throw new \InvalidArgumentException(
                'El stock mínimo supera el máximo permitido ('.Limites::STOCK_MINIMO_MAX.').'
            );
        }

        if (! $this->decimalesValidos($stockMinimoNumero, 2)) {
            throw new \InvalidArgumentException('El stock mínimo admite como máximo 2 decimales.');
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

        $datos = [
            'codigo' => $codigo,
            'nombre' => $nombre,
            'categoria_id' => $categoriaModelo->id,
            'unidad_medida_id' => $unidadModelo->id,
            'stock_minimo' => (float) $stockMinimoNumero,
        ];

        // Las columnas opcionales solo se tocan si el archivo las trae con
        // valor. Si no, el artículo conserva lo que ya tenía.
        if ($descripcion !== null) {
            $datos['descripcion'] = $descripcion;
        }

        if ($controlIndividual !== null) {
            $datos['control_individual'] = $this->booleano($controlIndividual);
        }

        if ($precioVenta !== null && $permitirCostos) {
            $precioNumero = $this->numero($precioVenta, $separador);

            if ($precioNumero === null || (float) $precioNumero < 0) {
                throw new \InvalidArgumentException('El precio de venta debe ser un número igual o mayor a cero.');
            }

            if ((float) $precioNumero > (float) Limites::PRECIO_MAX) {
                throw new \InvalidArgumentException(
                    'El precio de venta supera el máximo permitido ('.Limites::PRECIO_MAX.').'
                );
            }

            if (! $this->decimalesValidos($precioNumero, 2)) {
                throw new \InvalidArgumentException('El precio de venta admite como máximo 2 decimales.');
            }

            $datos['precio_venta'] = (float) $precioNumero;
        }

        return $datos;
    }

    /**
     * Normaliza un número escrito por Excel al formato numérico de PHP.
     *
     * El separador del archivo decide cómo está escrito el decimal: con `;`
     * es la convención española ("1.250,50"), con `,` es la anglosajona
     * ("1,250.50").
     */
    private function numero(?string $valor, string $separador): ?string
    {
        if ($valor === null) {
            return null;
        }

        $limpio = str_replace(' ', '', $valor);

        if ($separador === ';') {
            $limpio = str_replace('.', '', $limpio);
            $limpio = str_replace(',', '.', $limpio);
        } else {
            $limpio = str_replace(',', '', $limpio);
        }

        return is_numeric($limpio) ? $limpio : null;
    }

    /**
     * ¿El número cabe en los decimales de la columna?
     */
    private function decimalesValidos(string $numero, int $maximo): bool
    {
        $parte = strrchr($numero, '.');

        if ($parte === false) {
            return true;
        }

        return strlen(substr($parte, 1)) <= $maximo;
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

        // Solo se actualizan las columnas que trae el archivo: el resto del
        // artículo queda como estaba.
        $existente->update($datos);

        return 'actualizado';
    }
}
