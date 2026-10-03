<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="md-title">
                Nuevo artículo
            </h2>

            <a href="{{ route('articulos.index') }}" class="md-btn md-btn-text md-btn-sm">
                Volver al listado
            </a>
        </div>
    </x-slot>

    <x-pagina-form :action="route('articulos.store')">
        <section class="md-card-plain p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Registra un artículo. El stock inicial se registra después con una
                <a href="{{ route('movimientos.create') }}" class="font-medium text-indigo-600 hover:underline dark:text-indigo-400">
                    entrada de inventario
                </a>.
            </p>
        </section>

        @include('articulos.partials.form')

        <x-slot:acciones>
            <button type="submit" class="md-btn md-btn-filled">
                Guardar artículo
            </button>

            <a href="{{ route('articulos.index') }}" class="md-btn md-btn-text">
                Cancelar
            </a>
        </x-slot:acciones>
    </x-pagina-form>
</x-app-layout>