<section>
    <header>
        <h2 class="md-section-title">Actualizar contraseña</h2>

        <p class="md-subtitle mt-0.5">Usa una contraseña larga y aleatoria para mantener tu cuenta segura.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="md-label">Contraseña actual</label>

            <input
                type="password"
                name="current_password"
                id="update_password_current_password"
                autocomplete="current-password"
                class="md-field"
            >

            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="md-error" />
        </div>

        <div>
            <label for="update_password_password" class="md-label">Nueva contraseña</label>

            <input
                type="password"
                name="password"
                id="update_password_password"
                autocomplete="new-password"
                class="md-field"
            >

            <x-input-error :messages="$errors->updatePassword->get('password')" class="md-error" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="md-label">Confirmar contraseña</label>

            <input
                type="password"
                name="password_confirmation"
                id="update_password_password_confirmation"
                autocomplete="new-password"
                class="md-field"
            >

            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="md-error" />
        </div>

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <button type="submit" class="md-btn md-btn-md md-btn-filled">Guardar</button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-emerald-600 dark:text-emerald-400"
                >Guardado.</p>
            @endif
        </div>
    </form>
</section>