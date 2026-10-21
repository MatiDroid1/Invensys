<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Editar usuario
        </h2>
    </x-slot>

    <x-pagina-form :action="route('usuarios.update', $usuario)" :method="'PUT'">
        <x-usuario-campos :usuario="$usuario" />

        <x-slot:acciones>
            <button type="submit" class="md-btn md-btn-filled">
                Guardar cambios
            </button>

            <a href="{{ route('usuarios.index') }}" class="md-btn md-btn-text">
                Cancelar
            </a>
        </x-slot:acciones>
    </x-pagina-form>
</x-app-layout>
