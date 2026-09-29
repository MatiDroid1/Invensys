<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Kardex de inventario
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="GET" action="{{ route('kardex.index') }}">
                        <div class="flex flex-col md:flex-row md:items-end gap-4">

                            <div class="flex-1">
                                <label
                                    for="articulo_id"
                                    class="block font-medium text-sm"
                                >
                                    Artículo
                                </label>

                                <select
                                    name="articulo_id"
                                    id="articulo_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                >
                                    <option value="">
                                        Seleccione un artículo
                                    </option>

                                    @foreach ($articulos as $item)
                                        <option
                                            value="{{ $item->id }}"
                                            @selected($articulo?->id == $item->id)
                                        >
                                            {{ $item->codigo }} - {{ $item->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700"
                                >
                                    Consultar Kardex
                                </button>
                            </div>

                        </div>
                    </form>

                </div>
            </div>

            @if ($articulo)

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    Código
                                </div>

                                <div class="font-semibold">
                                    {{ $articulo->codigo }}
                                </div>
                            </div>

                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    Artículo
                                </div>

                                <div class="font-semibold">
                                    {{ $articulo->nombre }}
                                </div>
                            </div>

                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    Stock mínimo
                                </div>

                                <div class="font-semibold">
                                    {{ number_format($articulo->stock_minimo, 2, ',', '.') }}
                                </div>
                            </div>

                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    Stock actual
                                </div>

                                <div class="font-semibold text-lg">
                                    {{ number_format($articulo->stock_actual, 2, ',', '.') }}
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">

                        @if ($movimientos->isEmpty())

                            <p>
                                Este artículo no tiene movimientos registrados.
                            </p>

                        @else

                            <div class="overflow-x-auto">
                                <table class="min-w-full">

                                    <thead>
                                        <tr class="border-b border-gray-200 dark:border-gray-700">
                                            <th class="px-4 py-3 text-left">
                                                Fecha
                                            </th>

                                            <th class="px-4 py-3 text-left">
                                                Tipo
                                            </th>

                                            <th class="px-4 py-3 text-left">
                                                Referencia
                                            </th>

                                            <th class="px-4 py-3 text-left">
                                                Persona
                                            </th>

                                            <th class="px-4 py-3 text-right">
                                                Entrada
                                            </th>

                                            <th class="px-4 py-3 text-right">
                                                Salida
                                            </th>

                                            <th class="px-4 py-3 text-right">
                                                Saldo
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @foreach ($movimientos as $movimiento)

                                            <tr class="border-b border-gray-200 dark:border-gray-700">

                                                <td class="px-4 py-3">
                                                    {{ $movimiento->fecha_movimiento->format('d/m/Y H:i') }}
                                                </td>

                                                <td class="px-4 py-3">
                                                    {{ $movimiento->tipo }}
                                                </td>

                                                <td class="px-4 py-3">
                                                    {{ $movimiento->referencia ?? '-' }}
                                                </td>

                                                <td class="px-4 py-3">
                                                    {{ $movimiento->persona?->nombre_completo ?? '-' }}
                                                </td>

                                                <td class="px-4 py-3 text-right">
                                                    @if ($movimiento->entrada > 0)
                                                        {{ number_format($movimiento->entrada, 2, ',', '.') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>

                                                <td class="px-4 py-3 text-right">
                                                    @if ($movimiento->salida > 0)
                                                        {{ number_format($movimiento->salida, 2, ',', '.') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>

                                                <td class="px-4 py-3 text-right font-semibold">
                                                    {{ number_format($movimiento->saldo, 2, ',', '.') }}
                                                </td>

                                            </tr>

                                        @endforeach

                                    </tbody>

                                </table>
                            </div>

                        @endif

                    </div>
                </div>

            @endif

        </div>
    </div>
</x-app-layout>