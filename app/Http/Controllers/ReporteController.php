<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ExportaCsv;
use App\Models\Articulo;
use App\Models\Movimiento;
use App\Models\Persona;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    use ExportaCsv;

    public function stock(Request $request)
    {
        $articulos = Articulo::query()
            ->conStock()
            ->with([
                'categoria',
                'unidadMedida',
            ])
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

        $valorTotal = $articulos->sum(
            fn ($articulo) => $articulo->valor_inventario ?? 0
        );

        return view('reportes.stock', compact(
            'articulos',
            'soloBajoMinimo',
            'valorTotal'
        ));
    }

    public function stockCsv(Request $request)
    {
        $articulos = Articulo::query()
            ->conStock()
            ->with([
                'categoria',
                'unidadMedida',
            ])
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
            'Costo promedio',
            'Precio de venta',
            'Margen unitario',
            'Margen %',
            'Valor inventario',
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
                $articulo->costo_promedio !== null
                    ? $this->numeroEs($articulo->costo_promedio)
                    : null,
                $articulo->precio_venta !== null
                    ? $this->numeroEs($articulo->precio_venta)
                    : null,
                $articulo->margen !== null
                    ? $this->numeroEs($articulo->margen)
                    : null,
                $articulo->margen_porcentaje !== null
                    ? $this->numeroEs($articulo->margen_porcentaje)
                    : null,
                $articulo->valor_inventario !== null
                    ? $this->numeroEs($articulo->valor_inventario)
                    : null,
                $articulo->activo ? 'Activo' : 'Inactivo',
            ];
        }

        return $this->respuestaCsv($lineas, $nombreArchivo);
    }

    /**
     * Inventario valorizado para el contador.
     *
     * A diferencia del reporte de stock, aquí solo interesan los artículos con
     * existencias y costo conocido: es el detalle que respalda el asiento de
     * inventario a fin de período. El costo es el promedio ponderado; el valor
     * es cantidad × costo. Se cierra con el total general.
     */
    public function inventarioValorizadoCsv(Request $request)
    {
        $articulos = Articulo::query()
            ->conStock()
            ->with([
                'categoria',
                'unidadMedida',
            ])
            ->where('activo', true)
            ->orderBy('nombre')
            ->get()
            ->filter(fn ($articulo) => (float) $articulo->stock_calculado > 0
                && $articulo->costo_promedio !== null)
            ->values();

        $nombreArchivo = 'inventario_valorizado_'.now()->format('Y-m-d_H-i-s').'.csv';

        $lineas = [[
            'Código',
            'Artículo',
            'Categoría',
            'Unidad de medida',
            'Cantidad',
            'Costo unitario',
            'Valor',
        ]];

        $valorTotal = 0.0;

        foreach ($articulos as $articulo) {
            $cantidad = (float) $articulo->stock_calculado;
            $costo = (float) $articulo->costo_promedio;
            $valor = $cantidad * $costo;

            $valorTotal += $valor;

            $lineas[] = [
                $articulo->codigo,
                $articulo->nombre,
                $articulo->categoria?->nombre,
                $articulo->unidadMedida?->nombre,
                $this->numeroEs($cantidad),
                $this->numeroEs($costo),
                $this->numeroEs($valor),
            ];
        }

        $lineas[] = [];
        $lineas[] = [
            '',
            'TOTAL',
            '',
            '',
            '',
            '',
            $this->numeroEs($valorTotal),
        ];

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
            ->paginate(50)
            ->withQueryString();

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

        $movimientos = $movimientosQuery->paginate(50)->withQueryString();

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

        $movimientos = Movimiento::query()
            ->with([
                'articulo:id,codigo,nombre',
                'persona:id,nombre,apellido,identificador',
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
            ->orderBy('fecha_movimiento', 'desc')
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

        foreach ($movimientos as $movimiento) {
            $fechaMovimiento = $movimiento->fecha_movimiento;
            $persona = $movimiento->persona;

            $lineas[] = [
                $persona ? trim($persona->nombre.' '.$persona->apellido) : null,
                $persona?->identificador,
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

        return $this->respuestaCsv($lineas, $nombreArchivo);
    }
}
