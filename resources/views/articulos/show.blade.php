<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <a
                    href="{{ route('articulos.index') }}"
                    class="text-sm text-gray-500 hover:underline dark:text-gray-400"
                >
                    &larr; Artículos
                </a>

                <h2 class="mt-1 font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $articulo->nombre }}
                </h2>

                <p class="mt-1 font-mono text-sm text-gray-500 dark:text-gray-400">
                    {{ $articulo->codigo }}
                </p>
            </div>

            <div class="flex flex-col gap-2 sm:flex-row">
                <a
                    href="{{ route('articulos.edit', $articulo) }}"
                    class="inline-flex items-center justify-center px-4 py-2 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700"
                >
                    Editar
                </a>

                <a
                    href="{{ route('movimientos.salida.create', ['articulo_id' => $articulo->id]) }}"
                    class="inline-flex items-center justify-center px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:text-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:hover:bg-gray-700"
                >
                    Registrar salida
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Indicadores --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                @php
                    $stock = (float) $articulo->stock_actual;
                    $minimo = (float) $articulo->stock_minimo;
                    $stockBajo = $stock <= $minimo;
                @endphp

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-5 sm:p-6">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Stock actual
                        </div>

                        <div @class([
                            'mt-2 text-2xl sm:text-3xl font-bold',
                            'text-red-600 dark:text-red-400' => $stockBajo,
                            'text-green-600 dark:text-green-400' => ! $stockBajo,
                        ])>
                            {{ number_format($stock, 2, ',', '.') }}
                        </div>

                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ $articulo->unidadMedida?->nombre }}
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-5 sm:p-6">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Stock mínimo
                        </div>

                        <div class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100">
                            {{ number_format($minimo, 2, ',', '.') }}
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-5 sm:p-6">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Última entrada
                        </div>

                        <div class="mt-2 text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ $ultimaEntrada ? \Illuminate\Support\Carbon::parse($ultimaEntrada)->format('d/m/Y') : '—' }}
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-5 sm:p-6">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Última salida
                        </div>

                        <div class="mt-2 text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ $ultimaSalida ? \Illuminate\Support\Carbon::parse($ultimaSalida)->format('d/m/Y') : '—' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Ficha del artículo --}}
                <div class="lg:col-span-1 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">
                            Ficha del artículo
                        </h3>

                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between gap-4">
                                <dt class="text-gray-500 dark:text-gray-400">
                                    Categoría
                                </dt>
                                <dd class="font-medium text-right">
                                    {{ $articulo->categoria?->nombre ?? '—' }}
                                </dd>
                            </div>

                            <div class="flex justify-between gap-4">
                                <dt class="text-gray-500 dark:text-gray-400">
                                    Unidad de medida
                                </dt>
                                <dd class="font-medium text-right">
                                    {{ $articulo->unidadMedida?->nombre ?? '—' }}
                                </dd>
                            </div>

                            <div class="flex justify-between gap-4">
                                <dt class="text-gray-500 dark:text-gray-400">
                                    Control individual
                                </dt>
                                <dd class="font-medium text-right">
                                    {{ $articulo->control_individual ? 'Sí' : 'No' }}
                                </dd>
                            </div>

                            <div class="flex justify-between gap-4">
                                <dt class="text-gray-500 dark:text-gray-400">
                                    Estado
                                </dt>
                                <dd class="font-medium text-right">
                                    {{ $articulo->activo ? 'Activo' : 'Inactivo' }}
                                </dd>
                            </div>

                            <div class="flex justify-between gap-4">
                                <dt class="text-gray-500 dark:text-gray-400">
                                    Creado
                                </dt>
                                <dd class="font-medium text-right">
                                    {{ $articulo->created_at->format('d/m/Y') }}
                                </dd>
                            </div>
                        </dl>

                        @if ($articulo->descripcion)
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-2">
                                    Descripción
                                </h4>

                                <p class="text-sm whitespace-pre-line">
                                    {{ $articulo->descripcion }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Movimientos recientes --}}
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">

                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between mb-4">
                            <h3 class="text-lg font-semibold">
                                Movimientos recientes
                            </h3>

                            <a
                                href="{{ route('kardex.index', ['articulo_id' => $articulo->id]) }}"
                                class="text-sm font-medium text-indigo-600 hover:underline dark:text-indigo-400"
                            >
                                Ver Kardex completo
                            </a>
                        </div>

                        @if ($movimientos->isEmpty())
                            <p class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Este artículo no tiene movimientos registrados.
                            </p>
                        @else
                            <div class="overflow-x-auto -mx-6">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                Fecha
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                Tipo
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                Referencia
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                Cantidad
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($movimientos as $movimiento)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/40 transition-colors">
                                                <td class="px-6 py-3 text-sm whitespace-nowrap">
                                                    {{ $movimiento->fecha_movimiento->format('d/m/Y H:i') }}
                                                </td>

                                                <td class="px-6 py-3">
                                                    <x-tipo-movimiento :tipo="$movimiento->tipo" />
                                                </td>

                                                <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $movimiento->referencia ?? '—' }}
                                                </td>

                                                <td
                                                    @class([
                                                        'px-6 py-3 text-sm text-right font-semibold whitespace-nowrap',
                                                        'text-green-600 dark:text-green-400' => in_array($movimiento->tipo, ['ENTRADA', 'AJUSTE_POSITIVO']),
                                                        'text-red-600 dark:text-red-400' => in_array($movimiento->tipo, ['SALIDA', 'AJUSTE_NEGATIVO']),
                                                    ])
                                                >
                                                    {{ number_format($movimiento->cantidad, 2, ',', '.') }}
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
    </div>
</x-app-layout>
