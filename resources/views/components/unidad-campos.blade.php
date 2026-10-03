@props([
    'unidad' => null,
])

{{--
    Campos de alta y edición de una unidad de medida.

    Estaban duplicados en `create` y `edit`; la diferencia real entre ambas
    pantallas es el campo "Estado", que solo tiene sentido cuando la unidad ya
    existe y ya la usan artículos.
--}}
<div class="grid grid-cols-1 gap-x-6 gap-y-5">
    <div>
        <label for="nombre" class="md-label">Nombre</label>

        <input
            type="text"
            name="nombre"
            id="nombre"
            value="{{ old('nombre', $unidad?->nombre) }}"
            maxlength="50"
            required
            class="md-field"
        >

        <x-input-error :messages="$errors->get('nombre')" class="md-error" />
    </div>

    <div>
        <label for="abreviatura" class="md-label">Abreviatura</label>

        <input
            type="text"
            name="abreviatura"
            id="abreviatura"
            value="{{ old('abreviatura', $unidad?->abreviatura) }}"
            maxlength="10"
            class="md-field"
        >

        <p class="md-hint">Opcional. Es la que aparece junto a las cantidades en los movimientos.</p>

        <x-input-error :messages="$errors->get('abreviatura')" class="md-error" />
    </div>

    @if ($unidad)
        <div>
            <label for="activo" class="md-label">Estado</label>

            <select name="activo" id="activo" class="md-field" required>
                <option value="1" @selected(old('activo', $unidad->activo) == 1)>Activo</option>
                <option value="0" @selected(old('activo', $unidad->activo) == 0)>Inactivo</option>
            </select>

            <p class="md-hint">Desactivarla no cambia los movimientos ya registrados.</p>

            <x-input-error :messages="$errors->get('activo')" class="md-error" />
        </div>
    @endif
</div>