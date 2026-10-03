<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Editar categoría
        </h2>
    </x-slot>

    <x-pagina-form :action="route('categorias.update', $categoria)" :method="'PUT'">
        <div>
            <label for="nombre" class="md-label">
                Nombre
            </label>

            <input
                type="text"
                name="nombre"
                id="nombre"
                value="{{ old('nombre', $categoria->nombre) }}"
                maxlength="100"
                required
                autofocus
                class="md-field mt-1"
            >
        </div>

        <div>
            <label for="descripcion" class="md-label">
                Descripción
            </label>

            <textarea
                name="descripcion"
                id="descripcion"
                rows="4"
                class="md-field mt-1"
            >{{ old('descripcion', $categoria->descripcion) }}</textarea>
        </div>

        <div>
            <label for="activo" class="md-label">
                Estado
            </label>

            <select name="activo" id="activo" required class="md-field mt-1">
                <option value="1" @selected(old('activo', $categoria->activo) == 1)>
                    Activo
                </option>

                <option value="0" @selected(old('activo', $categoria->activo) == 0)>
                    Inactivo
                </option>
            </select>
        </div>

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