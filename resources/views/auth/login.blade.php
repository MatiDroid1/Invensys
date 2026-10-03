<x-guest-layout>
    <x-slot name="title">
        Iniciar sesión
    </x-slot>

    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <label for="email" class="md-label">Correo electrónico</label>

            <input
                type="email"
                name="email"
                id="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                class="md-field"
            >

            <x-input-error :messages="$errors->get('email')" class="md-error" />
        </div>

        <div class="mt-5">
            <div class="flex items-baseline justify-between gap-3">
                <label for="password" class="md-label">Contraseña</label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="mb-1.5 text-sm text-indigo-600 hover:underline dark:text-indigo-400">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>

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

        <div class="mt-5">
            <label for="remember_me" class="inline-flex cursor-pointer items-center gap-2">
                <input
                    type="checkbox"
                    name="remember"
                    id="remember_me"
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                >

                <span class="text-sm text-gray-600 dark:text-gray-400">Recordarme</span>
            </label>
        </div>

        <div class="mt-7">
            <button type="submit" class="md-btn md-btn-md md-btn-filled w-full justify-center">
                Iniciar sesión
            </button>
        </div>
    </form>
</x-guest-layout>