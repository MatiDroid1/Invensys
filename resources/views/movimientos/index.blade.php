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

                <a
                    href="{{ route('movimientos.ajuste.create') }}"
                    class="md-btn md-btn-outlined"
                >
                    Nuevo ajuste
                </a>
            </div>
        </div>
    </x-slot>

    <div
        class="md-page md-page-body space-y-6"
        x-data="{ motivo: '', accion: '' }"
        x-on:confirmar-anulacion.window="motivo = ''; accion = $event.detail.accion"
    >
        {{-- Un solo modal para toda la tabla: se rellena con el movimiento
             elegido desde la fila, en lugar de imprimir un confirm() del
             navegador por cada fila. --}}
        <x-modal name="anular-movimiento" maxWidth="md">
            <form method="POST" x-bind:action="accion">
                @csrf

                <div class="p-6">
                    <h2 class="md-section-title">Anular movimiento</h2>

                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        El movimiento no se borra: queda marcado como anulado y se
                        registra un movimiento compensatorio para que el stock
                        siga cuadrando. Indica el motivo, que queda en la
                        auditoría.
                    </p>

                    <div class="mt-4">
                        <label for="motivo" class="md-label">Motivo</label>

                        <textarea
                            name="motivo"
                            id="motivo"
                            rows="3"
                            maxlength="150"
                            x-model="motivo"
                            class="md-field mt-1"
                            required
                        ></textarea>

                        <x-input-error :messages="$errors->get('motivo')" class="md-error" />
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 bg-gray-50 px-6 py-4 dark:bg-gray-800/60 sm:flex-row sm:justify-end">
                    <button type="button" class="md-btn md-btn-text" x-on:click="$dispatch('close')">
                        Cancelar
                    </button>

                    <button type="submit" class="md-btn md-btn-danger">
                        Anular movimiento
                    </button>
                </div>
            </form>
        </x-modal>

        <x-alerta />

        {{-- Filtros --}}
        <section class="md-card mx-auto w-full max-w-6xl p-5 sm:p-6">
            <form method="GET" action="{{ route('movimientos.index') }}">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">

                    <div class="sm:col-span-2">
                        <label for="articulo_id" class="md-label">
                            Artículo
                        </label>

                        <select
                            name="articulo_id"
                            id="articulo_id"
                            class="md-field mt-1"
                        >
                            <option value="">Todos</option>

                            @foreach ($articulos as $articulo)
                                <option
                                    value="{{ $articulo->id }}"
                                    @selected(request('articulo_id') == $articulo->id)
                                >
                                    {{ $articulo->codigo }} - {{ $articulo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="tipo" class="md-label">
                            Tipo
                        </label>

                        <select
                            name="tipo"
                            id="tipo"
                            class="md-field mt-1"
                        >
                            <option value="">Todos</option>

                            @foreach (['ENTRADA' => 'Entrada', 'SALIDA' => 'Salida', 'AJUSTE_POSITIVO' => 'Ajuste +', 'AJUSTE_NEGATIVO' => 'Ajuste -'] as $valor => $texto)
                                <option value="{{ $valor }}" @selected(request('tipo') === $valor)>
                                    {{ $texto }}
                                </option>
                            @endforeach
                        </select>
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
                            <option value="">Todas</option>

                            @foreach ($personas as $persona)
                                <option
                                    value="{{ $persona->id }}"
                                    @selected(request('persona_id') == $persona->id)
                                >
                                    {{ $persona->nombre_completo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="desde" class="md-label">
                            Desde
                        </label>

                        <input
                            type="date"
                            name="desde"
                            id="desde"
                            value="{{ request('desde') }}"
                            class="md-field mt-1"
                        >
                    </div>

                    <div>
                        <label for="hasta" class="md-label">
                            Hasta
                        </label>

                        <input
                            type="date"
                            name="hasta"
                            id="hasta"
                            value="{{ request('hasta') }}"
                            class="md-field mt-1"
                        >
                    </div>

                </div>

                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <button
                        type="submit"
                        class="md-btn md-btn-filled"
                    >
                        Filtrar
                    </button>

                    @if (request()->hasAny(['articulo_id', 'tipo', 'persona_id', 'desde', 'hasta']))
                        <a
                            href="{{ route('movimientos.index') }}"
                            class="md-btn md-btn-text md-btn-sm"
                        >
                            Limpiar filtros
                        </a>
                    @endif
                </div>
            </form>
        </section>

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

                            <th scope="col" class="md-th md-th-sticky md-th-num">
                                Costo unit.
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

                            @if (auth()->user()->puede('movimientos.anular'))
                                <th scope="col" class="md-th md-th-sticky">
                                    Acciones
                                </th>
                            @endif
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
                                    <x-tipo-movimiento
                                        :tipo="$movimiento->tipo"
                                        :estado="$movimiento->estado"
                                    />
                                </td>

                                <td class="md-td md-td-num">
                                    {{ number_format($movimiento->cantidad, 2, ',', '.') }}
                                </td>

                                <td class="md-td md-td-num">
                                    <x-dinero :monto="$movimiento->costo_unitario !== null ? (float) $movimiento->costo_unitario : null" />
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

                                @if (auth()->user()->puede('movimientos.anular'))
                                    <td class="md-td">
                                        @if ($movimiento->estaAnulado())
                                            <span
                                                class="text-xs text-gray-400 dark:text-gray-500"
                                                title="{{ $movimiento->motivo }}"
                                            >
                                                Anulado por {{ $movimiento->anuladoPor?->name ?? 'sistema' }}
                                            </span>
                                        @else
                                            <button
                                                type="button"
                                                class="md-btn md-btn-text md-btn-sm !text-red-600 dark:!text-red-300"
                                                @click="$dispatch('confirmar-anulacion', {
                                                    accion: @js(route('movimientos.anular', $movimiento))
                                                }); $dispatch('open-modal', 'anular-movimiento')"
                                            >
                                                Anular
                                            </button>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </x-tabla>

                @if ($movimientos->hasPages())
                    <div class="border-t border-gray-100 px-5 py-4 dark:border-gray-700/70">
                        {{ $movimientos->links() }}
                    </div>
                @endif
            @endif
        </section>
    </div>
</x-app-layout>
