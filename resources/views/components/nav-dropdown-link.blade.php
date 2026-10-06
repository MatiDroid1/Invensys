@props(['href', 'activo' => false, 'tono' => 'normal'])

@php
    /*
     * Fila de un panel desplegable. El icono llega como slot con nombre
     * (`x-slot name="icon"`) y es opcional, para no obligar a dibujarlo en
     * todas las entradas.
     *
     * `tono` existe por una razón concreta: pasar `class="text-red-600"` desde
     * afuera NO funciona. Tailwind no resuelve por el orden del atributo sino
     * por el orden del CSS generado, así que el `text-gray-700` de base le
     * ganaría al rojo y "Cerrar sesión" saldría gris. Con el tono se cambia la
     * paleta completa, sin dejar dos clases enfrentadas.
     */
    $base = 'flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-sm transition-colors duration-100';

    $paletas = [
        'normal' => [
            'inactivo' => 'text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700/60 dark:hover:text-white',
            'activo' => 'bg-indigo-50 font-semibold text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300',
        ],
        'peligro' => [
            'inactivo' => 'text-red-600 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10 dark:hover:text-red-300',
            'activo' => 'bg-red-50 font-semibold text-red-700 dark:bg-red-500/10 dark:text-red-300',
        ],
    ];

    $paleta = $paletas[$tono] ?? $paletas['normal'];
@endphp

<a
    href="{{ $href }}"
    {{ $attributes->merge(['class' => $base.' '.($activo ? $paleta['activo'] : $paleta['inactivo'])]) }}
    @if ($activo) aria-current="page" @endif
>
    @if (isset($icon))
        {{-- El icono hereda el color del texto: si no, un "Cerrar sesión" rojo
             con icono gris se ve como dos elementos distintos. --}}
        <span @class([
            'shrink-0',
            'text-current opacity-70' => $tono === 'peligro',
            'text-gray-400 dark:text-gray-500' => $tono !== 'peligro',
        ])>{{ $icon }}</span>
    @endif

    <span class="truncate">{{ $slot }}</span>
</a>