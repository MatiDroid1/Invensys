<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Editar unidad de medida
        </h2>
    </x-slot>

    <x-pagina-form :action="route('unidades-medida.update', $unidadMedida)" :method="'PUT'">
        <div>
            <label for="nombre" class="md-label">
                Nombre
            </label>

            <input
                type="text"
                name="nombre"
                id="nombre"
                value="{{ old('nombre', $unidadMedida->nombre) }}"
                maxlength="50"
                required
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
                value="{{ old('abreviatura', $unidadMedida->abreviatura) }}"
                maxlength="10"
                class="md-field mt-1"
            >
        </div>

        <div>
            <label for="activo" class="md-label">
                Estado
            </label>

            <select
                name="activo"
                id="activo"
                required
                class="md-field mt-1"
            >
                <option value="1" @selected(old('activo', $unidadMedida->activo) == 1)>
                    Activo
                </option>

                <option value="0" @selected(old('activo', $unidadMedida->activo) == 0)>
                    Inactivo
                </option>
            </select>
        </div>

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