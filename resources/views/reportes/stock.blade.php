<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="md-title">Reporte de stock</h2>

                <p class="md-subtitle mt-0.5">
                    Situación actual de cada artículo frente a su stock mínimo.
                </p>
            </div>

            <a href="{{ route('reportes.stock.csv', request()->query()) }}" class="md-btn md-btn-md md-btn-outlined">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>

                Descargar CSV
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            {{-- El filtro se envía solo al cambiar: es una sola casilla y pediría
                 un botón para un clic. --}}
            <form method="GET" action="{{ route('reportes.stock') }}" class="md-card px-5 py-4">
                <label class="flex w-fit cursor-pointer items-center gap-3">
                    <input
                        type="checkbox"
                        name="solo_bajo_minimo"
                        value="1"
                        @checked($soloBajoMinimo)
                        onchange="this.form.submit()"
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900"
                    >

                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                        Mostrar solo artículos bajo mínimo
                    </span>
                </label>
            </form>

            <section class="md-card overflow-hidden">
                @if ($articulos->isEmpty())
                    <x-estado-vacio
                        icono="M3.75 9.75h16.5m-16.5 0V6a2.25 2.25 0 012.25-2.25h12a2.25 2.25 0 012.25 2.25v3.75M3.75 9.75v9.75A2.25 2.25 0 006 21.75h12a2.25 2.25 0 002.25-2.25V9.75M9.75 13.5h4.5"
                        titulo="Sin artículos que mostrar"
                        descripcion="{{ $soloBajoMinimo ? 'Ningún artículo está por debajo de su stock mínimo.' : 'Todavía no hay artículos en el catálogo.' }}"
                    />
                @else
                    <div class="overflow-x-auto">
                        <table class="md-table">
                            <thead>
                                <tr>
                                    <th scope="col">Código</th>
                                    <th scope="col">Artículo</th>
                                    <th scope="col">Categoría</th>
                                    <th scope="col">Unidad</th>
                                    <th scope="col" class="text-right">Stock actual</th>
                                    <th scope="col" class="text-right">Stock mínimo</th>
                                    <th scope="col">Estado</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($articulos as $articulo)
                                    @php
                                        $stockActual = (float) $articulo->stock_calculado;
                                        $stockMinimo = (float) $articulo->stock_minimo;
                                        $stockBajo = $stockActual <= $stockMinimo;
                                    @endphp

                                    <tr>
                                        <td class="font-mono text-xs">{{ $articulo->codigo }}</td>

                                        <td class="font-medium text-gray-900 dark:text-white">
                                            {{ $articulo->nombre }}
                                        </td>

                                        <td>{{ $articulo->categoria->nombre }}</td>
                                        <td>{{ $articulo->unidadMedida->nombre }}</td>

                                        <td @class([
                                            'text-right font-semibold',
                                            'text-red-600 dark:text-red-400' => $stockBajo,
                                            'text-gray-900 dark:text-white' => ! $stockBajo,
                                        ])>
                                            {{ number_format($stockActual, 2, ',', '.') }}
                                        </td>

                                        <td class="text-right text-gray-500 dark:text-gray-400">
                                            {{ number_format($stockMinimo, 2, ',', '.') }}
                                        </td>

                                        <td>
                                            <span @class([
                                                'md-badge',
                                                'md-badge-danger' => $stockBajo,
                                                'md-badge-success' => ! $stockBajo,
                                            ])>
                                                {{ $stockBajo ? 'Bajo mínimo' : 'Normal' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>