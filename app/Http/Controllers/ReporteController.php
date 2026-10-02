<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Movimiento;
use App\Models\Persona;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
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

        $csv = fopen('php://temp', 'r+');

        fputcsv($csv, [
            'Código',
            'Artículo',
            'Categoría',
            'Unidad de medida',
            'Stock actual',
            'Stock mínimo',
            'Stock máximo',
            'Estado',
        ]);

        foreach ($articulos as $articulo) {
            fputcsv($csv, [
                $articulo->codigo,
                $articulo->nombre,
                $articulo->categoria?->nombre,
                $articulo->unidadMedida?->nombre,
                (float) $articulo->stock_calculado,
                $articulo->stock_minimo,
                $articulo->stock_maximo,
                $articulo->activo ? 'Activo' : 'Inactivo',
            ]);
        }

        rewind($csv);
        $contenido = stream_get_contents($csv);
        fclose($csv);

        return response($contenido, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$nombreArchivo}\"",
        ]);
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

        $csv = fopen('php://temp', 'r+');

        fputcsv($csv, [
            'Fecha',
            'Hora',
            'Artículo (código)',
            'Artículo',
            'Tipo',
            'Cantidad',
            'Persona',
            'Usuario',
            'Referencia',
        ]);

        foreach ($movimientos as $movimiento) {
            $fechaMovimiento = $movimiento->fecha_movimiento;

            fputcsv($csv, [
                $fechaMovimiento?->format('d/m/Y'),
                $fechaMovimiento?->format('H:i'),
                $movimiento->articulo?->codigo,
                $movimiento->articulo?->nombre,
                $movimiento->tipo,
                (float) $movimiento->cantidad,
                $movimiento->persona
                    ? trim($movimiento->persona->nombre.' '.$movimiento->persona->apellido)
                    : null,
                $movimiento->usuario?->name,
                $movimiento->referencia,
            ]);
        }

        rewind($csv);
        $contenido = stream_get_contents($csv);
        fclose($csv);

        return response($contenido, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$nombreArchivo}\"",
        ]);
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

        $csv = fopen('php://temp', 'r+');

        fputcsv($csv, [
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
        ]);

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

                fputcsv($csv, [
                    trim($persona->nombre.' '.$persona->apellido),
                    $persona->rut,
                    $fechaMovimiento?->format('d/m/Y'),
                    $fechaMovimiento?->format('H:i'),
                    $movimiento->articulo?->codigo,
                    $movimiento->articulo?->nombre,
                    $movimiento->tipo,
                    (float) $movimiento->cantidad,
                    $movimiento->usuario?->name,
                    $movimiento->referencia,
                ]);
            }
        }

        rewind($csv);
        $contenido = stream_get_contents($csv);
        fclose($csv);

        return response($contenido, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$nombreArchivo}\"",
        ]);
    }
}
