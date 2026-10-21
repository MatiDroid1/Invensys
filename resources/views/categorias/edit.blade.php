<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Editar categoría
        </h2>
    </x-slot>

    <x-pagina-form :action="route('categorias.update', $categoria)" :method="'PUT'">
        <x-categoria-campos :categoria="$categoria" />

        <x-slot:acciones>
            <button type="submit" class="md-btn md-btn-filled">
                Guardar cambios
            </button>

            <a href="{{ route('categorias.index') }}" class="md-btn md-btn-text">
                Cancelar
            </a>
        </x-slot:acciones>
    </x-pagina-form>
</x-app-layout>
