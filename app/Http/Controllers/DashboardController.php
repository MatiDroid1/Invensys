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

        $articulosStockBajo = Articulo::stockBajo();

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
