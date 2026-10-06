<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Kardex {{ $articulo->codigo }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1f2937;
            margin: 0;
            padding: 24px;
        }

        h1 {
            font-size: 16px;
            margin: 0 0 4px;
        }

        .marca {
            color: #6b7280;
            font-size: 10px;
            margin-bottom: 18px;
        }

        .datos {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .datos td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
        }

        .datos .etiqueta {
            background: #f3f4f6;
            width: 22%;
            font-weight: bold;
        }

        table.movimientos {
            width: 100%;
            border-collapse: collapse;
        }

        table.movimientos th,
        table.movimientos td {
            border: 1px solid #d1d5db;
            padding: 5px 6px;
            text-align: left;
        }

        table.movimientos th {
            background: #111827;
            color: #ffffff;
            font-size: 10px;
        }

        .num {
            text-align: right;
        }

        .sin-movimientos {
            padding: 14px;
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            text-align: center;
        }

        .pie {
            margin-top: 18px;
            font-size: 9px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Kardex de inventario — {{ $articulo->nombre }}</h1>

    <div class="marca">
        Invensys · Generado el {{ now()->format('d/m/Y H:i') }}
    </div>

    <table class="datos">
        <tr>
            <td class="etiqueta">Código</td>
            <td>{{ $articulo->codigo }}</td>
            <td class="etiqueta">Categoría</td>
            <td>{{ $articulo->categoria?->nombre ?? '-' }}</td>
        </tr>
        <tr>
            <td class="etiqueta">Stock mínimo</td>
            <td>{{ number_format($articulo->stock_minimo, 2, ',', '.') }}</td>
            <td class="etiqueta">Stock actual</td>
            <td>{{ number_format($articulo->stock_actual, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="etiqueta">Unidad de medida</td>
            <td>{{ $articulo->unidadMedida?->nombre ?? '-' }}</td>
            <td class="etiqueta">Movimientos</td>
            <td>{{ $movimientos->count() }}</td>
        </tr>
    </table>

    @if ($movimientos->isEmpty())
        <div class="sin-movimientos">
            Este artículo no tiene movimientos registrados.
        </div>
    @else
        <table class="movimientos">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Referencia</th>
                    <th>Persona</th>
                    <th class="num">Entrada</th>
                    <th class="num">Salida</th>
                    <th class="num">Saldo</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($movimientos as $movimiento)
                    <tr>
                        <td>{{ $movimiento->fecha_movimiento->format('d/m/Y H:i') }}</td>
                        <td>{{ $movimiento->tipo }}</td>
                        <td>{{ $movimiento->referencia ?? '-' }}</td>
                        <td>{{ $movimiento->persona?->nombre_completo ?? '-' }}</td>
                        <td class="num">
                            {{ $movimiento->entrada > 0 ? number_format($movimiento->entrada, 2, ',', '.') : '-' }}
                        </td>
                        <td class="num">
                            {{ $movimiento->salida > 0 ? number_format($movimiento->salida, 2, ',', '.') : '-' }}
                        </td>
                        <td class="num"><strong>{{ number_format($movimiento->saldo, 2, ',', '.') }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="pie">
        Documento generado por Invensys · Sistema de control de inventario
    </div>
</body>
</html>
