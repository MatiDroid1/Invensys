<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Ajuste de inventario
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-md">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-6 p-4 bg-yellow-50 text-yellow-800 rounded-md">
                        Un ajuste modifica el saldo del inventario sin borrar ni modificar
                        movimientos anteriores.
                    </div>

                    <form method="POST" action="{{ route('movimientos.ajuste.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label
                                for="articulo_id"
                                class="block font-medium text-sm"
                            >
                                Artículo
                            </label>

                            <select
                                name="articulo_id"
                                id="articulo_id"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                            >
                                <option value="">
                                    Seleccione un artículo
                                </option>

                                @foreach ($articulos as $articulo)
                                    <option
                                        value="{{ $articulo->id }}"
                                        @selected(old('articulo_id', $articuloPreseleccionado?->id) == $articulo->id)
                                    >
                                        {{ $articulo->codigo }}
                                        -
                                        {{ $articulo->nombre }}
                                        (Stock:
                                        {{ number_format($articulo->stock_actual, 2, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label
                                for="tipo"
                                class="block font-medium text-sm"
                            >
                                Tipo de ajuste
                            </label>

                            <select
                                name="tipo"
                                id="tipo"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                            >
                                <option value="">
                                    Seleccione un tipo
                                </option>

                                <option
                                    value="AJUSTE_POSITIVO"
                                    @selected(old('tipo') === 'AJUSTE_POSITIVO')
                                >
                                    Ajuste positivo
                                </option>

                                <option
                                    value="AJUSTE_NEGATIVO"
                                    @selected(old('tipo') === 'AJUSTE_NEGATIVO')
                                >
                                    Ajuste negativo
                                </option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label
                                for="cantidad"
                                class="block font-medium text-sm"
                            >
                                Cantidad
                            </label>

                            <input
                                type="number"
                                name="cantidad"
                                id="cantidad"
                                value="{{ old('cantidad') }}"
                                min="0.01"
                                step="0.01"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                            >
                        </div>

                        <div class="mb-4">
                            <label
                                for="fecha_movimiento"
                                class="block font-medium text-sm"
                            >
                                Fecha y hora
                            </label>

                            <input
                                type="datetime-local"
                                name="fecha_movimiento"
                                id="fecha_movimiento"
                                value="{{ old('fecha_movimiento', now()->format('Y-m-d\TH:i')) }}"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                            >
                        </div>

                        <div class="mb-4">
                            <label
                                for="referencia"
                                class="block font-medium text-sm"
                            >
                                Referencia
                            </label>

                            <input
                                type="text"
                                name="referencia"
                                id="referencia"
                                value="{{ old('referencia') }}"
                                maxlength="100"
                                placeholder="Ej: Conteo físico mensual"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                            >
                        </div>

                        <div class="mb-6">
                            <label
                                for="observaciones"
                                class="block font-medium text-sm"
                            >
                                Observaciones
                            </label>

                            <textarea
                                name="observaciones"
                                id="observaciones"
                                rows="4"
                                placeholder="Indique el motivo del ajuste"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                            >{{ old('observaciones') }}</textarea>
                        </div>

                        <div class="flex items-center gap-3">

                            <button
                                type="submit"
                                class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700"
                            >
                                Registrar ajuste
                            </button>

                            <a
                                href="{{ route('movimientos.index') }}"
                                class="px-4 py-2 text-gray-600 dark:text-gray-300"
                            >
                                Cancelar
                            </a>

                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>