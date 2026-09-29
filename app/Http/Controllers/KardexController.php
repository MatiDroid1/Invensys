<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Movimiento;
use Illuminate\Http\Request;

class KardexController extends Controller
{
    public function index(Request $request)
    {
        $articulos = Articulo::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $articulo = null;
        $movimientos = collect();
        $saldo = 0;

        if ($request->filled('articulo_id')) {
            $articulo = Articulo::where('activo', true)
                ->findOrFail($request->articulo_id);

            $movimientos = Movimiento::with([
                'persona',
                'usuario',
            ])
                ->where('articulo_id', $articulo->id)
                ->orderBy('fecha_movimiento')
                ->orderBy('id')
                ->get();

            $movimientos->each(function ($movimiento) use (&$saldo) {
                $entrada = 0;
                $salida = 0;

                if (in_array($movimiento->tipo, [
                    'ENTRADA',
                    'AJUSTE_POSITIVO',
                ])) {
                    $entrada = (float) $movimiento->cantidad;
                    $saldo += $entrada;
                }

                if (in_array($movimiento->tipo, [
                    'SALIDA',
                    'AJUSTE_NEGATIVO',
                ])) {
                    $salida = (float) $movimiento->cantidad;
                    $saldo -= $salida;
                }

                $movimiento->entrada = $entrada;
                $movimiento->salida = $salida;
                $movimiento->saldo = $saldo;
            });
        }

        return view('kardex.index', compact(
            'articulos',
            'articulo',
            'movimientos'
        ));
    }
}
