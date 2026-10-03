<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Movimiento;
use App\Models\Persona;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    /**
     * Configuración común de las descargas CSV.
     *
     * El separador es punto y coma porque es el que espera Excel en
     * configuración regional española: con la coma, un texto que contenga una
     * coma se abre en columnas de más y los decimales con punto se parten.
     *
     * El BOM es lo que hace que Excel entienda que el archivo es UTF-8. Sin él
     * abre el CSV con la codificación ANSI del sistema y los acentos salen
     * descodificados, aunque el Content-Type y el propio archivo sean UTF-8
     * correctos.
     */
    private const CSV_SEPARADOR = ';';

    private const CSV_FIN_DE_LINEA = "\r\n";

    private const CSV_BOM = "\xEF\xBB\xBF";

    public function stock(Request $request)
    {
        $articulos = Articulo::query()
            ->with([
                'categoria',
                'unidadMedida',
            ])
            ->select('articulos.*')
            ->selectSub(
                Movimiento::selectRaw("
                    COALESCE(
                        SUM(
                            CASE
                                WHEN tipo IN ('ENTRADA', 'AJUSTE_POSITIVO') THEN cantidad
                                WHEN tipo IN ('SALIDA', 'AJUSTE_NEGATIVO') THEN -cantidad
                                ELSE 0
                            END
                        ),
                        0
                    )
                ")
                    ->whereColumn(
                        'movimientos.articulo_id',
                        'articulos.id'
                    ),
                'stock_calculado'
            )
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $soloBajoMinimo = $request->boolean('solo_bajo_minimo');

        if ($soloBajoMinimo) {
            $articulos = $articulos
                ->filter(function ($articulo) {
                    return (float) $articulo->stock_calculado
                        <= (float) $articulo->stock_minimo;
                })
                ->values();
        }

        return view('reportes.stock', compact(
            'articulos',
            'soloBajoMinimo'
        ));
    }

    public function stockCsv(Request $request)
    {
        $articulos = Articulo::query()
            ->with([
                'categoria',
                'unidadMedida',
            ])
            ->select('articulos.*')
            ->selectSub(
                Movimiento::selectRaw("
                    COALESCE(
                        SUM(
                            CASE
                                WHEN tipo IN ('ENTRADA', 'AJUSTE_POSITIVO') THEN cantidad
                                WHEN tipo IN ('SALIDA', 'AJUSTE_NEGATIVO') THEN -cantidad
                                ELSE 0
                            END
                        ),
                        0
                    )
                ")
                    ->whereColumn(
                        'movimientos.articulo_id',
                        'articulos.id'
                    ),
                'stock_calculado'
            )
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $soloBajoMinimo = $request->boolean('solo_bajo_minimo');

        if ($soloBajoMinimo) {
            $articulos = $articulos
                ->filter(function ($articulo) {
                    return (float) $articulo->stock_calculado
                        <= (float) $articulo->stock_minimo;
                })
                ->values();
        }

        $nombreArchivo = $soloBajoMinimo
            ? 'stock_bajo_minimo_'.now()->format('Y-m-d_H-i-s').'.csv'
            : 'stock_actual_'.now()->format('Y-m-d_H-i-s').'.csv';

        $lineas = [[
            'Código',
            'Artículo',
            'Categoría',
            'Unidad de medida',
            'Stock actual',
            'Stock mínimo',
            'Stock máximo',
            'Estado',
        ]];

        foreach ($articulos as $articulo) {
            $lineas[] = [
                $articulo->codigo,
                $articulo->nombre,
                $articulo->categoria?->nombre,
                $articulo->unidadMedida?->nombre,
                $this->numeroEs($articulo->stock_calculado),
                $this->numeroEs($articulo->stock_minimo),
                $this->numeroEs($articulo->stock_maximo),
                $articulo->activo ? 'Activo' : 'Inactivo',
            ];
        }

        return $this->respuestaCsv($lineas, $nombreArchivo);
    }

    public function movimientos(Request $request)
    {
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $articuloId = $request->input('articulo_id');
        $tipo = $request->input('tipo');

        $articulos = Articulo::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $movimientos = Movimiento::query()
            ->with([
                'articulo:id,codigo,nombre',
                'persona:id,nombre,apellido',
                'usuario:id,name',
            ])
            ->when($fechaDesde, function ($query, $fechaDesde) {
                return $query->whereDate('fecha_movimiento', '>=', $fechaDesde);
            })
            ->when($fechaHasta, function ($query, $fechaHasta) {
                return $query->whereDate('fecha_movimiento', '<=', $fechaHasta);
            })
            ->when($articuloId, function ($query, $articuloId) {
                return $query->where('articulo_id', $articuloId);
            })
            ->when($tipo, function ($query, $tipo) {
                return $query->where('tipo', $tipo);
            })
            ->orderBy('fecha_movimiento', 'desc')
            ->paginate(50);

        return view('reportes.movimientos', compact(
            'movimientos',
            'articulos',
            'fechaDesde',
            'fechaHasta',
            'articuloId',
            'tipo'
        ));
    }

    public function movimientosCsv(Request $request)
    {
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $articuloId = $request->input('articulo_id');
        $tipo = $request->input('tipo');

        $movimientos = Movimiento::query()
            ->with([
                'articulo:id,codigo,nombre',
                'persona:id,nombre,apellido',
                'usuario:id,name',
            ])
            ->when($fechaDesde, function ($query, $fechaDesde) {
                return $query->whereDate('fecha_movimiento', '>=', $fechaDesde);
            })
            ->when($fechaHasta, function ($query, $fechaHasta) {
                return $query->whereDate('fecha_movimiento', '<=', $fechaHasta);
            })
            ->when($articuloId, function ($query, $articuloId) {
                return $query->where('articulo_id', $articuloId);
            })
            ->when($tipo, function ($query, $tipo) {
                return $query->where('tipo', $tipo);
            })
            ->orderBy('fecha_movimiento', 'desc')
            ->get();

        $nombreArchivo = 'movimientos_'.now()->format('Y-m-d_H-i-s').'.csv';

        $lineas = [[
            'Fecha',
            'Hora',
            'Artículo (código)',
            'Artículo',
            'Tipo',
            'Cantidad',
            'Persona',
            'Usuario',
            'Referencia',
        ]];

        foreach ($movimientos as $movimiento) {
            $fechaMovimiento = $movimiento->fecha_movimiento;

            $lineas[] = [
                $fechaMovimiento?->format('d/m/Y'),
                $fechaMovimiento?->format('H:i'),
                $movimiento->articulo?->codigo,
                $movimiento->articulo?->nombre,
                $movimiento->tipo,
                $this->numeroEs($movimiento->cantidad),
                $movimiento->persona
                    ? trim($movimiento->persona->nombre.' '.$movimiento->persona->apellido)
                    : null,
                $movimiento->usuario?->name,
                $movimiento->referencia,
            ];
        }

        return $this->respuestaCsv($lineas, $nombreArchivo);
    }

    public function entregasPersona(Request $request)
    {
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $personaId = $request->input('persona_id');

        $personas = Persona::where('activo', true)
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->get();

        $movimientosQuery = Movimiento::query()
            ->with([
                'articulo:id,codigo,nombre',
                'persona:id,nombre,apellido',
                'usuario:id,name',
            ])
            ->whereNotNull('persona_id')
            ->when($fechaDesde, function ($query, $fechaDesde) {
                return $query->whereDate('fecha_movimiento', '>=', $fechaDesde);
            })
            ->when($fechaHasta, function ($query, $fechaHasta) {
                return $query->whereDate('fecha_movimiento', '<=', $fechaHasta);
            })
            ->when($personaId, function ($query, $personaId) {
                return $query->where('persona_id', $personaId);
            })
            ->orderBy('fecha_movimiento', 'desc');

        $movimientos = $movimientosQuery->paginate(50);

        return view('reportes.entregas_persona', compact(
            'movimientos',
            'personas',
            'fechaDesde',
            'fechaHasta',
            'personaId'
        ));
    }

    public function entregasPersonaCsv(Request $request)
    {
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $personaId = $request->input('persona_id');

        $personas = Persona::where('activo', true)
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->get();

        $nombreArchivo = 'entregas_por_persona_'.now()->format('Y-m-d_H-i-s').'.csv';

        $lineas = [[
            'Persona',
            'Rut',
            'Fecha',
            'Hora',
            'Artículo (código)',
            'Artículo',
            'Tipo',
            'Cantidad',
            'Usuario',
            'Referencia',
        ]];

        foreach ($personas as $persona) {
            $movimientos = Movimiento::query()
                ->with([
                    'articulo:id,codigo,nombre',
                    'usuario:id,name',
                ])
                ->where('persona_id', $persona->id)
                ->when($fechaDesde, function ($query, $fechaDesde) {
                    return $query->whereDate('fecha_movimiento', '>=', $fechaDesde);
                })
                ->when($fechaHasta, function ($query, $fechaHasta) {
                    return $query->whereDate('fecha_movimiento', '<=', $fechaHasta);
                })
                ->when($personaId && $personaId == $persona->id, function ($query) {
                    return $query;
                })
                ->orderBy('fecha_movimiento', 'desc')
                ->get();

            if ($movimientos->isEmpty()) {
                continue;
            }

            foreach ($movimientos as $movimiento) {
                $fechaMovimiento = $movimiento->fecha_movimiento;

                $lineas[] = [
                    trim($persona->nombre.' '.$persona->apellido),
                    $persona->rut,
                    $fechaMovimiento?->format('d/m/Y'),
                    $fechaMovimiento?->format('H:i'),
                    $movimiento->articulo?->codigo,
                    $movimiento->articulo?->nombre,
                    $movimiento->tipo,
                    $this->numeroEs($movimiento->cantidad),
                    $movimiento->usuario?->name,
                    $movimiento->referencia,
                ];
            }
        }

        return $this->respuestaCsv($lineas, $nombreArchivo);
    }

    /**
     * Arma el CSV para Excel en español.
     *
     * `fputcsv` con sus valores por defecto escribe coma como separador, salto
     * `\n` y sin marca de orden de bytes: abierto en Excel en español eso
     * significa una sola columna con todo el archivo amontonado, acentos rotos
     * y las filas pegadas en un renglón. Aquí se escribe a mano con `;`, CRLF y
     * BOM UTF-8.
     *
     * @param  array<int, array<int, mixed>>  $lineas
     */
    private function respuestaCsv(array $lineas, string $nombreArchivo)
    {
        $contenido = '';

        foreach ($lineas as $campos) {
            $contenido .= implode(';', array_map(function ($campo) {
                return '"'.str_replace('"', '""', (string) $campo).'"';
            }, $campos))."\r\n";
        }

        return response("\xEF\xBB\xBF".$contenido, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$nombreArchivo}\"",
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
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
