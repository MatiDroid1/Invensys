<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Reporte de entregas por persona
        </h2>
    </x-slot>

    <div class="md-page md-page-body space-y-5">
        <x-alerta />

        {{-- Filtros --}}
        <section class="md-card mx-auto w-full max-w-5xl p-5 sm:p-6">
            <form method="GET" action="{{ route('reportes.entregas-persona') }}">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label for="fecha_desde" class="md-label">
                            Fecha desde
                        </label>

                        <input
                            type="date"
                            name="fecha_desde"
                            id="fecha_desde"
                            value="{{ $fechaDesde }}"
                            class="md-field mt-1"
                        >
                    </div>

                    <div>
                        <label for="fecha_hasta" class="md-label">
                            Fecha hasta
                        </label>

                        <input
                            type="date"
                            name="fecha_hasta"
                            id="fecha_hasta"
                            value="{{ $fechaHasta }}"
                            class="md-field mt-1"
                        >
                    </div>

                    <div>
                        <label for="persona_id" class="md-label">
                            Persona
                        </label>

                        <select
                            name="persona_id"
                            id="persona_id"
                            class="md-field mt-1"
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

                <div class="mt-5 flex flex-wrap items-center gap-3">
                    <button type="submit" class="md-btn md-btn-filled">
                        Buscar
                    </button>

                    <a href="{{ route('reportes.entregas-persona') }}" class="md-btn md-btn-text">
                        Limpiar filtros
                    </a>

                    <a
                        href="{{ route('reportes.entregas-persona.csv', request()->query()) }}"
                        class="md-btn md-btn-tonal"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>

                        Descargar CSV
                    </a>
                </div>
            </form>
        </section>

        {{-- Resultados --}}
        <section class="md-card">
            <div class="flex items-center justify-between px-5 pb-4 pt-5 sm:px-6 sm:pt-6">
                <h3 class="md-section-title">
                    Entregas encontradas:
                    <span class="font-normal text-gray-500 dark:text-gray-400">{{ $movimientos->count() }}</span>
                </h3>
            </div>

            @if ($movimientos->isEmpty())
                <x-estado-vacio :descripcion="'No se encontraron entregas con los filtros seleccionados.'" />
            @else
                <x-tabla :alto="true">
                    <thead>
                        <tr class="md-tr">
                            <th scope="col" class="md-th md-th-num">Fecha</th>
                            <th scope="col" class="md-th">Artículo</th>
                            <th scope="col" class="md-th md-th-num">Cantidad</th>
                            <th scope="col" class="md-th">Persona</th>
                            <th scope="col" class="md-th">Área</th>
                            <th scope="col" class="md-th">Usuario</th>
                            <th scope="col" class="md-th">Referencia</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($movimientos as $movimiento)
                            <tr class="md-tr">
                                <td class="md-td md-td-num">
                                    {{ $movimiento->fecha_movimiento->format('d/m/Y H:i') }}
                                </td>

                                <td class="md-td md-td-strong">
                                    {{ $movimiento->articulo->codigo }}
                                    -
                                    {{ $movimiento->articulo->nombre }}
                                </td>

                                <td class="md-td md-td-num">
                                    {{ number_format($movimiento->cantidad, 2, ',', '.') }}
                                </td>

                                <td class="md-td">{{ $movimiento->persona?->nombre_completo ?? '-' }}</td>

                                <td class="md-td">{{ $movimiento->persona?->area ?? '-' }}</td>

                                <td class="md-td">{{ $movimiento->usuario->name }}</td>

                                <td class="md-td">{{ $movimiento->referencia ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-tabla>
            @endif
        </section>
    </div>
</x-app-layout>