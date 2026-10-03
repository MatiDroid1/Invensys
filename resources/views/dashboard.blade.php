<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Panel
        </h2>
    </x-slot>

    <div class="md-page md-page-body space-y-6">
        {{-- Indicadores principales --}}
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">

            <div class="md-stat">
                <div class="md-overline">
                    Artículos activos
                </div>

                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $articulosActivos }}
                </div>
            </div>

            <div class="md-stat">
                <div class="md-overline">
                    Personas activas
                </div>

                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $personasActivas }}
                </div>
            </div>

            <div class="md-stat">
                <div class="md-overline">
                    Movimientos este mes
                </div>

                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $movimientosMes }}
                </div>
            </div>

            <div class="md-stat">
                <div class="md-overline">
                    Stock bajo
                </div>

                <div @class([
                    'mt-2 text-3xl font-bold',
                    'text-red-600' => $articulosStockBajo->count() > 0,
                    'text-green-600' => $articulosStockBajo->count() === 0,
                ])>
                    {{ $articulosStockBajo->count() }}
                </div>
            </div>

                @endif
            </section>
        </div>

        {{-- Stock bajo --}}
        <section class="md-card">
            <div class="flex flex-col gap-3 px-5 pb-4 pt-5 sm:flex-row sm:items-center sm:justify-between sm:px-6 sm:pt-6">
                <h3 class="md-section-title">
                    Alertas de stock
                </h3>

                <a
                    href="{{ route('articulos.index') }}"
                    class="md-btn md-btn-text md-btn-sm"
                >
                    Ver artículos
                </a>
            </div>

            @if ($articulosStockBajo->isEmpty())

                <x-estado-vacio
                    :descripcion="'No hay artículos con stock igual o inferior al mínimo.'"
                />

            @else

                <x-tabla>
                    <thead>
                        <tr class="md-tr">
                            <th scope="col" class="md-th">
                                Código
                            </th>

                            <th scope="col" class="md-th">
                                Artículo
                            </th>

                            <th scope="col" class="md-th md-th-num">
                                Stock actual
                            </th>

                            <th scope="col" class="md-th md-th-num">
                                Stock mínimo
                            </th>

                            <th scope="col" class="md-th">
                                Estado
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($articulosStockBajo as $articulo)
                            <tr class="md-tr">
                                <td class="md-td font-mono text-gray-500 dark:text-gray-400">
                                    {{ $articulo->codigo }}
                                </td>

                                <td class="md-td md-td-strong">
                                    <span class="md-td-clip" title="{{ $articulo->nombre }}">
                                        {{ $articulo->nombre }}
                                    </span>
                                </td>

                                <td class="md-td md-td-num font-semibold">
                                    {{ number_format($articulo->stock_calculado, 2, ',', '.') }}
                                </td>

                                <td class="md-td md-td-num">
                                    {{ number_format($articulo->stock_minimo, 2, ',', '.') }}
                                </td>

                                <td class="md-td">
                                    <span class="md-badge md-badge-danger">
                                        Stock bajo
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-tabla>

            @endif
        </section>

        {{-- Últimos movimientos --}}
        <section class="md-card">
            <div class="flex flex-col gap-3 px-5 pb-4 pt-5 sm:flex-row sm:items-center sm:justify-between sm:px-6 sm:pt-6">
                <h3 class="md-section-title">
                    Últimos movimientos
                </h3>

                <a
                    href="{{ route('movimientos.index') }}"
                    class="md-btn md-btn-text md-btn-sm"
                >
                    Ver todos
                </a>
            </div>

            @if ($ultimosMovimientos->isEmpty())

                <x-estado-vacio
                    :descripcion="'No hay movimientos registrados.'"
                />

            @else

                <x-tabla>
                    <thead>
                        <tr class="md-tr">
                            <th scope="col" class="md-th md-th-num">
                                Fecha
                            </th>

                            <th scope="col" class="md-th">
                                Artículo
                            </th>

                            <th scope="col" class="md-th">
                                Tipo
                            </th>

                            <th scope="col" class="md-th md-th-num">
                                Cantidad
                            </th>

                            <th scope="col" class="md-th">
                                Persona
                            </th>

                            <th scope="col" class="md-th">
                                Usuario
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($ultimosMovimientos as $movimiento)
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
                            </tr>
                        @endforeach
                    </tbody>
                </x-tabla>

            @endif
        </section>
    </div>
</x-app-layout>