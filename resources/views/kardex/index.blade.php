<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Kardex de inventario
        </h2>
    </x-slot>

    <div class="md-page md-page-body space-y-6">
        <section class="md-card mx-auto w-full max-w-3xl p-5 sm:p-6">
            <form method="GET" action="{{ route('kardex.index') }}">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end">

                    <div class="min-w-0 flex-1">
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
                                Seleccione un artículo
                            </option>

                            @foreach ($articulos as $item)
                                <option
                                    value="{{ $item->id }}"
                                    @selected($articulo?->id == $item->id)
                                >
                                    {{ $item->codigo }} - {{ $item->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <button
                            type="submit"
                            class="md-btn md-btn-filled w-full sm:w-auto"
                        >
                            Consultar Kardex
                        </button>
                    </div>

                </div>
            </form>
        </section>

        @if ($articulo)

            <section class="md-card p-5 sm:p-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">

                    <div>
                        <div class="md-subtitle">
                            Código
                        </div>

                        <div class="md-section-title">
                            {{ $articulo->codigo }}
                        </div>
                    </div>

                    <div>
                        <div class="md-subtitle">
                            Artículo
                        </div>

                        <div class="md-section-title">
                            {{ $articulo->nombre }}
                        </div>
                    </div>

                    <div>
                        <div class="md-subtitle">
                            Stock mínimo
                        </div>

                        <div class="md-section-title">
                            {{ number_format($articulo->stock_minimo, 2, ',', '.') }}
                        </div>
                    </div>

                    <div>
                        <div class="md-subtitle">
                            Stock actual
                        </div>

                        <div class="md-section-title text-lg">
                            {{ number_format($articulo->stock_actual, 2, ',', '.') }}
                        </div>
                    </div>

                </div>
            </section>

            <section class="md-card">
                @if ($movimientos->isEmpty())

                    <x-estado-vacio :descripcion="'Este artículo no tiene movimientos registrados.'" />

                @else

                    {{-- Historial extenso: el alto máximo deja el encabezado fijo al recorrerlo. --}}
                    <x-tabla :alto="true">
                        <thead>
                            <tr class="md-tr">
                                <th scope="col" class="md-th md-th-sticky md-th-num">
                                    Fecha
                                </th>

                                <th scope="col" class="md-th md-th-sticky">
                                    Tipo
                                </th>

                                <th scope="col" class="md-th md-th-sticky">
                                    Referencia
                                </th>

                                <th scope="col" class="md-th md-th-sticky">
                                    Persona
                                </th>

                                <th scope="col" class="md-th md-th-sticky md-th-num">
                                    Entrada
                                </th>

                                <th scope="col" class="md-th md-th-sticky md-th-num">
                                    Salida
                                </th>

                                <th scope="col" class="md-th md-th-sticky md-th-num">
                                    Saldo
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
                                        <span class="md-chip">
                                            {{ $movimiento->tipo }}
                                        </span>
                                    </td>

                                    <td class="md-td">
                                        {{ $movimiento->referencia ?? '-' }}
                                    </td>

                                    <td class="md-td">
                                        {{ $movimiento->persona?->nombre_completo ?? '-' }}
                                    </td>

                                    <td class="md-td md-td-num">
                                        @if ($movimiento->entrada > 0)
                                            {{ number_format($movimiento->entrada, 2, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td class="md-td md-td-num">
                                        @if ($movimiento->salida > 0)
                                            {{ number_format($movimiento->salida, 2, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td class="md-td md-td-num md-td-strong">
                                        {{ number_format($movimiento->saldo, 2, ',', '.') }}
                                    </td>
                                </tr>

                            @endforeach
                        </tbody>
                    </x-tabla>

                @endif
            </section>

        @endif
    </div>
</x-app-layout>