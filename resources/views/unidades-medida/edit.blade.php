<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Editar unidad de medida
        </h2>
    </x-slot>

    <x-pagina-form :action="route('unidades-medida.update', $unidadMedida)" :method="'PUT'">
        <x-unidad-campos :unidad="$unidadMedida" />

        <x-slot:acciones>
            <button type="submit" class="md-btn md-btn-filled">
                Guardar cambios
            </button>

            <a href="{{ route('unidades-medida.index') }}" class="md-btn md-btn-text">
                Cancelar
            </a>
        </x-slot:acciones>
    </x-pagina-form>
</x-app-layout>
