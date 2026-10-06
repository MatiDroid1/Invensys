@props([
    'align' => 'left',
    'active' => false,
])

{{--
    Destino con menú desplegable en la barra superior.

    Antes cada desplegable repetía su propio bloque de Alpine y su propio
    `<div>` (y dos de ellos quedaban sin sangrar en el archivo). Aquí se resuelve
    una sola vez, con el mismo comportamiento en todos: abrir, cerrar al hacer
    clic fuera y con `Esc`.

    El botón usa `md-nav-item`, la misma clase que un enlace normal, para que la
    fila se lea como una sola tira y no como dos estilos distintos.
--}}
<div x-data="{ abierto: false }" @keydown.escape.window="abierto = false" class="relative">
    <button
        type="button"
        @click="abierto = !abierto"
        :aria-expanded="abierto"
        aria-haspopup="true"
        class="md-nav-item {{ $active ? 'md-nav-item-active' : '' }}"
    >
        {{ $trigger }}

        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
            class="h-4 w-4 shrink-0 transition-transform duration-200 ease-standard"
            :class="abierto && 'rotate-180'"
        >
            <path
                fill-rule="evenodd"
                d="M5.293 7.293a1 1 0 011.414 0L10 11.586l4.707-4.293a1 1 0 011.414 1.414l-5.414 5a1 1 0 01-1.414 0l-5.414-5a1 1 0 010-1.414z"
                clip-rule="evenodd"
            />
        </svg>
    </button>

    <div
        x-show="abierto"
        @click.outside="abierto = false"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        style="display: none;"
        class="md-menu origin-top {{ $align === 'right' ? 'right-0' : 'left-0' }}"
    >
        {{ $slot }}
    </div>
</div>