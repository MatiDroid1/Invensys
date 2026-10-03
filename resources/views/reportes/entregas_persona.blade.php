<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Reporte de entregas por persona
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Filtros -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="GET" action="{{ route('reportes.entregas-persona') }}">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

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
                                    for="persona_id"
                                    class="block font-medium text-sm"
                                >
                                    Persona
                                </label>

                                <select
                                    name="persona_id"
                                    id="persona_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                >
                                    <option value="">
                                        Todas las personas
                                    </option>

                                    @foreach ($personas as $persona)
                                        <option
                                            value="{{ $persona->id }}"
                                            @selected($personaId == $persona->id)
                                        >
                                            {{ $persona->nombre_completo }}
                                            @if ($persona->area)
                                                - {{ $persona->area }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="flex items-center gap-3 mt-6">

                            <button
                                type="submit"
                                class="px-4 py-2 bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 rounded-md hover:bg-gray-700 dark:hover:bg-white transition"
                            >
                                Buscar
                            </button>

                            <a
                                href="{{ route('reportes.entregas-persona') }}"
                                class="px-4 py-2 text-gray-600 dark:text-gray-300"
                            >
                                Limpiar filtros
                            </a>

                            <a
                                href="{{ route('reportes.entregas-persona.csv', request()->query()) }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 text-sm font-medium rounded-md hover:bg-gray-700 dark:hover:bg-white transition"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4 me-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>

                                Descargar CSV
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
                            Entregas encontradas:
                            {{ $movimientos->count() }}
                        </h3>
                    </div>

                    @if ($movimientos->isEmpty())

                        <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-md">
                            No se encontraron entregas con los filtros seleccionados.
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

                                        <th class="px-4 py-3 text-right">
                                            Cantidad
                                        </th>

                                        <th class="px-4 py-3 text-left">
                                            Persona
                                        </th>

                                        <th class="px-4 py-3 text-left">
                                            Área
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

                                            <td class="px-4 py-3 text-right">
                                                {{ number_format($movimiento->cantidad, 2, ',', '.') }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $movimiento->persona?->nombre_completo ?? '-' }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $movimiento->persona?->area ?? '-' }}
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