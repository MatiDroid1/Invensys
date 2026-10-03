<x-guest-layout>
    <x-slot name="title">
        Restablecer contraseña
    </x-slot>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label for="email" class="md-label">Correo electrónico</label>

            <input
                type="email"
                name="email"
                id="email"
                value="{{ old('email', $request->email) }}"
                required
                autofocus
                autocomplete="username"
                class="md-field"
            >

            <x-input-error :messages="$errors->get('email')" class="md-error" />
        </div>

        <div class="mt-5">
            <label for="password" class="md-label">Nueva contraseña</label>

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

        <div class="mt-7">
            <button type="submit" class="md-btn md-btn-md md-btn-filled w-full justify-center">
                Restablecer contraseña
            </button>
        </div>
    </form>
</x-guest-layout>