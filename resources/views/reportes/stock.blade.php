<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="md-title">
                Reporte de stock
            </h2>
        </div>
    </x-slot>

    <div class="md-page md-page-body space-y-6">
        <section class="md-card p-5 sm:p-6">
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

            <div class="mt-5">
                <a
                    href="{{ route('reportes.stock.csv', request()->query()) }}"
                    class="md-btn md-btn-filled"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>

                    Descargar CSV
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
                </x-tabla>

            @endif
        </section>
    </div>
</x-app-layout>