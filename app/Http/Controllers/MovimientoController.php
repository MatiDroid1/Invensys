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
    public function index(Request $request)
    {
        $movimientos = Movimiento::with([
            'articulo',
            'usuario',
            'persona',
            'anuladoPor',
        ])
            ->when($request->filled('articulo_id'), function ($query) use ($request) {
                $query->where('articulo_id', $request->integer('articulo_id'));
            })
            ->when($request->filled('tipo'), function ($query) use ($request) {
                $query->where('tipo', $request->string('tipo'));
            })
            ->when($request->filled('persona_id'), function ($query) use ($request) {
                $query->where('persona_id', $request->integer('persona_id'));
            })
            ->when($request->filled('desde'), function ($query) use ($request) {
                $query->whereDate('fecha_movimiento', '>=', $request->date('desde'));
            })
            ->when($request->filled('hasta'), function ($query) use ($request) {
                $query->whereDate('fecha_movimiento', '<=', $request->date('hasta'));
            })
            ->orderByDesc('fecha_movimiento')
            ->orderByDesc('id')
            ->paginate(50)
            ->withQueryString();

        $articulos = Articulo::orderBy('nombre')
            ->get(['id', 'codigo', 'nombre']);

        $personas = Persona::where('activo', true)
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'apellido']);

        return view('movimientos.index', compact('movimientos', 'articulos', 'personas'));
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
            costoUnitario: isset($datos['costo_unitario'])
                ? (float) $datos['costo_unitario']
                : null,
            referencia: $datos['referencia'] ?? null,
            observaciones: $datos['observaciones'] ?? null,
            usuarioId: auth()->id(),
            idempotencyKey: $request->input('idempotency_key'),
        );

        return redirect()
            ->route('movimientos.index')
            ->with('success', 'Entrada registrada correctamente.');
    }

    public function createSalida(Request $request)
    {
        $articulos = Articulo::query()
            ->conStock()
            ->where('activo', true)
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
            idempotencyKey: $request->input('idempotency_key'),
        );

        return redirect()
            ->route('movimientos.index')
            ->with('success', 'Salida registrada correctamente.');
    }

    public function createAjuste(Request $request)
    {
        $articulos = Articulo::query()
            ->conStock()
            ->where('activo', true)
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
            costoUnitario: isset($datos['costo_unitario'])
                ? (float) $datos['costo_unitario']
                : null,
            referencia: $datos['referencia'] ?? null,
            observaciones: $datos['observaciones'] ?? null,
            usuarioId: auth()->id(),
            idempotencyKey: $request->input('idempotency_key'),
        );

        return redirect()
            ->route('movimientos.index')
            ->with('success', 'Ajuste de inventario registrado correctamente.');
    }

    /**
     * Anula un movimiento confirmado.
     *
     * El movimiento no se borra: queda marcado como anulado y se genera un
     * movimiento compensatorio, de forma que el historial cuadra y la
     * anulación queda auditable.
     */
    public function anular(
        Movimiento $movimiento,
        Request $request,
        MovimientoService $movimientoService
    ) {
        $datos = $request->validate([
            'motivo' => ['required', 'string', 'max:150'],
        ], [], [
            'motivo' => 'motivo',
        ]);

        $movimientoService->anularMovimiento(
            movimientoId: $movimiento->id,
            motivo: $datos['motivo'],
            usuarioId: auth()->id(),
        );

        return redirect()
            ->route('movimientos.index')
            ->with('success', 'Movimiento anulado correctamente.');
    }
}
