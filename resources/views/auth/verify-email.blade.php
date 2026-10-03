<x-guest-layout>
    <x-slot name="title">
        Verifica tu correo electrónico
    </x-slot>

    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        ¡Gracias por registrarte! Antes de comenzar, confirma tu correo electrónico haciendo clic en el
        enlace que te acabamos de enviar. Si no lo recibiste, con gusto te enviamos otro.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 p-4 text-sm font-medium text-green-800 bg-green-50 border border-green-200 rounded-md dark:bg-green-900/30 dark:text-green-200 dark:border-green-800">
            Se envió un nuevo enlace de verificación al correo electrónico que registraste.
        </div>
    @endif

    <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                Cerrar sesión
            </button>
        </form>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <x-primary-button class="w-full sm:w-auto">
                Reenviar correo de verificación
            </x-primary-button>
        </form>
    </div>
</x-guest-layout>
