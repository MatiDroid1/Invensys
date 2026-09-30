<x-guest-layout>
    <x-slot name="title">
        Recuperar contraseña
    </x-slot>

    @php
        /*
         * El formulario de abajo solo se muestra si hay un servidor de correo
         * real configurado. Con MAIL_MAILER=log (el valor por defecto) Laravel
         * no envía nada: escribe el correo en storage/logs/laravel.log y se
         * acaba. Mostrar el formulario ahí sería prometer un enlace que jamás
         * llega, dejando al usuario sin salida.
         */
        $correoConfigurado = config('mail.default') !== 'log';
    @endphp

    @if ($correoConfigurado)
        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            ¿Olvidaste tu contraseña? Indícanos tu correo electrónico y te enviaremos
            un enlace para que puedas elegir una nueva.
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div>
                <x-input-label for="email" value="Correo electrónico" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex justify-end mt-6">
                <x-primary-button class="w-full sm:w-auto">
                    Enviar enlace de recuperación
                </x-primary-button>
            </div>
        </form>
    @else
        <div class="mb-6 flex items-start gap-3 rounded-lg border border-amber-300 bg-amber-50 p-4 dark:border-amber-700 dark:bg-amber-950">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="mt-0.5 h-5 w-5 shrink-0 text-amber-600 dark:text-amber-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>

            <div class="text-sm">
                <p class="font-semibold text-amber-900 dark:text-amber-200">
                    La recuperación por correo no está disponible
                </p>
                <p class="mt-1 text-amber-800 dark:text-amber-300">
                    Este sistema no tiene un servidor de correo configurado, así que no
                    podemos enviarte un enlace de recuperación. Pide a un administrador
                    que la restablezca por consola.
                </p>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                Para que un administrador te la restablezca
            </p>

            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Necesitas que el usuario tenga el correo
                <span class="font-mono text-xs text-gray-900 dark:text-gray-100">{{ auth()->check() ? auth()->user()->email : 'con el que iniciaste sesión' }}</span>.
                Pásaselo a quien administra el sistema, que puede cambiarla con:
            </p>

            <pre class="mt-3 overflow-x-auto rounded bg-gray-900 px-3 py-2 text-xs text-gray-100"><code>php artisan usuario:clave CORREO</code></pre>
        </div>
    @endif
</x-guest-layout>
