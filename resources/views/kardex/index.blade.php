<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="md-title">Kardex de inventario</h2>

                <p class="md-subtitle mt-0.5">
                    Historial completo de entradas, salidas y saldo acumulado por artículo.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            {{-- Consulta: el Kardex siempre es de un artículo, así que el
                 selector va sobre la tabla y no escondido en un menú. --}}
            <form method="GET" action="{{ route('kardex.index') }}" class="md-card p-5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
                    <div class="flex-1">
                        <label for="articulo_id" class="md-label">Artículo</label>

                        <select name="articulo_id" id="articulo_id" class="md-field">
                            <option value="">Seleccione un artículo</option>

                            @foreach ($articulos as $item)
                                <option value="{{ $item->id }}" @selected($articulo?->id == $item->id)>
                                    {{ $item->codigo }} - {{ $item->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="md-btn md-btn-md md-btn-filled">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>

                        Consultar Kardex
                    </button>
                </div>
            </form>

            @if ($articulo)
                <section class="md-card">
                    <div class="grid grid-cols-2 gap-4 p-5 md:grid-cols-4">
                        <div>
                            <p class="md-overline">Código</p>
                            <p class="mt-1 font-mono font-semibold">{{ $articulo->codigo }}</p>
                        </div>

                        <div>
                            <p class="md-overline">Artículo</p>
                            <p class="mt-1 font-semibold">{{ $articulo->nombre }}</p>
                        </div>

                        <div>
                            <p class="md-overline">Stock mínimo</p>
                            <p class="mt-1 font-semibold">
                                {{ number_format($articulo->stock_minimo, 2, ',', '.') }}
                            </p>
                        </div>

                        <div>
                            <p class="md-overline">Stock actual</p>
                            <p @class([
                                'mt-1 text-lg font-bold',
                                'text-red-600 dark:text-red-400' => $articulo->stock_actual <= $articulo->stock_minimo,
                                'text-emerald-600 dark:text-emerald-400' => $articulo->stock_actual > $articulo->stock_minimo,
                            ])>
                                {{ number_format($articulo->stock_actual, 2, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </section>

                <section class="md-card overflow-hidden">
                    @if ($movimientos->isEmpty())
                        <x-estado-vacio
                            class="md-divider"
                            titulo="Sin movimientos"
                            descripcion="Este artículo no tiene movimientos registrados."
                        />
                    @else
                        <div class="overflow-x-auto">
                            <table class="md-table">
                                <thead>
                                    <tr>
                                        <th scope="col">Fecha</th>
                                        <th scope="col">Tipo</th>
                                        <th scope="col">Referencia</th>
                                        <th scope="col">Persona</th>
                                        <th scope="col" class="text-right">Entrada</th>
                                        <th scope="col" class="text-right">Salida</th>
                                        <th scope="col" class="text-right">Saldo</th>
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

                                            <td>{{ $movimiento->persona?->nombre_completo ?? '—' }}</td>

                                            <td class="text-right font-medium text-emerald-600 dark:text-emerald-400">
                                                {{ $movimiento->entrada > 0 ? number_format($movimiento->entrada, 2, ',', '.') : '—' }}
                                            </td>

                                            <td class="text-right font-medium text-red-600 dark:text-red-400">
                                                {{ $movimiento->salida > 0 ? number_format($movimiento->salida, 2, ',', '.') : '—' }}
                                            </td>

                                            <td @class([
                                                'text-right font-semibold',
                                                'text-red-600 dark:text-red-400' => $movimiento->saldo <= 0,
                                                'text-gray-900 dark:text-white' => $movimiento->saldo > 0,
                                            ])>
                                                {{ number_format($movimiento->saldo, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>
            @else
                <x-estado-vacio
                    icono="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"
                    titulo="Elige un artículo"
                    descripcion="Selecciona un artículo arriba para ver su historial de movimientos y el saldo acumulado."
                />
            @endif
        </div>
    </div>
</x-app-layout>