<section class="space-y-5">
    <header>
        <h2 class="md-section-title">Eliminar cuenta</h2>

        <p class="md-subtitle mt-0.5">
            Una vez eliminada tu cuenta, todos sus datos se eliminarán permanentemente. Antes de
            continuar, descarga cualquier información que necesites conservar.
        </p>
    </header>

    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="md-btn md-btn-md md-btn-danger"
    >Eliminar cuenta</button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <x-slot:title>¿Seguro que deseas eliminar tu cuenta?</x-slot:title>

        <x-slot:footer>
            <button
                type="button"
                class="md-btn md-btn-md md-btn-outlined"
                x-on:click="$dispatch('close')"
            >
                Cancelar
            </button>

            <button type="submit" form="delete-user-form" class="md-btn md-btn-md md-btn-danger">
                Eliminar cuenta
            </button>
        </x-slot:footer>

        <form id="delete-user-form" method="post" action="{{ route('profile.destroy') }}">
            @csrf
            @method('delete')

            <p class="text-sm text-gray-600 dark:text-gray-400">
                Todos los datos de tu cuenta se eliminarán permanentemente. Ingresa tu contraseña para
                confirmar.
            </p>

            <div class="mt-5">
                <label for="password" class="md-label">Contraseña</label>

                <input
                    type="password"
                    name="password"
                    id="password"
                    autocomplete="current-password"
                    class="md-field"
                    placeholder="Tu contraseña"
                >

                <x-input-error :messages="$errors->userDeletion->get('password')" class="md-error" />
            </div>
        </form>
    </x-modal>
</section>