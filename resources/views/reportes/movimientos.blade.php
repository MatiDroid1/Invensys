<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="md-title">Reporte de movimientos</h2>

            <p class="md-subtitle mt-0.5">Filtra por período, artículo y tipo de movimiento.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('reportes.movimientos') }}" class="md-card p-5">
                <div class="grid grid-cols-1 gap-x-6 gap-y-4 md:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="fecha_desde" class="md-label">Fecha desde</label>

                        <input type="date" name="fecha_desde" id="fecha_desde" value="{{ $fechaDesde }}" class="md-field">
                    </div>

                    <div>
                        <label for="fecha_hasta" class="md-label">Fecha hasta</label>

                        <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ $fechaHasta }}" class="md-field">
                    </div>

                    <div>
                        <label for="articulo_id" class="md-label">Artículo</label>

                        <select name="articulo_id" id="articulo_id" class="md-field">
                            <option value="">Todos los artículos</option>

                            @foreach ($articulos as $articulo)
                                <option value="{{ $articulo->id }}" @selected($articuloId == $articulo->id)>
                                    {{ $articulo->codigo }} - {{ $articulo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="tipo" class="md-label">Tipo de movimiento</label>

                        <select name="tipo" id="tipo" class="md-field">
                            <option value="">Todos los tipos</option>

                            <option value="ENTRADA" @selected($tipo === 'ENTRADA')>Entrada</option>
                            <option value="SALIDA" @selected($tipo === 'SALIDA')>Salida</option>
                            <option value="AJUSTE_POSITIVO" @selected($tipo === 'AJUSTE_POSITIVO')>Ajuste positivo</option>
                            <option value="AJUSTE_NEGATIVO" @selected($tipo === 'AJUSTE_NEGATIVO')>Ajuste negativo</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap items-center gap-2">
                    <button type="submit" class="md-btn md-btn-md md-btn-filled">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>

                        Buscar
                    </button>

                    <a href="{{ route('reportes.movimientos') }}" class="md-btn md-btn-md md-btn-text">
                        Limpiar filtros
                    </a>

                    <a href="{{ route('reportes.movimientos.csv', request()->query()) }}" class="md-btn md-btn-md md-btn-outlined ms-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>

                        Descargar CSV
                    </a>
                </div>
            </form>

            <section class="md-card overflow-hidden">
                <div class="flex flex-wrap items-center justify-between gap-2 px-5 py-4">
                    <h3 class="md-section-title">Movimientos encontrados</h3>

                    <span class="md-badge">{{ $movimientos->count() }}</span>
                </div>

                @if ($movimientos->isEmpty())
                    <x-estado-vacio
                        class="md-divider"
                        descripcion="No se encontraron movimientos con los filtros seleccionados."
                    />
                @else
                    <div class="md-divider overflow-x-auto">
                        <table class="md-table">
                            <thead>
                                <tr>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Artículo</th>
                                    <th scope="col">Tipo</th>
                                    <th scope="col" class="text-right">Cantidad</th>
                                    <th scope="col">Persona</th>
                                    <th scope="col">Usuario</th>
                                    <th scope="col">Referencia</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($movimientos as $movimiento)
                                    <tr>
                                        <td class="whitespace-nowrap">
                                            {{ $movimiento->fecha_movimiento->format('d/m/Y H:i') }}
                                        </td>

                                        <td>
                                            <span class="font-mono text-xs text-gray-500 dark:text-gray-400">
                                                {{ $movimiento->articulo->codigo }}
                                            </span>

                                            <span class="block">{{ $movimiento->articulo->nombre }}</span>
                                        </td>

                                        <td>
                                            <x-tipo-movimiento :tipo="$movimiento->tipo" />
                                        </td>

                                        <td
                                            @class([
                                                'text-right font-semibold whitespace-nowrap',
                                                'text-emerald-600 dark:text-emerald-400' => in_array($movimiento->tipo, ['ENTRADA', 'AJUSTE_POSITIVO']),
                                                'text-red-600 dark:text-red-400' => in_array($movimiento->tipo, ['SALIDA', 'AJUSTE_NEGATIVO']),
                                            ])
                                        >
                                            {{ number_format($movimiento->cantidad, 2, ',', '.') }}
                                        </td>

                                        <td>{{ $movimiento->persona?->nombre_completo ?? '—' }}</td>

                                        <td>{{ $movimiento->usuario->name }}</td>

                                        <td class="text-gray-500 dark:text-gray-400">
                                            {{ $movimiento->referencia ?? '—' }}
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
</x-app-layout>