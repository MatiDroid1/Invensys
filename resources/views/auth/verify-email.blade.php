<x-guest-layout>
    <x-slot name="title">
        Verifica tu correo electrónico
    </x-slot>

    <p class="mb-5 text-sm text-gray-600 dark:text-gray-400">
        ¡Gracias por registrarte! Antes de comenzar, confirma tu correo electrónico haciendo clic en el
        enlace que te acabamos de enviar. Si no lo recibiste, con gusto te enviamos otro.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-5 rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-200">
            Se envió un nuevo enlace de verificación al correo electrónico que registraste.
        </div>
    @endif

    <div class="mt-7 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="md-btn md-btn-md md-btn-text">
                Cerrar sesión
            </button>
        </form>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <button type="submit" class="md-btn md-btn-md md-btn-filled">
                Reenviar correo de verificación
            </button>
        </form>
    </div>
</x-guest-layout>
