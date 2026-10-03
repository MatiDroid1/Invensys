<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h2 class="md-title">Panel</h2>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Resumen del inventario a {{ now()->translatedFormat('d \d\e F \d\e Y') }}.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('movimientos.create') }}" class="md-btn md-btn-sm md-btn-tonal">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>

                    Nueva entrada
                </a>

                <a href="{{ route('movimientos.salida.create') }}" class="md-btn md-btn-sm md-btn-filled">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m7.5 7.5h-15" />
                    </svg>

                    Nueva salida
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Indicadores principales --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                @php
                    /*
                     * Cada indicador lleva su icono en un cuadro de color. Antes
                     * eran cuatro rectángulos idénticos con un número: no se
                     * distinguían entre sí de un vistazo, que es justo lo que se
                     * mira en un panel.
                     */
                    $indicadores = [
                        [
                            'titulo' => 'Artículos activos',
                            'valor' => $articulosActivos,
                            'icono' => 'M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375',
                            'tono' => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-300',
                        ],
                        [
                            'titulo' => 'Personas activas',
                            'valor' => $personasActivas,
                            'icono' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
                            'tono' => 'bg-sky-50 text-sky-600 dark:bg-sky-500/15 dark:text-sky-300',
                        ],
                        [
                            'titulo' => 'Movimientos este mes',
                            'valor' => $movimientosMes,
                            'icono' => 'M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5',
                            'tono' => 'bg-violet-50 text-violet-600 dark:bg-violet-500/15 dark:text-violet-300',
                        ],
                        [
                            'titulo' => 'Stock bajo',
                            'valor' => $articulosStockBajo->count(),
                            'icono' => 'M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z',
                            'tono' => $articulosStockBajo->isEmpty()
                                ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-300'
                                : 'bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-300',
                        ],
                    ];
                @endphp

                @foreach ($indicadores as $indicador)
                    <div class="md-card flex items-center gap-4 p-5">
                        <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl {{ $indicador['tono'] }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $indicador['icono'] }}" />
                            </svg>
                        </span>

                        <div class="min-w-0">
                            <p class="md-overline">{{ $indicador['titulo'] }}</p>

                            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-gray-900 dark:text-white">
                                {{ $indicador['valor'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Stock bajo --}}
            <section class="md-card overflow-hidden">
                <header class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
                    <div>
                        <h3 class="md-section-title">Alertas de stock</h3>

                        <p class="md-hint mt-0.5">
                            Artículos que están en su mínimo o por debajo.
                        </p>
                    </div>

                    <a href="{{ route('articulos.index') }}" class="md-btn md-btn-sm md-btn-text">
                        Ver artículos

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </header>

                @if ($articulosStockBajo->isEmpty())

                    {{-- Estado vacío: se dice que no hay alertas, no se deja la
                         tabla en blanco sin explicación. --}}
                    <div class="flex items-center gap-3 border-t border-gray-100 px-5 py-6 dark:border-gray-700/70">
                        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-300">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </span>

                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            No hay artículos con stock igual o inferior al mínimo.
                        </p>
                    </div>

                @else

                    <div class="overflow-x-auto border-t border-gray-100 dark:border-gray-700/70">
                        <table class="md-table">
                            <thead>
                                <tr>
                                    <th scope="col">Código</th>
                                    <th scope="col">Artículo</th>
                                    <th scope="col" class="text-end">Stock actual</th>
                                    <th scope="col" class="text-end">Stock mínimo</th>
                                    <th scope="col">Estado</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($articulosStockBajo as $articulo)
                                    <tr>
                                        <td class="font-medium tabular-nums text-gray-900 dark:text-white">
                                            {{ $articulo->codigo }}
                                        </td>

                                        <td>{{ $articulo->nombre }}</td>

                                        <td class="text-end font-semibold tabular-nums">
                                            {{ number_format($articulo->stock_calculado, 2, ',', '.') }}
                                        </td>

                                        <td class="text-end tabular-nums text-gray-500 dark:text-gray-400">
                                            {{ number_format($articulo->stock_minimo, 2, ',', '.') }}
                                        </td>

                                        <td>
                                            <span class="md-badge-danger">Stock bajo</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                @endif
            </section>

            {{-- Últimos movimientos --}}
            <section class="md-card overflow-hidden">
                <header class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
                    <div>
                        <h3 class="md-section-title">Últimos movimientos</h3>

                        <p class="md-hint mt-0.5">
                            Las {{ min(10, max($ultimosMovimientos->count(), 1)) }} operaciones más recientes.
                        </p>
                    </div>

                    <a href="{{ route('movimientos.index') }}" class="md-btn md-btn-sm md-btn-text">
                        Ver todos

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </header>

                @if ($ultimosMovimientos->isEmpty())

                    <p class="border-t border-gray-100 px-5 py-6 text-sm text-gray-500 dark:border-gray-700/70 dark:text-gray-400">
                        No hay movimientos registrados.
                    </p>

                @else

                    <div class="overflow-x-auto border-t border-gray-100 dark:border-gray-700/70">
                        <table class="md-table">
                            <thead>
                                <tr>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Artículo</th>
                                    <th scope="col">Tipo</th>
                                    <th scope="col" class="text-end">Cantidad</th>
                                    <th scope="col">Persona</th>
                                    <th scope="col">Usuario</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($ultimosMovimientos as $movimiento)
                                    <tr>
                                        <td class="whitespace-nowrap tabular-nums text-gray-500 dark:text-gray-400">
                                            {{ $movimiento->fecha_movimiento->format('d/m/Y H:i') }}
                                        </td>

                                        <td>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $movimiento->articulo->codigo }}</span>
                                            <span class="text-gray-500 dark:text-gray-400">— {{ $movimiento->articulo->nombre }}</span>
                                        </td>

                                        <td>
                                            <x-tipo-movimiento :tipo="$movimiento->tipo" />
                                        </td>

                                        <td class="text-end font-semibold tabular-nums">
                                            {{ number_format($movimiento->cantidad, 2, ',', '.') }}
                                        </td>

                                        <td>{{ $movimiento->persona?->nombre_completo ?? '—' }}</td>

                                        <td>{{ $movimiento->usuario->name }}</td>
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