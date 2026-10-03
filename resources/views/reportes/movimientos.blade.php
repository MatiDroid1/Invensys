<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Reporte de movimientos
        </h2>
    </x-slot>

    <div class="md-page md-page-body space-y-6">
        {{-- Filtros --}}
        <section class="md-card mx-auto w-full max-w-5xl p-5 sm:p-6">
            <form method="GET" action="{{ route('reportes.movimientos') }}">

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">

                    <div>
                        <label
                            for="fecha_desde"
                            class="md-label"
                        >
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
                        <label
                            for="fecha_hasta"
                            class="md-label"
                        >
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
                        <label
                            for="articulo_id"
                            class="md-label"
                        >
                            Artículo
                        </label>

                        <select
                            name="articulo_id"
                            id="articulo_id"
                            class="md-field mt-1"
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
                            class="md-label"
                        >
                            Tipo de movimiento
                        </label>

                        <select
                            name="tipo"
                            id="tipo"
                            class="md-field mt-1"
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

                <div class="mt-6 flex flex-wrap items-center gap-3">

                    <button
                        type="submit"
                        class="md-btn md-btn-filled"
                    >
                        Buscar
                    </button>

                    <a
                        href="{{ route('reportes.movimientos') }}"
                        class="md-btn md-btn-text"
                    >
                        Limpiar filtros
                    </a>

                    <a
                        href="{{ route('reportes.movimientos.csv', request()->query()) }}"
                        class="md-btn md-btn-tonal"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>

                        Descargar CSV
                    </a>

                </div>

            </form>
        </section>

        {{-- Resultados --}}
        <section class="md-card">
            <div class="px-5 pb-4 pt-5 sm:px-6 sm:pt-6">
                <h3 class="md-section-title">
                    Movimientos encontrados:
                    {{ $movimientos->count() }}
                </h3>
            </div>

            @if ($movimientos->isEmpty())

                <x-estado-vacio
                    :descripcion="'No se encontraron movimientos con los filtros seleccionados.'"
                />

            @else

                {{-- Historial extenso: el alto máximo deja el encabezado fijo al recorrerlo. --}}
                <x-tabla :alto="true">
                    <thead>
                        <tr class="md-tr">
                            <th scope="col" class="md-th md-th-sticky md-th-num">
                                Fecha
                            </th>

                            <th scope="col" class="md-th md-th-sticky">
                                Artículo
                            </th>

                            <th scope="col" class="md-th md-th-sticky">
                                Tipo
                            </th>

                            <th scope="col" class="md-th md-th-sticky md-th-num">
                                Cantidad
                            </th>

                            <th scope="col" class="md-th md-th-sticky">
                                Persona
                            </th>

                            <th scope="col" class="md-th md-th-sticky">
                                Usuario
                            </th>

                            <th scope="col" class="md-th md-th-sticky">
                                Referencia
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($movimientos as $movimiento)
                            <tr class="md-tr">
                                <td class="md-td md-td-num">
                                    {{ $movimiento->fecha_movimiento->format('d/m/Y H:i') }}
                                </td>

                                <td class="md-td md-td-strong">
                                    <span class="md-td-clip" title="{{ $movimiento->articulo->codigo }} - {{ $movimiento->articulo->nombre }}">
                                        {{ $movimiento->articulo->codigo }} - {{ $movimiento->articulo->nombre }}
                                    </span>
                                </td>

                                <td class="md-td">
                                    <x-tipo-movimiento :tipo="$movimiento->tipo" />
                                </td>

                                <td class="md-td md-td-num">
                                    {{ number_format($movimiento->cantidad, 2, ',', '.') }}
                                </td>

                                <td class="md-td">
                                    {{ $movimiento->persona?->nombre_completo ?? '-' }}
                                </td>

                                <td class="md-td">
                                    {{ $movimiento->usuario->name }}
                                </td>

                                <td class="md-td">
                                    <span class="md-td-clip" title="{{ $movimiento->referencia ?? '-' }}">
                                        {{ $movimiento->referencia ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-tabla>

                {{-- El reporte pagina de 50 en 50; sin estos enlaces las entregas
                     que caen fuera de la primera página no eran alcanzables. --}}
                <div class="md-divider mt-5 px-5 pb-5 pt-5">
                    {{ $movimientos->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>