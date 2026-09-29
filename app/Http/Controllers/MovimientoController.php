<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAjusteRequest;
use App\Http\Requests\StoreMovimientoRequest;
use App\Http\Requests\StoreSalidaRequest;
use App\Models\Articulo;
use App\Models\Movimiento;
use App\Models\Persona;
use App\Services\MovimientoService;
use Illuminate\Http\Request;

class MovimientoController extends Controller
{
    public function index()
    {
        $movimientos = Movimiento::with([
            'articulo',
            'usuario',
            'persona',
        ])
            ->orderByDesc('fecha_movimiento')
            ->get();

        return view('movimientos.index', compact('movimientos'));
    }

    public function create(Request $request)
    {
        $articulos = Articulo::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $articuloPreseleccionado = $request->filled('articulo_id')
            ? $articulos->firstWhere('id', $request->integer('articulo_id'))
            : null;

        return view('movimientos.create', compact('articulos', 'articuloPreseleccionado'));
    }

    public function store(
        StoreMovimientoRequest $request,
        MovimientoService $movimientoService
    ) {
        $datos = $request->validated();

        $movimientoService->registrarEntrada(
            articuloId: $datos['articulo_id'],
            cantidad: (float) $datos['cantidad'],
            fechaMovimiento: $datos['fecha_movimiento'],
            referencia: $datos['referencia'] ?? null,
            observaciones: $datos['observaciones'] ?? null,
            usuarioId: auth()->id(),
        );

        return redirect()
            ->route('movimientos.index')
            ->with('success', 'Entrada registrada correctamente.');
    }

    public function createSalida(Request $request)
    {
        $articulos = Articulo::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $personas = Persona::where('activo', true)
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->get();

        $articuloPreseleccionado = $request->filled('articulo_id')
            ? $articulos->firstWhere('id', $request->integer('articulo_id'))
            : null;

        return view('movimientos.create-salida', compact(
            'articulos',
            'personas',
            'articuloPreseleccionado'
        ));
    }

    public function storeSalida(
        StoreSalidaRequest $request,
        MovimientoService $movimientoService
    ) {
        $datos = $request->validated();

        $movimientoService->registrarSalida(
            articuloId: $datos['articulo_id'],
            personaId: $datos['persona_id'],
            cantidad: (float) $datos['cantidad'],
            fechaMovimiento: $datos['fecha_movimiento'],
            referencia: $datos['referencia'] ?? null,
            observaciones: $datos['observaciones'] ?? null,
            usuarioId: auth()->id(),
        );

        return redirect()
            ->route('movimientos.index')
            ->with('success', 'Salida registrada correctamente.');
    }

    public function createAjuste(Request $request)
    {
        $articulos = Articulo::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $articuloPreseleccionado = $request->filled('articulo_id')
            ? $articulos->firstWhere('id', $request->integer('articulo_id'))
            : null;

        return view('movimientos.create-ajuste', compact('articulos', 'articuloPreseleccionado'));
    }

    public function storeAjuste(
        StoreAjusteRequest $request,
        MovimientoService $movimientoService
    ) {
        $datos = $request->validated();

        $movimientoService->registrarAjuste(
            articuloId: $datos['articulo_id'],
            tipo: $datos['tipo'],
            cantidad: (float) $datos['cantidad'],
            fechaMovimiento: $datos['fecha_movimiento'],
            referencia: $datos['referencia'] ?? null,
            observaciones: $datos['observaciones'] ?? null,
            usuarioId: auth()->id(),
        );

        return redirect()
            ->route('movimientos.index')
            ->with('success', 'Ajuste de inventario registrado correctamente.');
    }
}
