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