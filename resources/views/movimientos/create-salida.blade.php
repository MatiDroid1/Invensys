<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Nueva salida de inventario
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

                    <form method="POST" action="{{ route('movimientos.salida.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="articulo_id" class="block font-medium text-sm">
                                Artículo
                            </label>

                            <select
                                name="articulo_id"
                                id="articulo_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                required
                            >
                                <option value="">Seleccione un artículo</option>

                                @foreach ($articulos as $articulo)
                                    <option
                                        value="{{ $articulo->id }}"
                                        @selected(old('articulo_id', $articuloPreseleccionado?->id) == $articulo->id)
                                    >
                                        {{ $articulo->codigo }} - {{ $articulo->nombre }}
                                        (Stock: {{ number_format($articulo->stock_actual, 2, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="persona_id" class="block font-medium text-sm">
                                Persona que recibe
                            </label>

                            <select
                                name="persona_id"
                                id="persona_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                required
                            >
                                <option value="">Seleccione una persona</option>

                                @foreach ($personas as $persona)
                                    <option
                                        value="{{ $persona->id }}"
                                        @selected(old('persona_id') == $persona->id)
                                    >
                                        {{ $persona->nombre_completo }}
                                        @if ($persona->area)
                                            - {{ $persona->area }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="cantidad" class="block font-medium text-sm">
                                Cantidad
                            </label>

                            <input
                                type="number"
                                name="cantidad"
                                id="cantidad"
                                value="{{ old('cantidad') }}"
                                min="0.01"
                                step="0.01"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                required
                            >
                        </div>

                        <div class="mb-4">
                            <label for="fecha_movimiento" class="block font-medium text-sm">
                                Fecha y hora
                            </label>

                            <input
                                type="datetime-local"
                                name="fecha_movimiento"
                                id="fecha_movimiento"
                                value="{{ old('fecha_movimiento', now()->format('Y-m-d\TH:i')) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                required
                            >
                        </div>

                        <div class="mb-4">
                            <label for="referencia" class="block font-medium text-sm">
                                Referencia
                            </label>

                            <input
                                type="text"
                                name="referencia"
                                id="referencia"
                                value="{{ old('referencia') }}"
                                maxlength="100"
                                placeholder="Ej: Solicitud interna #456"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                            >
                        </div>

                        <div class="mb-6">
                            <label for="observaciones" class="block font-medium text-sm">
                                Observaciones
                            </label>

                            <textarea
                                name="observaciones"
                                id="observaciones"
                                rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                            >{{ old('observaciones') }}</textarea>
                        </div>

                        <div class="flex items-center gap-3">

                            <button
                                type="submit"
                                class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700"
                            >
                                Registrar salida
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