<x-guest-layout>
    <x-slot name="title">
        Error del servidor
    </x-slot>

    <div class="text-center">
        <p class="text-6xl font-bold text-indigo-600 dark:text-indigo-400">
            500
        </p>

        <h2 class="mt-4 text-lg font-semibold text-gray-900 dark:text-gray-100">
            Algo salió mal
        </h2>

        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Ocurrió un problema al procesar tu solicitud. Ya quedó registrado para que podamos revisarlo.
        </p>

        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-center">
            <a href="{{ url()->current() }}" class="md-btn md-btn-filled">
                Reintentar
            </a>

            <a href="{{ url('/') }}" class="md-btn md-btn-outlined">
                Ir al inicio
            </a>
        </div>
    </div>
</x-guest-layout>
