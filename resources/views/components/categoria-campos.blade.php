@props([
    'categoria' => null,
])

{{--
    Campos de alta y edición de una categoría.

    Los mismos campos estaban duplicados en `create` y `edit`; la única
    diferencia real entre ambas pantallas es el campo "Estado", que solo tiene
    sentido cuando la categoría ya existe.
--}}
<div class="grid grid-cols-1 gap-x-6 gap-y-5">
    <div>
        <label for="nombre" class="md-label">Nombre</label>

        <input
            type="text"
            name="nombre"
            id="nombre"
            value="{{ old('nombre', $categoria?->nombre) }}"
            maxlength="100"
            required
            class="md-field"
        >

        <x-input-error :messages="$errors->get('nombre')" class="md-error" />
    </div>

    <div>
        <label for="descripcion" class="md-label">Descripción</label>

        <textarea
            name="descripcion"
            id="descripcion"
            rows="4"
            class="md-field"
        >{{ old('descripcion', $categoria?->descripcion) }}</textarea>

        <p class="md-hint">Opcional. Se muestra como texto de ayuda en los listados.</p>

        <x-input-error :messages="$errors->get('descripcion')" class="md-error" />
    </div>

    @if ($categoria)
        <div>
            <label for="activo" class="md-label">Estado</label>

            <select name="activo" id="activo" class="md-field" required>
                <option value="1" @selected(old('activo', $categoria->activo) == 1)>Activo</option>
                <option value="0" @selected(old('activo', $categoria->activo) == 0)>Inactivo</option>
            </select>

            <p class="md-hint">Una categoría inactiva no se ofrece al crear artículos, pero no borra los que ya la usan.</p>

            <x-input-error :messages="$errors->get('activo')" class="md-error" />
        </div>
    @endif
</div>