<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Editar usuario
        </h2>
    </x-slot>

    <x-pagina-form :action="route('usuarios.update', $usuario)" :method="'PUT'">
        <div>
            <label for="name" class="md-label">
                Nombre
            </label>

            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name', $usuario->name) }}"
                maxlength="255"
                required
                class="md-field mt-1"
            >
        </div>

        <div>
            <label for="email" class="md-label">
                Correo electrónico
            </label>

            <input
                type="email"
                name="email"
                id="email"
                value="{{ old('email', $usuario->email) }}"
                maxlength="255"
                required
                class="md-field mt-1"
            >
        </div>

        <div>
            <label for="rol" class="md-label">
                Rol
            </label>

            <select
                name="rol"
                id="rol"
                required
                class="md-field mt-1"
            >
                <option value="usuario" @selected(old('rol', $usuario->rol) === 'usuario')>
                    Usuario
                </option>

                <option value="admin" @selected(old('rol', $usuario->rol) === 'admin')>
                    Administrador
                </option>
            </select>
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
                <option value="1" @selected(old('activo', $usuario->activo) == 1)>
                    Activo
                </option>

                <option value="0" @selected(old('activo', $usuario->activo) == 0)>
                    Inactivo
                </option>
            </select>
        </div>

        <div>
            <label for="password" class="md-label">
                Nueva contraseña
            </label>

            <input
                type="password"
                name="password"
                id="password"
                minlength="8"
                class="md-field mt-1"
            >

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Déjalo vacío para conservar la contraseña actual.
            </p>
        </div>

        <div>
            <label for="password_confirmation" class="md-label">
                Confirmar nueva contraseña
            </label>

            <input
                type="password"
                name="password_confirmation"
                id="password_confirmation"
                minlength="8"
                class="md-field mt-1"
            >
        </div>

        <x-slot:acciones>
            <button type="submit" class="md-btn md-btn-filled">
                Guardar cambios
            </button>

            <a href="{{ route('usuarios.index') }}" class="md-btn md-btn-text">
                Cancelar
            </a>
        </x-slot:acciones>
    </x-pagina-form>
</x-app-layout>