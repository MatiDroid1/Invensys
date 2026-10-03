<x-guest-layout>
    <x-slot name="title">
        Confirmar contraseña
    </x-slot>

    <p class="mb-5 text-sm text-gray-600 dark:text-gray-400">
        Esta es una zona segura de la aplicación. Por favor, confirma tu contraseña para continuar.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div>
            <label for="password" class="md-label">Contraseña</label>

            <input
                type="password"
                name="password"
                id="password"
                required
                autocomplete="current-password"
                class="md-field"
            >

            <x-input-error :messages="$errors->get('password')" class="md-error" />
        </div>

        <div class="mt-7">
            <button type="submit" class="md-btn md-btn-md md-btn-filled w-full justify-center">
                Confirmar
            </button>
        </div>
    </form>
</x-guest-layout>