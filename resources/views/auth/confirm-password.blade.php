<x-guest-layout>
    <x-slot name="title">
        Confirmar contraseña
    </x-slot>

    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        Esta es una zona segura de la aplicación. Por favor, confirma tu contraseña para continuar.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Contraseña" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-6">
            <x-primary-button class="w-full sm:w-auto">
                Confirmar
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
