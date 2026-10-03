<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Nuevo usuario
        </h2>
    </x-slot>

    <x-pagina-form :action="route('usuarios.store')">
        <div>
            <label for="name" class="md-label">
                Nombre
            </label>

            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name') }}"
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
                value="{{ old('email') }}"
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
                <option value="usuario" @selected(old('rol', 'usuario') === 'usuario')>
                    Usuario
                </option>

                <option value="admin" @selected(old('rol') === 'admin')>
                    Administrador
                </option>
            </select>
        </div>

        <div>
            <label for="password" class="md-label">
                Contraseña
            </label>

            <input
                type="password"
                name="password"
                id="password"
                minlength="8"
                required
                class="md-field mt-1"
            >
        </div>

        <div>
            <label for="password_confirmation" class="md-label">
                Confirmar contraseña
            </label>

            <input
                type="password"
                name="password_confirmation"
                id="password_confirmation"
                minlength="8"
                required
                class="md-field mt-1"
            >
        </div>

        <x-slot:acciones>
            <button type="submit" class="md-btn md-btn-filled">
                Crear usuario
            </button>

            <a href="{{ route('usuarios.index') }}" class="md-btn md-btn-text">
                Cancelar
            </a>
        </x-slot:acciones>
    </x-pagina-form>
</x-app-layout>