<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Nueva categoría
        </h2>
    </x-slot>

    <x-pagina-form :action="route('categorias.store')">
        <x-categoria-campos />

        <x-slot:acciones>
            <button type="submit" class="md-btn md-btn-filled">
                Guardar categoría
            </button>

            <a href="{{ route('categorias.index') }}" class="md-btn md-btn-text">
                Cancelar
            </a>
        </x-slot:acciones>
    </x-pagina-form>
</x-app-layout>
