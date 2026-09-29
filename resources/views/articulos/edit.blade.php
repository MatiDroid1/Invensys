<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Editar artículo
            </h2>

            <a
                href="{{ route('articulos.show', $articulo) }}"
                class="text-sm text-gray-600 hover:underline dark:text-gray-300"
            >
                Ver detalle
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 dark:bg-gray-900/40 dark:border-gray-700 dark:text-gray-100">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:gap-3">
                    <span class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Código
                    </span>
                    <span class="font-mono font-semibold">{{ $articulo->codigo }}</span>
                    <span class="hidden sm:inline text-gray-300 dark:text-gray-600">|</span>
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        Stock actual:
                        <span class="font-semibold text-gray-900 dark:text-gray-100">
                            {{ number_format($articulo->stock_actual, 2, ',', '.') }}
                        </span>
                    </span>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('articulos.update', $articulo) }}">
                        @csrf
                        @method('PUT')

                        @include('articulos.partials.form')

                        <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:items-center">
                            <button
                                type="submit"
                                class="w-full sm:w-auto px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition"
                            >
                                Guardar cambios
                            </button>

                            <a
                                href="{{ route('articulos.index') }}"
                                class="w-full sm:w-auto px-4 py-2 text-center text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:text-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:hover:bg-gray-700"
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
