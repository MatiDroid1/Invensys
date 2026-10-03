<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <a
                    href="{{ route('articulos.index') }}"
                    class="md-btn md-btn-sm md-btn-text -ml-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>

                    Artículos
                </a>

                <h2 class="mt-1.5 md-title">{{ $articulo->nombre }}</h2>

                <p class="mt-1 font-mono text-sm text-gray-500 dark:text-gray-400">
                    {{ $articulo->codigo }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a
                    href="{{ route('articulos.edit', $articulo) }}"
                    class="md-btn md-btn-md md-btn-filled"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                    </svg>

                    Editar
                </a>

                <a
                    href="{{ route('movimientos.salida.create', ['articulo_id' => $articulo->id]) }}"
                    class="md-btn md-btn-md md-btn-outlined"
                >
                    Registrar salida
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @php
                $stock = (float) $articulo->stock_actual;
                $minimo = (float) $articulo->stock_minimo;
                $stockBajo = $stock <= $minimo;
            @endphp

            {{-- Indicadores --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 sm:gap-6">
                <div class="md-card p-5 sm:p-6">
                    <p class="md-overline">Stock actual</p>

                    <p @class([
                        'mt-2 text-2xl font-bold sm:text-3xl',
                        'text-red-600 dark:text-red-400' => $stockBajo,
                        'text-emerald-600 dark:text-emerald-400' => ! $stockBajo,
                    ])>
                        {{ number_format($stock, 2, ',', '.') }}
                    </p>

                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ $articulo->unidadMedida?->nombre }}
                    </p>
                </div>

                <div class="md-card p-5 sm:p-6">
                    <p class="md-overline">Stock mínimo</p>

                    <p class="mt-2 text-2xl font-bold text-gray-900 sm:text-3xl dark:text-white">
                        {{ number_format($minimo, 2, ',', '.') }}
                    </p>
                </div>

                <div class="md-card p-5 sm:p-6">
                    <p class="md-overline">Última entrada</p>

                    <p class="mt-2 text-base font-semibold text-gray-900 sm:text-lg dark:text-white">
                        {{ $ultimaEntrada ? \Illuminate\Support\Carbon::parse($ultimaEntrada)->format('d/m/Y') : '—' }}
                    </p>
                </div>

                <div class="md-card p-5 sm:p-6">
                    <p class="md-overline">Última salida</p>

                    <p class="mt-2 text-base font-semibold text-gray-900 sm:text-lg dark:text-white">
                        {{ $ultimaSalida ? \Illuminate\Support\Carbon::parse($ultimaSalida)->format('d/m/Y') : '—' }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                {{-- Ficha del artículo --}}
                <section class="md-card lg:col-span-1">
                    <div class="md-divider px-5 py-4">
                        <h3 class="md-section-title">Ficha del artículo</h3>
                    </div>

                    <dl class="space-y-3 px-5 py-4 text-sm">
                        <div class="flex justify-between gap-4">
                            <dt class="text-gray-500 dark:text-gray-400">Categoría</dt>
                            <dd class="text-right font-medium">{{ $articulo->categoria?->nombre ?? '—' }}</dd>
                        </div>

                        <div class="flex justify-between gap-4">
                            <dt class="text-gray-500 dark:text-gray-400">Unidad de medida</dt>
                            <dd class="text-right font-medium">{{ $articulo->unidadMedida?->nombre ?? '—' }}</dd>
                        </div>

                        <div class="flex justify-between gap-4">
                            <dt class="text-gray-500 dark:text-gray-400">Control individual</dt>
                            <dd class="text-right font-medium">{{ $articulo->control_individual ? 'Sí' : 'No' }}</dd>
                        </div>

                        <div class="flex justify-between gap-4">
                            <dt class="text-gray-500 dark:text-gray-400">Estado</dt>
                            <dd class="text-right">
                                <span @class([
                                    'md-badge',
                                    'md-badge-success' => $articulo->activo,
                                    'md-badge-warning' => ! $articulo->activo,
                                ])>
                                    {{ $articulo->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </dd>
                        </div>

                        <div class="flex justify-between gap-4">
                            <dt class="text-gray-500 dark:text-gray-400">Creado</dt>
                            <dd class="text-right font-medium">{{ $articulo->created_at->format('d/m/Y') }}</dd>
                        </div>
                    </dl>

                    @if ($articulo->descripcion)
                        <div class="md-divider px-5 py-4">
                            <h4 class="md-overline mb-2">Descripción</h4>

                            <p class="whitespace-pre-line text-sm text-gray-700 dark:text-gray-200">
                                {{ $articulo->descripcion }}
                            </p>
                        </div>
                    @endif
                </section>

                {{-- Movimientos recientes --}}
                <section class="md-card lg:col-span-2">
                    <div class="flex flex-col gap-2 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <h3 class="md-section-title">Movimientos recientes</h3>

                        <a
                            href="{{ route('kardex.index', ['articulo_id' => $articulo->id]) }}"
                            class="md-btn md-btn-sm md-btn-text"
                        >
                            Ver Kardex completo
                        </a>
                    </div>

                    @if ($movimientos->isEmpty())
                        <x-estado-vacio
                            class="md-divider"
                            titulo="Sin movimientos"
                            descripcion="Este artículo todavía no registra entradas ni salidas."
                        />
                    @else
                        <div class="md-divider overflow-x-auto">
                            <table class="md-table">
                                <thead>
                                    <tr>
                                        <th scope="col">Fecha</th>
                                        <th scope="col">Tipo</th>
                                        <th scope="col">Referencia</th>
                                        <th scope="col" class="text-right">Cantidad</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($movimientos as $movimiento)
                                        <tr>
                                            <td class="whitespace-nowrap">
                                                {{ $movimiento->fecha_movimiento->format('d/m/Y H:i') }}
                                            </td>

                                            <td>
                                                <x-tipo-movimiento :tipo="$movimiento->tipo" />
                                            </td>

                                            <td class="text-gray-500 dark:text-gray-400">
                                                {{ $movimiento->referencia ?? '—' }}
                                            </td>

                                            <td @class([
                                                'text-right font-semibold whitespace-nowrap',
                                                'text-emerald-600 dark:text-emerald-400' => in_array($movimiento->tipo, ['ENTRADA', 'AJUSTE_POSITIVO']),
                                                'text-red-600 dark:text-red-400' => in_array($movimiento->tipo, ['SALIDA', 'AJUSTE_NEGATIVO']),
                                            ])>
                                                {{ number_format($movimiento->cantidad, 2, ',', '.') }}
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
    </div>
</x-app-layout>