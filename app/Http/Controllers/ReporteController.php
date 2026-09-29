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

    public function movimientos(Request $request)
    {
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $articuloId = $request->input('articulo_id');
        $tipo = $request->input('tipo');

        $articulos = Articulo::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $movimientosQuery = Movimiento::with([
            'articulo',
            'persona',
            'usuario',
        ])->orderByDesc('fecha_movimiento')
            ->orderByDesc('id');

        if ($fechaDesde) {
            $movimientosQuery->whereDate(
                'fecha_movimiento',
                '>=',
                $fechaDesde
            );
        }

        if ($fechaHasta) {
            $movimientosQuery->whereDate(
                'fecha_movimiento',
                '<=',
                $fechaHasta
            );
        }

        if ($articuloId) {
            $movimientosQuery->where(
                'articulo_id',
                $articuloId
            );
        }

        if ($tipo) {
            $movimientosQuery->where(
                'tipo',
                $tipo
            );
        }

        $movimientos = $movimientosQuery->get();

        return view('reportes.movimientos', compact(
            'movimientos',
            'articulos',
            'fechaDesde',
            'fechaHasta',
            'articuloId',
            'tipo'
        ));
    }

    public function entregasPersona(Request $request)
    {
        $personaId = $request->input('persona_id');
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');

        $personas = Persona::where('activo', true)
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->get();

        $query = Movimiento::with([
            'articulo',
            'persona',
            'usuario',
        ])
            ->where('tipo', 'SALIDA')
            ->orderByDesc('fecha_movimiento')
            ->orderByDesc('id');

        if ($personaId) {
            $query->where('persona_id', $personaId);
        }

        if ($fechaDesde) {
            $query->whereDate(
                'fecha_movimiento',
                '>=',
                $fechaDesde
            );
        }

        if ($fechaHasta) {
            $query->whereDate(
                'fecha_movimiento',
                '<=',
                $fechaHasta
            );
        }

        $movimientos = $query->get();

        return view('reportes.entregas-persona', compact(
            'movimientos',
            'personas',
            'personaId',
            'fechaDesde',
            'fechaHasta'
        ));
    }
}
