<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Editar persona
        </h2>
    </x-slot>

    <x-pagina-form :action="route('personas.update', $persona)" :method="'PUT'">
        <x-persona-campos :persona="$persona" />

        <x-slot:acciones>
            <button type="submit" class="md-btn md-btn-filled">
                Guardar cambios
            </button>

            <a href="{{ route('personas.index') }}" class="md-btn md-btn-text">
                Cancelar
            </a>
        </x-slot:acciones>
    </x-pagina-form>
</x-app-layout>
