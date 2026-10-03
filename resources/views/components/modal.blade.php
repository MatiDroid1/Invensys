@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl'
])

@php
$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

<div
    x-data="{
        show: @js($show),
        focusables() {
            // All focusable element types...
            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'
            return [...$el.querySelectorAll(selector)]
                // All non-disabled elements...
                .filter(el => ! el.hasAttribute('disabled'))
        },
        firstFocusable() { return this.focusables()[0] },
        lastFocusable() { return this.focusables().slice(-1)[0] },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
        prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) -1 },
    }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-y-hidden');
            {{ $attributes->has('focusable') ? 'setTimeout(() => firstFocusable().focus(), 100)' : '' }}
        } else {
            document.body.classList.remove('overflow-y-hidden');
        }
    })"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()"
    x-show="show"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
    style="display: {{ $show ? 'block' : 'none' }};"
    >
        <div
            x-show="show"
            class="fixed inset-0 transform transition-all"
            x-on:click="show = false"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            {{-- El velo se difumina en vez de quedar como una placa gris: en
                 Material el fondo se atenúa, no se tapa. --}}
            <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-[2px]"></div>
        </div>

        {{--
                `relative` no es decorativo: el velo de fondo es un
                `fixed inset-0` situado antes en el DOM. Un panel sin
                posicionamiento propio se pinta por debajo de ese velo, y el
                diálogo acaba cubierto por el fondo difuminado y sin recibir
                clics, así que cualquier toque lo cerraba. Con `relative` ambos
                están en la misma capa y, al ir este después, queda encima.
            --}}
        <div
            x-show="show"
            class="relative mb-6 mx-auto w-[calc(100%-1.5rem)] overflow-hidden rounded-3xl bg-white shadow-e4 ring-1 ring-gray-900/5 transition-all duration-300 ease-standard dark:bg-gray-800 dark:ring-white/10 sm:w-full {{ $maxWidth }}"
            role="dialog"
            aria-modal="true"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-6 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-6 sm:translate-y-0 sm:scale-95"
        >
            {{--
                Un diálogo de Material tiene tres zonas: título, contenido y
                acciones. Se apoya en slots con nombre para que cada pantalla no
                tenga que inventar su propio paddín ni sus propios bordes.

                Si no se pasan `title` ni `footer`, el contenido es el slot
                normal: los modales que ya existían siguen funcionando igual.

                El panel NO lleva `max-h`: crece lo justo para que el texto quepa
                entero dentro, sin barra de desplazamiento propia. El ancho sale
                de `maxWidth`, y `w-[calc(100%-1.5rem)]` sólo lo separa de los
                bordes en pantallas angostas.
            --}}
            @if (filled($title ?? null))
                {{-- `min-w-0` y `break-words` evitan que una palabra larga (un nombre de
                     artículo, un código) desborde el recuadro y quede cortada
                     contra el `overflow-hidden` del panel. --}}
                <div class="flex items-start justify-between gap-4 px-6 pb-1 pt-6">
                    <h2 class="min-w-0 break-words text-lg font-semibold tracking-tight text-gray-900 dark:text-white">
                        {{ $title }}
                    </h2>

                    <button
                        type="button"
                        x-on:click="show = false"
                        class="md-icon-btn -me-2 -mt-1 h-9 w-9"
                        aria-label="Cerrar"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            {{-- Sin `max-h` ni desplazamiento propio: el texto fluye y ocupa el alto que
                 necesita, así que el mensaje completo queda a la vista dentro
                 del recuadro. `break-words` es lo que evita que una palabra
                 larga desborde el ancho y acabe recortada. --}}
            <div class="min-w-0 break-words px-6 py-5">
                {{ isset($content) ? $content : $slot }}
            </div>

            @if (isset($footer))
                <div class="flex flex-wrap items-center justify-end gap-2 border-t border-gray-100 px-6 py-4 dark:border-gray-700/70">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
