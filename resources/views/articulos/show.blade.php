<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <a
                    href="{{ route('articulos.index') }}"
                    class="md-btn md-btn-text md-btn-sm"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>

                    Artículos
                </a>

                <h2 class="md-title mt-1">
                    {{ $articulo->nombre }}
                </h2>

                <p class="mt-1 font-mono text-sm text-gray-500 dark:text-gray-400">
                    {{ $articulo->codigo }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a
                    href="{{ route('articulos.edit', $articulo) }}"
                    class="md-btn md-btn-filled"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                    </svg>

                    Editar
                </a>

                <a
                    href="{{ route('movimientos.salida.create', ['articulo_id' => $articulo->id]) }}"
                    class="md-btn md-btn-outlined"
                >
                    Registrar salida
                </a>
            </div>
        </div>
    </x-slot>

    <div class="md-page md-page-body space-y-6">
        {{-- Indicadores --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 sm:gap-6">
            @php
                $stock = (float) $articulo->stock_actual;
                $minimo = (float) $articulo->stock_minimo;
                $stockBajo = $stock <= $minimo;
            @endphp

            <div class="md-stat">
                <div class="md-overline">
                    Stock actual
                </div>

                <div @class([
                    'mt-2 text-2xl font-bold sm:text-3xl',
                    'text-red-600 dark:text-red-400' => $stockBajo,
                    'text-green-600 dark:text-green-400' => ! $stockBajo,
                ])>
                    {{ number_format($stock, 2, ',', '.') }}
                </div>

                <div class="md-hint">
                    {{ $articulo->unidadMedida?->nombre }}
                </div>
            </div>

            <div class="md-stat">
                <div class="md-overline">
                    Stock mínimo
                </div>

                <div class="mt-2 text-2xl font-bold sm:text-3xl text-gray-900 dark:text-gray-100">
                    {{ number_format($minimo, 2, ',', '.') }}
                </div>
            </div>

            <div class="md-stat">
                <div class="md-overline">
                    Última entrada
                </div>

                <div class="mt-2 text-base font-semibold text-gray-900 dark:text-gray-100 sm:text-lg">
                    {{ $ultimaEntrada ? \Illuminate\Support\Carbon::parse($ultimaEntrada)->format('d/m/Y') : '—' }}
                </div>
            </div>

            <div class="md-stat">
                <div class="md-overline">
                    Última salida
                </div>

                <div class="mt-2 text-base font-semibold text-gray-900 dark:text-gray-100 sm:text-lg">
                    {{ $ultimaSalida ? \Illuminate\Support\Carbon::parse($ultimaSalida)->format('d/m/Y') : '—' }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- Ficha del artículo --}}
            <section class="md-card p-5 sm:p-6 lg:col-span-1">
                <h3 class="md-section-title">
                    Ficha del artículo
                </h3>

                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-500 dark:text-gray-400">
                            Categoría
                        </dt>
                        <dd class="text-right font-medium text-gray-900 dark:text-gray-100">
                            {{ $articulo->categoria?->nombre ?? '—' }}
                        </dd>
                    </div>

                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-500 dark:text-gray-400">
                            Unidad de medida
                        </dt>
                        <dd class="text-right font-medium text-gray-900 dark:text-gray-100">
                            {{ $articulo->unidadMedida?->nombre ?? '—' }}
                        </dd>
                    </div>

                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-500 dark:text-gray-400">
                            Control individual
                        </dt>
                        <dd class="text-right font-medium text-gray-900 dark:text-gray-100">
                            {{ $articulo->control_individual ? 'Sí' : 'No' }}
                        </dd>
                    </div>

                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-500 dark:text-gray-400">
                            Estado
                        </dt>
                        <dd class="text-right font-medium text-gray-900 dark:text-gray-100">
                            {{ $articulo->activo ? 'Activo' : 'Inactivo' }}
                        </dd>
                    </div>

                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-500 dark:text-gray-400">
                            Creado
                        </dt>
                        <dd class="text-right font-medium text-gray-900 dark:text-gray-100">
                            {{ $articulo->created_at->format('d/m/Y') }}
                        </dd>
                    </div>
                </dl>

                @if ($articulo->descripcion)
                    <div class="md-divider mt-6 pt-6">
                        <h4 class="md-overline">
                            Descripción
                        </h4>

                        <p class="mt-2 whitespace-pre-line text-sm text-gray-700 dark:text-gray-300">
                            {{ $articulo->descripcion }}
                        </p>
                    </div>
                @endif
            </section>

            {{-- Movimientos recientes --}}
            <section class="md-card lg:col-span-2">
                <div class="flex flex-col gap-2 px-5 pb-4 pt-5 sm:flex-row sm:items-center sm:justify-between sm:px-6 sm:pt-6">
                    <h3 class="md-section-title">
                        Movimientos recientes
                    </h3>

                    <a
                        href="{{ route('kardex.index', ['articulo_id' => $articulo->id]) }}"
                        class="md-btn md-btn-text md-btn-sm"
                    >
                        Ver Kardex completo
                    </a>
                </div>

                @if ($movimientos->isEmpty())
                    <x-estado-vacio
                        :descripcion="'Este artículo no tiene movimientos registrados.'"
                    />
                @else
                    <x-tabla>
                        <thead>
                            <tr class="md-tr">
                                <th scope="col" class="md-th md-th-num">
                                    Fecha
                                </th>
                                <th scope="col" class="md-th">
                                    Tipo
                                </th>
                                <th scope="col" class="md-th">
                                    Referencia
                                </th>
                                <th scope="col" class="md-th md-th-num">
                                    Cantidad
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($movimientos as $movimiento)
                                <tr class="md-tr">
                                    <td class="md-td md-td-num">
                                        {{ $movimiento->fecha_movimiento->format('d/m/Y H:i') }}
                                    </td>

                                    <td class="md-td">
                                        <x-tipo-movimiento :tipo="$movimiento->tipo" />
                                    </td>

                                    <td class="md-td text-gray-500 dark:text-gray-400">
                                        <span class="md-td-clip" title="{{ $movimiento->referencia ?? '—' }}">
                                            {{ $movimiento->referencia ?? '—' }}
                                        </span>
                                    </td>

                                    <td
                                        @class([
                                            'md-td md-td-num font-semibold',
                                            'text-green-600 dark:text-green-400' => in_array($movimiento->tipo, ['ENTRADA', 'AJUSTE_POSITIVO']),
                                            'text-red-600 dark:text-red-400' => in_array($movimiento->tipo, ['SALIDA', 'AJUSTE_NEGATIVO']),
                                        ])
                                    >
                                        {{ number_format($movimiento->cantidad, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </x-tabla>
                @endif
            </section>

        </div>
    </div>
</x-app-layout>