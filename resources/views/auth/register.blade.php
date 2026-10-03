<x-guest-layout>
    <x-slot name="title">
        Crear cuenta
    </x-slot>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <label for="name" class="md-label">Nombre</label>

            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                class="md-field"
            >

            <x-input-error :messages="$errors->get('name')" class="md-error" />
        </div>

        <div class="mt-5">
            <label for="email" class="md-label">Correo electrónico</label>

            <input
                type="email"
                name="email"
                id="email"
                value="{{ old('email') }}"
                required
                autocomplete="username"
                class="md-field"
            >

            <x-input-error :messages="$errors->get('email')" class="md-error" />
        </div>

        <div class="mt-5">
            <label for="password" class="md-label">Contraseña</label>

            <input
                type="password"
                name="password"
                id="password"
                required
                autocomplete="new-password"
                class="md-field"
            >

            <x-input-error :messages="$errors->get('password')" class="md-error" />
        </div>

        <div class="mt-5">
            <label for="password_confirmation" class="md-label">Confirmar contraseña</label>

            <input
                type="password"
                name="password_confirmation"
                id="password_confirmation"
                required
                autocomplete="new-password"
                class="md-field"
            >

            <x-input-error :messages="$errors->get('password_confirmation')" class="md-error" />
        </div>

        <div class="mt-7 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:underline dark:text-indigo-400">
                ¿Ya tienes una cuenta?
            </a>

            <button type="submit" class="md-btn md-btn-md md-btn-filled justify-center">
                Crear cuenta
            </button>
        </div>
    </form>
</x-guest-layout>