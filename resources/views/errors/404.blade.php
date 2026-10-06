<x-guest-layout>
    <x-slot name="title">
        Página no encontrada
    </x-slot>

    <div class="text-center">
        <p class="text-6xl font-bold text-indigo-600 dark:text-indigo-400">
            404
        </p>

        <h2 class="mt-4 text-lg font-semibold text-gray-900 dark:text-gray-100">
            No encontramos esa página
        </h2>

        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            La dirección que solicitaste no existe o fue movida a otra parte.
        </p>

        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-center">
            <a href="{{ route('dashboard') }}" class="md-btn md-btn-filled">
                Volver al panel
            </a>

            <a href="{{ url('/') }}" class="md-btn md-btn-outlined">
                Ir al inicio
            </a>
        </div>
    </div>
</x-guest-layout>
