<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Movimiento;
use App\Models\Persona;

class DashboardController extends Controller
{
    public function index()
    {
        $articulosActivos = Articulo::where('activo', true)
            ->count();

        $personasActivas = Persona::where('activo', true)
            ->count();

        $inicioMes = now()->startOfMonth();
        $finMes = now()->endOfMonth();

        $movimientosMes = Movimiento::whereBetween(
            'fecha_movimiento',
            [$inicioMes, $finMes]
        )->count();

        $articulosConStock = Articulo::query()
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
            ->get();

        $articulosStockBajo = $articulosConStock
            ->filter(function ($articulo) {
                return (float) $articulo->stock_calculado
                    <= (float) $articulo->stock_minimo;
            })
            ->values();

        $ultimosMovimientos = Movimiento::with([
            'articulo',
            'persona',
            'usuario',
        ])
            ->orderByDesc('fecha_movimiento')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        return view('dashboard', compact(
            'articulosActivos',
            'personasActivas',
            'movimientosMes',
            'articulosStockBajo',
            'ultimosMovimientos'
        ));
    }
}
