@props([
    'usuario' => null,
])

{{--
    Campos de alta y edición de un usuario.

    Ambos formularios eran copias el uno del otro salvo por la contraseña (que en
    edición es opcional) y el estado (que solo existe si el usuario ya está
    creado). Un solo componente con esas dos diferencias evita que vuelvan a
    separarse.
--}}
<div class="grid grid-cols-1 gap-x-6 gap-y-5 md:grid-cols-2">
    <div>
        <label for="name" class="md-label">Nombre</label>

        <input
            type="text"
            name="name"
            id="name"
            value="{{ old('name', $usuario?->name) }}"
            maxlength="255"
            required
            class="md-field"
        >

        <x-input-error :messages="$errors->get('name')" class="md-error" />
    </div>

    <div>
        <label for="email" class="md-label">Correo electrónico</label>

        <input
            type="email"
            name="email"
            id="email"
            value="{{ old('email', $usuario?->email) }}"
            maxlength="255"
            required
            class="md-field"
        >

        <x-input-error :messages="$errors->get('email')" class="md-error" />
    </div>

    <div>
        <label for="rol" class="md-label">Rol</label>

        <select name="rol" id="rol" class="md-field" required>
            <option value="usuario" @selected(old('rol', $usuario?->rol ?? 'usuario') === 'usuario')>
                Usuario
            </option>

            <option value="admin" @selected(old('rol', $usuario?->rol) === 'admin')>
                Administrador
            </option>
        </select>

        <p class="md-hint">
            Un administrador accede a artículos, personas, usuarios y reportes.
        </p>

        <x-input-error :messages="$errors->get('rol')" class="md-error" />
    </div>

    @if ($usuario)
        <div>
            <label for="activo" class="md-label">Estado</label>

            <select name="activo" id="activo" class="md-field" required>
                <option value="1" @selected(old('activo', $usuario->activo) == 1)>Activo</option>
                <option value="0" @selected(old('activo', $usuario->activo) == 0)>Inactivo</option>
            </select>

            <p class="md-hint">Un usuario inactivo no puede iniciar sesión.</p>

            <x-input-error :messages="$errors->get('activo')" class="md-error" />
        </div>
    @endif

    <div>
        <label for="password" class="md-label">
            {{ $usuario ? 'Nueva contraseña' : 'Contraseña' }}
        </label>

        <input
            type="password"
            name="password"
            id="password"
            minlength="8"
            @required(! $usuario)
            autocomplete="new-password"
            class="md-field"
        >

        <p class="md-hint">
            {{ $usuario ? 'Déjalo vacío para conservar la contraseña actual.' : 'Mínimo 8 caracteres.' }}
        </p>

        <x-input-error :messages="$errors->get('password')" class="md-error" />
    </div>

    <div>
        <label for="password_confirmation" class="md-label">
            {{ $usuario ? 'Confirmar nueva contraseña' : 'Confirmar contraseña' }}
        </label>

        <input
            type="password"
            name="password_confirmation"
            id="password_confirmation"
            minlength="8"
            @required(! $usuario)
            autocomplete="new-password"
            class="md-field"
        >

        <x-input-error :messages="$errors->get('password_confirmation')" class="md-error" />
    </div>
</div>