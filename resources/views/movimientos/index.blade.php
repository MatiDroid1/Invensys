<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="md-title">Movimientos de inventario</h2>

                <p class="md-subtitle mt-0.5">
                    Cada entrada, salida y ajuste queda asentado con su usuario y su referencia.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('movimientos.create') }}" class="md-btn md-btn-md md-btn-filled">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>

                    Nueva entrada
                </a>

                <a href="{{ route('movimientos.salida.create') }}" class="md-btn md-btn-md md-btn-tonal">
                    Nueva salida
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
            <x-alerta />

            <section class="md-card overflow-hidden">
                @if ($movimientos->isEmpty())
                    <x-estado-vacio
                        titulo="Sin movimientos"
                        descripcion="Todavía no hay entradas, salidas ni ajustes registrados."
                    >
                        <x-slot:accion>
                            <a href="{{ route('movimientos.create') }}" class="md-btn md-btn-md md-btn-filled">
                                Registrar la primera entrada
                            </a>
                        </x-slot:accion>
                    </x-estado-vacio>
                @else
                    <div class="overflow-x-auto">
                        <table class="md-table">
                            <thead>
                                <tr>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Artículo</th>
                                    <th scope="col">Tipo</th>
                                    <th scope="col" class="text-right">Cantidad</th>
                                    <th scope="col">Usuario</th>
                                    <th scope="col">Persona</th>
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

                                        <td>{{ $movimiento->usuario->name }}</td>

                                        <td>{{ $movimiento->persona?->nombre_completo ?? '—' }}</td>

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