<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="md-title">
                Editar artículo
            </h2>

            <a href="{{ route('articulos.show', $articulo) }}" class="md-btn md-btn-text md-btn-sm">
                Ver detalle
            </a>
        </div>
    </x-slot>

    <x-pagina-form :action="route('articulos.update', $articulo)" :method="'PUT'">
        <section class="md-card-plain p-4">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:gap-3">
                <span class="md-overline">
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
        </section>

        @include('articulos.partials.form')

        <x-slot:acciones>
            <button type="submit" class="md-btn md-btn-filled">
                Guardar cambios
            </button>

            <a href="{{ route('articulos.index') }}" class="md-btn md-btn-text">
                Cancelar
            </a>
        </x-slot:acciones>
    </x-pagina-form>
</x-app-layout>