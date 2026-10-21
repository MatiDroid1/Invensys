<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Nueva entrada de inventario
        </h2>
    </x-slot>

    <x-pagina-form :action="route('movimientos.store')">
        <x-movimiento-campos
            :articulos="$articulos"
            :articulo-preseleccionado="$articuloPreseleccionado"
            :mostrar-stock="false"
            :mostrar-costo="true"
            placeholder-referencia="Ej: Envío Casa Matriz #123"
        />

        <x-slot:acciones>
            <button type="submit" class="md-btn md-btn-filled">
                Registrar entrada
            </button>

            <a href="{{ route('dashboard') }}" class="md-btn md-btn-text">
                Cancelar
            </a>
        </x-slot:acciones>
    </x-pagina-form>
</x-app-layout>
