<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="md-title">
                Reporte de stock
            </h2>
        </div>
    </x-slot>

    <div class="md-page md-page-body space-y-6">
        <x-alerta />
        <section class="md-card mx-auto w-full max-w-3xl p-5 sm:p-6">
            <form method="GET" action="{{ route('reportes.stock') }}">
                <label class="inline-flex items-center">
                    <input
                        type="checkbox"
                        name="solo_bajo_minimo"
                        value="1"
                        @checked($soloBajoMinimo)
                        onchange="this.form.submit()"
                        class="rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                    >

                    <span class="ms-2 text-sm text-gray-700 dark:text-gray-300">
                        Mostrar solo artículos bajo mínimo
                    </span>
                </label>
            </form>

            <div class="mt-5 flex flex-wrap gap-3">
                <a
                    href="{{ route('reportes.stock.csv', request()->query()) }}"
                    class="md-btn md-btn-filled"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>

                    Descargar CSV
                </a>

                <a
                    href="{{ route('reportes.inventario-valorizado.csv') }}"
                    class="md-btn md-btn-tonal"
                    title="Inventario con existencias y costo, con total general. Pensado para contabilidad."
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m-6 4h6m-6 4h3M6.75 3.75h10.5A2.25 2.25 0 0119.5 6v12a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 18V6a2.25 2.25 0 012.25-2.25z" />
                    </svg>

                    Inventario valorizado (contabilidad)
                </a>
            </div>
        </section>

        <section class="md-card">
            @if ($articulos->isEmpty())

                <x-estado-vacio
                    :descripcion="'No hay artículos que mostrar.'"
                />

            @else

                <x-tabla>
                    <thead>
                        <tr class="md-tr">
                            <th scope="col" class="md-th">
                                Código
                            </th>

                            <th scope="col" class="md-th">
                                Artículo
                            </th>

                            <th scope="col" class="md-th">
                                Categoría
                            </th>

                            <th scope="col" class="md-th">
                                Unidad
                            </th>

                            <th scope="col" class="md-th md-th-num">
                                Stock actual
                            </th>

                            <th scope="col" class="md-th md-th-num">
                                Stock mínimo
                            </th>

                            <th scope="col" class="md-th md-th-num">
                                Costo promedio
                            </th>

                            <th scope="col" class="md-th md-th-num">
                                Precio venta
                            </th>

                            <th scope="col" class="md-th md-th-num">
                                Margen
                            </th>

                            <th scope="col" class="md-th md-th-num">
                                Valor inventario
                            </th>

                            <th scope="col" class="md-th">
                                Estado
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($articulos as $articulo)
                            @php
                                $stockActual = (float) $articulo->stock_calculado;
                                $stockMinimo = (float) $articulo->stock_minimo;
                                $stockBajo = $stockActual <= $stockMinimo;
                            @endphp

                            <tr class="md-tr">
                                <td class="md-td font-mono text-gray-500 dark:text-gray-400">
                                    {{ $articulo->codigo }}
                                </td>

                                <td class="md-td md-td-strong">
                                    <span class="md-td-clip" title="{{ $articulo->nombre }}">
                                        {{ $articulo->nombre }}
                                    </span>
                                </td>

                                <td class="md-td">
                                    {{ $articulo->categoria->nombre }}
                                </td>

                                <td class="md-td">
                                    {{ $articulo->unidadMedida->nombre }}
                                </td>

                                <td class="md-td md-td-num font-semibold">
                                    {{ number_format($stockActual, 2, ',', '.') }}
                                </td>

                                <td class="md-td md-td-num">
                                    {{ number_format($stockMinimo, 2, ',', '.') }}
                                </td>

                                <td class="md-td md-td-num">
                                    <x-dinero :monto="$articulo->costo_promedio !== null ? (float) $articulo->costo_promedio : null" />
                                </td>

                                <td class="md-td md-td-num">
                                    <x-dinero :monto="$articulo->precio_venta !== null ? (float) $articulo->precio_venta : null" />
                                </td>

                                <td class="md-td md-td-num">
                                    @if ($articulo->margen === null)
                                        <span class="text-gray-400 dark:text-gray-500" title="Falta costo o precio de venta">—</span>
                                    @else
                                        <x-dinero :monto="$articulo->margen" />
                                        <span class="block text-xs text-gray-400 dark:text-gray-500">
                                            {{ number_format($articulo->margen_porcentaje, 1, ',', '.') }}%
                                        </span>
                                    @endif
                                </td>

                                <td class="md-td md-td-num md-td-strong">
                                    <x-dinero :monto="$articulo->valor_inventario" />
                                </td>

                                <td class="md-td">
                                    @if ($stockBajo)
                                        <span class="md-badge md-badge-danger">
                                            Bajo mínimo
                                        </span>
                                    @else
                                        <span class="md-badge md-badge-success">
                                            Normal
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr class="md-tr">
                            <td class="md-td md-td-strong" colspan="9">
                                Valor total del inventario
                            </td>

                            <td class="md-td md-td-num md-td-strong">
                                <x-dinero :monto="$valorTotal" />
                            </td>

                            <td class="md-td"></td>
                        </tr>
                    </tfoot>
                </x-tabla>

            @endif
        </section>
    </div>
</x-app-layout>