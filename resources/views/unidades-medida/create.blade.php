<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Nueva unidad de medida
        </h2>
    </x-slot>

    <x-pagina-form :action="route('unidades-medida.store')">
        <div>
            <label for="nombre" class="md-label">
                Nombre
            </label>

            <input
                type="text"
                name="nombre"
                id="nombre"
                value="{{ old('nombre') }}"
                maxlength="50"
                required
                placeholder="Ej: Unidad"
                class="md-field mt-1"
            >
        </div>

        <div>
            <label for="abreviatura" class="md-label">
                Abreviatura
            </label>

            <input
                type="text"
                name="abreviatura"
                id="abreviatura"
                value="{{ old('abreviatura') }}"
                maxlength="10"
                placeholder="Ej: un"
                class="md-field mt-1"
            >
        </div>

        <x-slot:acciones>
            <button type="submit" class="md-btn md-btn-filled">
                Guardar unidad
            </button>

            <a href="{{ route('unidades-medida.index') }}" class="md-btn md-btn-text">
                Cancelar
            </a>
        </x-slot:acciones>
    </x-pagina-form>
</x-app-layout>