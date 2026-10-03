@props([
    'persona' => null,
])

{{--
    Campos de alta y edición de una persona.

    Eran los mismos seis campos duplicados en `create` y `edit`; la diferencia
    real entre ambas pantallas es el campo "Estado", que solo tiene sentido
    cuando la persona ya existe.
--}}
<div class="grid grid-cols-1 gap-x-6 gap-y-5 md:grid-cols-2">
    <div>
        <label for="nombre" class="md-label">Nombre</label>

        <input
            type="text"
            name="nombre"
            id="nombre"
            value="{{ old('nombre', $persona?->nombre) }}"
            maxlength="100"
            required
            class="md-field"
        >

        <x-input-error :messages="$errors->get('nombre')" class="md-error" />
    </div>

    <div>
        <label for="apellido" class="md-label">Apellido</label>

        <input
            type="text"
            name="apellido"
            id="apellido"
            value="{{ old('apellido', $persona?->apellido) }}"
            maxlength="100"
            required
            class="md-field"
        >

        <x-input-error :messages="$errors->get('apellido')" class="md-error" />
    </div>

    <div>
        <label for="identificador" class="md-label">Identificador</label>

        <input
            type="text"
            name="identificador"
            id="identificador"
            value="{{ old('identificador', $persona?->identificador) }}"
            maxlength="50"
            placeholder="Ej: RUT, código interno, etc."
            class="md-field"
        >

        <p class="md-hint">Sirve para identificar a la persona en la entrega de materiales.</p>

        <x-input-error :messages="$errors->get('identificador')" class="md-error" />
    </div>

    <div>
        <label for="email" class="md-label">Correo electrónico</label>

        <input
            type="email"
            name="email"
            id="email"
            value="{{ old('email', $persona?->email) }}"
            maxlength="150"
            class="md-field"
        >

        <x-input-error :messages="$errors->get('email')" class="md-error" />
    </div>

    <div>
        <label for="area" class="md-label">Área</label>

        <input
            type="text"
            name="area"
            id="area"
            value="{{ old('area', $persona?->area) }}"
            maxlength="100"
            class="md-field"
        >

        <x-input-error :messages="$errors->get('area')" class="md-error" />
    </div>

    <div>
        <label for="cargo" class="md-label">Cargo</label>

        <input
            type="text"
            name="cargo"
            id="cargo"
            value="{{ old('cargo', $persona?->cargo) }}"
            maxlength="100"
            class="md-field"
        >

        <x-input-error :messages="$errors->get('cargo')" class="md-error" />
    </div>

    @if ($persona)
        <div>
            <label for="activo" class="md-label">Estado</label>

            <select name="activo" id="activo" class="md-field" required>
                <option value="1" @selected(old('activo', $persona->activo) == 1)>Activo</option>
                <option value="0" @selected(old('activo', $persona->activo) == 0)>Inactivo</option>
            </select>

            <p class="md-hint">Una persona inactiva no puede recibir nuevas salidas.</p>

            <x-input-error :messages="$errors->get('activo')" class="md-error" />
        </div>
    @endif
</div>