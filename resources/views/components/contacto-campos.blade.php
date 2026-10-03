@props([
    'nombre' => null,
    'email' => null,
])

{{--
    Campos del formulario de contacto.

    Se precargan con los datos del usuario conectado: casi siempre se consulta
    por lo mismo que ya se sabe, y tener que reescribirlo es ruido. `old()`
    tiene prioridad para que al fallar la validación no se pierda lo escrito.
--}}
<div class="grid grid-cols-1 gap-x-6 gap-y-5 md:grid-cols-2">
    <div>
        <label for="nombre" class="md-label">Nombre</label>

        <input
            type="text"
            name="nombre"
            id="nombre"
            value="{{ old('nombre', $nombre ?? auth()->user()?->name) }}"
            maxlength="150"
            required
            class="md-field"
        >

        <x-input-error :messages="$errors->get('nombre')" class="md-error" />
    </div>

    <div>
        <label for="email" class="md-label">Correo electrónico</label>

        <input
            type="email"
            name="email"
            id="email"
            value="{{ old('email', $email ?? auth()->user()?->email) }}"
            maxlength="150"
            required
            class="md-field"
        >

        <x-input-error :messages="$errors->get('email')" class="md-error" />
    </div>

    <div class="md:col-span-2">
        <label for="asunto" class="md-label">Asunto</label>

        <input
            type="text"
            name="asunto"
            id="asunto"
            value="{{ old('asunto') }}"
            maxlength="150"
            required
            placeholder="Ej: Consulta sobre inventario"
            class="md-field"
        >

        <x-input-error :messages="$errors->get('asunto')" class="md-error" />
    </div>

    <div class="md:col-span-2">
        <label for="mensaje" class="md-label">Mensaje</label>

        <textarea
            name="mensaje"
            id="mensaje"
            rows="6"
            required
            placeholder="Escribe tu mensaje..."
            class="md-field"
        >{{ old('mensaje') }}</textarea>

        <x-input-error :messages="$errors->get('mensaje')" class="md-error" />
    </div>
</div>