<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Nuevo artículo
            </h2>

            <a
                href="{{ route('articulos.index') }}"
                class="text-sm text-gray-600 hover:underline dark:text-gray-300"
            >
                Volver al listado
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">
                        Registra un artículo. El stock inicial se registra después con una
                        <a href="{{ route('movimientos.create') }}" class="font-medium text-indigo-600 hover:underline dark:text-indigo-400">
                            entrada de inventario
                        </a>.
                    </p>

                    <form method="POST" action="{{ route('articulos.store') }}">
                        @csrf

                        @include('articulos.partials.form')

                        <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:items-center">
                            <button
                                type="submit"
                                class="w-full sm:w-auto px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition"
                            >
                                Guardar artículo
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
