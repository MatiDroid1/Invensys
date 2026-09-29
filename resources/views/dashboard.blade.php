<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Indicadores principales --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Artículos activos
                        </div>

                        <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $articulosActivos }}
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Personas activas
                        </div>

                        <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $personasActivas }}
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Movimientos este mes
                        </div>

                        <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $movimientosMes }}
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Stock bajo
                        </div>

                        <div class="mt-2 text-3xl font-bold
                            {{ $articulosStockBajo->count() > 0
                                ? 'text-red-600'
                                : 'text-green-600' }}">
                            {{ $articulosStockBajo->count() }}
                        </div>
                    </div>
                </div>

            </div>

            {{-- Stock bajo --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">
                            Alertas de stock
                        </h3>

                        <a
                            href="{{ route('articulos.index') }}"
                            class="text-blue-600 hover:underline"
                        >
                            Ver artículos
                        </a>
                    </div>

                    @if ($articulosStockBajo->isEmpty())

                        <div class="p-4 bg-green-100 text-green-800 rounded-md">
                            No hay artículos con stock igual o inferior al mínimo.
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
                                    @foreach ($articulosStockBajo as $articulo)

                                        <tr class="border-b border-gray-200 dark:border-gray-700">

                                            <td class="px-4 py-3">
                                                {{ $articulo->codigo }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $articulo->nombre }}
                                            </td>

                                            <td class="px-4 py-3 text-right font-semibold">
                                                {{ number_format($articulo->stock_calculado, 2, ',', '.') }}
                                            </td>

                                            <td class="px-4 py-3 text-right">
                                                {{ number_format($articulo->stock_minimo, 2, ',', '.') }}
                                            </td>

                                            <td class="px-4 py-3">
                                                <span class="text-red-600 font-semibold">
                                                    Stock bajo
                                                </span>
                                            </td>

                                        </tr>

                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    @endif

                </div>
            </div>

            {{-- Últimos movimientos --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">
                            Últimos movimientos
                        </h3>

                        <a
                            href="{{ route('movimientos.index') }}"
                            class="text-blue-600 hover:underline"
                        >
                            Ver todos
                        </a>
                    </div>

                    @if ($ultimosMovimientos->isEmpty())

                        <p>
                            No hay movimientos registrados.
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
                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach ($ultimosMovimientos as $movimiento)

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