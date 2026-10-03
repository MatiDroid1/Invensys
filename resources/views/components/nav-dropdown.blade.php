@props(['label', 'activo' => false])

@php
    /*
     * Botón + panel de los menús desplegables de la barra (Movimientos,
     * Reportes, Administración). Antes ese markup estaba copiado tres veces, cada
     * una con su propia sangría; los tres menús se parecían entre sí pero
     * cambiaban en detalles, que es justo como aparecen los descuadres.
     */
    $inactivo = 'inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white';
    $seleccionado = 'inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300';
@endphp

<div
    x-data="{ abierto: {{ $activo ? 'true' : 'false' }} }"
    @keydown.escape.window="abierto = false"
    class="relative"
>
    <button
        type="button"
        @click="abierto = !abierto"
        :aria-expanded="abierto.toString()"
        {{ $attributes->merge(['class' => $activo ? $seleccionado : $inactivo]) }}
    >
        {{ $label }}

        <svg
            class="h-4 w-4 shrink-0 transition-transform duration-200"
            :class="abierto ? 'rotate-180' : ''"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
            aria-hidden="true"
        >
            <path
                fill-rule="evenodd"
                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l4.707-4.293a1 1 0 011.414 1.414l-5.414 5a1 1 0 01-1.414 0l-5.414-5a1 1 0 010-1.414z"
                clip-rule="evenodd"
            />
        </svg>
    </button>

    <div
        x-show="abierto"
        @click.outside="abierto = false"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
        style="display: none;"
        class="absolute start-0 top-full mt-2 w-60 rounded-2xl bg-white p-1.5 shadow-e3 ring-1 ring-gray-900/5 dark:bg-gray-800 dark:ring-white/10 z-50"
    >
        {{ $slot }}
    </div>
</div>