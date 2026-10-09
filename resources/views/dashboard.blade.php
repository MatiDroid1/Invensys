<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Panel
        </h2>
    </x-slot>

    <div class="md-page md-page-body space-y-6">
        {{-- Indicadores principales --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">

            <div class="md-stat">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="md-overline">
                            Artículos activos
                        </div>

                        <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $articulosActivos }}
                        </div>
                    </div>

                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                    </span>
                </div>
            </div>

            <div class="md-stat">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="md-overline">
                            Personas activas
                        </div>

                        <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $personasActivas }}
                        </div>
                    </div>

                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </span>
                </div>
            </div>

            <div class="md-stat">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="md-overline">
                            Movimientos este mes
                        </div>

                        <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $movimientosMes }}
                        </div>
                    </div>

                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                        </svg>
                    </span>
                </div>
            </div>

            <div class="md-stat">
                <div class="flex items-start justify-between gap-4">
                    <div>
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

                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </span>
                </div>
            </div>

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