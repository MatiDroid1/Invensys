<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Nuevo usuario
        </h2>
    </x-slot>

    <x-pagina-form :action="route('usuarios.store')">
        <x-usuario-campos />

        <x-slot:acciones>
            <button type="submit" class="md-btn md-btn-filled">
                Crear usuario
            </button>

            <a href="{{ route('usuarios.index') }}" class="md-btn md-btn-text">
                Cancelar
            </a>
        </x-slot:acciones>
    </x-pagina-form>
</x-app-layout>
