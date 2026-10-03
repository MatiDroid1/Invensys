<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Nueva categoría
        </h2>
    </x-slot>

    <x-pagina-form :action="route('categorias.store')">
        <div>
            <label for="nombre" class="md-label">
                Nombre
            </label>

            <input
                type="text"
                name="nombre"
                id="nombre"
                value="{{ old('nombre') }}"
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
            >{{ old('descripcion') }}</textarea>
        </div>

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