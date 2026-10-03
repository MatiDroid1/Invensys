<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="md-title">
                Movimientos de inventario
            </h2>

            <div class="flex flex-wrap items-center gap-2">
                <a
                    href="{{ route('movimientos.create') }}"
                    class="md-btn md-btn-filled"
                >
                    Nueva entrada
                </a>

                <a
                    href="{{ route('movimientos.salida.create') }}"
                    class="md-btn md-btn-tonal"
                >
                    Nueva salida
                </a>
            </div>
        </div>
    </x-slot>

    <div class="md-page md-page-body space-y-6">
        <x-alerta />

        <section class="md-card">
            @if ($movimientos->isEmpty())
                <x-estado-vacio :descripcion="'No hay movimientos registrados.'" />
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
                                Usuario
                            </th>

                            <th scope="col" class="md-th md-th-sticky">
                                Persona
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
                                    {{ $movimiento->articulo->codigo }}
                                    -
                                    {{ $movimiento->articulo->nombre }}
                                </td>

                                <td class="md-td">
                                    <span class="md-chip">
                                        {{ $movimiento->tipo }}
                                    </span>
                                </td>

                                <td class="md-td md-td-num">
                                    {{ number_format($movimiento->cantidad, 2, ',', '.') }}
                                </td>

                                <td class="md-td">
                                    {{ $movimiento->usuario->name }}
                                </td>

                                <td class="md-td">
                                    {{ $movimiento->persona?->nombre_completo ?? '-' }}
                                </td>

                                <td class="md-td">
                                    {{ $movimiento->referencia ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-tabla>
            @endif
        </section>
    </div>
</x-app-layout>