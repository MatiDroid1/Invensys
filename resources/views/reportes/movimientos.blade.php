<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Reporte de movimientos
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Filtros -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="GET" action="{{ route('reportes.movimientos') }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                            <div>
                                <label
                                    for="fecha_desde"
                                    class="block font-medium text-sm"
                                >
                                    Fecha desde
                                </label>

                                <input
                                    type="date"
                                    name="fecha_desde"
                                    id="fecha_desde"
                                    value="{{ $fechaDesde }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                >
                            </div>

                            <div>
                                <label
                                    for="fecha_hasta"
                                    class="block font-medium text-sm"
                                >
                                    Fecha hasta
                                </label>

                                <input
                                    type="date"
                                    name="fecha_hasta"
                                    id="fecha_hasta"
                                    value="{{ $fechaHasta }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                >
                            </div>

                            <div>
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
                                        Todos los artículos
                                    </option>

                                    @foreach ($articulos as $articulo)
                                        <option
                                            value="{{ $articulo->id }}"
                                            @selected($articuloId == $articulo->id)
                                        >
                                            {{ $articulo->codigo }} - {{ $articulo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label
                                    for="tipo"
                                    class="block font-medium text-sm"
                                >
                                    Tipo de movimiento
                                </label>

                                <select
                                    name="tipo"
                                    id="tipo"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                >
                                    <option value="">
                                        Todos los tipos
                                    </option>

                                    <option
                                        value="ENTRADA"
                                        @selected($tipo === 'ENTRADA')
                                    >
                                        Entrada
                                    </option>

                                    <option
                                        value="SALIDA"
                                        @selected($tipo === 'SALIDA')
                                    >
                                        Salida
                                    </option>

                                    <option
                                        value="AJUSTE_POSITIVO"
                                        @selected($tipo === 'AJUSTE_POSITIVO')
                                    >
                                        Ajuste positivo
                                    </option>

                                    <option
                                        value="AJUSTE_NEGATIVO"
                                        @selected($tipo === 'AJUSTE_NEGATIVO')
                                    >
                                        Ajuste negativo
                                    </option>
                                </select>
                            </div>

                        </div>

                        <div class="flex items-center gap-3 mt-6">

                            <button
                                type="submit"
                                class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700"
                            >
                                Buscar
                            </button>

                            <a
                                href="{{ route('reportes.movimientos') }}"
                                class="px-4 py-2 text-gray-600 dark:text-gray-300"
                            >
                                Limpiar filtros
                            </a>

                        </div>

                    </form>

                </div>
            </div>

            <!-- Resultados -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-between items-center mb-4">

                        <h3 class="text-lg font-semibold">
                            Movimientos encontrados:
                            {{ $movimientos->count() }}
                        </h3>

                    </div>

                    @if ($movimientos->isEmpty())

                        <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-md">
                            No se encontraron movimientos con los filtros seleccionados.
                        </div>

                    @else

                        <div class="overflow-x-auto">
                            <table class="min-w-full">

                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-700">

                                        <th class="px-4 py-3 text-left">
                                            Fecha
                                        </th>

                                        <th class="px-4 py-3 text-left">
                                            Artículo
                                        </th>

                                        <th class="px-4 py-3 text-left">
                                            Tipo
                                        </th>

                                        <th class="px-4 py-3 text-right">
                                            Cantidad
                                        </th>

                                        <th class="px-4 py-3 text-left">
                                            Persona
                                        </th>

                                        <th class="px-4 py-3 text-left">
                                            Usuario
                                        </th>

                                        <th class="px-4 py-3 text-left">
                                            Referencia
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
                                                {{ $movimiento->articulo->codigo }}
                                                -
                                                {{ $movimiento->articulo->nombre }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $movimiento->tipo }}
                                            </td>

                                            <td class="px-4 py-3 text-right">
                                                {{ number_format($movimiento->cantidad, 2, ',', '.') }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $movimiento->persona?->nombre_completo ?? '-' }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $movimiento->usuario->name }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $movimiento->referencia ?? '-' }}
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