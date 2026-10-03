<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Reporte de stock
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">

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

                            <span class="ms-2">
                                Mostrar solo artículos bajo mínimo
                            </span>
                        </label>
                    </form>

                    <div class="mt-4">
                        <a
                            href="{{ route('reportes.stock.csv', request()->query()) }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 text-sm font-medium rounded-md hover:bg-gray-700 dark:hover:bg-white transition"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4 me-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>

                            Descargar CSV
                        </a>
                    </div>

                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if ($articulos->isEmpty())

                        <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-md">
                            No hay artículos que mostrar.
                        </div>

                    @else

                        <div class="overflow-x-auto">
                            <table class="min-w-full">

                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
                                        <th class="px-4 py-3 text-left">
                                            Código
                                        </th>

                                        <th class="px-4 py-3 text-left">
                                            Artículo
                                        </th>

                                        <th class="px-4 py-3 text-left">
                                            Categoría
                                        </th>

                                        <th class="px-4 py-3 text-left">
                                            Unidad
                                        </th>

                                        <th class="px-4 py-3 text-right">
                                            Stock actual
                                        </th>

                                        <th class="px-4 py-3 text-right">
                                            Stock mínimo
                                        </th>

                                        <th class="px-4 py-3 text-left">
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

                                        <tr class="border-b border-gray-200 dark:border-gray-700">

                                            <td class="px-4 py-3">
                                                {{ $articulo->codigo }}
                                            </td>

                                            <td class="px-4 py-3 font-medium">
                                                {{ $articulo->nombre }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $articulo->categoria->nombre }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $articulo->unidadMedida->nombre }}
                                            </td>

                                            <td class="px-4 py-3 text-right font-semibold">
                                                {{ number_format($stockActual, 2, ',', '.') }}
                                            </td>

                                            <td class="px-4 py-3 text-right">
                                                {{ number_format($stockMinimo, 2, ',', '.') }}
                                            </td>

                                            <td class="px-4 py-3">
                                                @if ($stockBajo)
                                                    <span class="text-red-600 font-semibold">
                                                        Bajo mínimo
                                                    </span>
                                                @else
                                                    <span class="text-green-600 font-semibold">
                                                        Normal
                                                    </span>
                                                @endif
                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>
                        </div>

                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>