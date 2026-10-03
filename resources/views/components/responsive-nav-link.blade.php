@props(['active', 'tono' => 'normal'])

@php
    /*
     * Mismo criterio de estado que la barra de escritorio (pastilla en vez de
     * filete), para que el menú móvil y la barra se lean como la misma pieza.
     *
     * `tono` cumple la misma función que en `x-nav-dropdown-link`: cambiar el
     * color desde afuera con `class` no alcanza, porque en Tailwind manda el
     * orden del CSS generado y no el del atributo.
     */
    $base = 'flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm';

    $paletas = [
        'normal' => [
            'activo' => 'bg-indigo-50 font-semibold text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300',
            'inactivo' => 'font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800',
        ],
        'peligro' => [
            'activo' => 'bg-red-50 font-semibold text-red-700 dark:bg-red-500/10 dark:text-red-300',
            'inactivo' => 'font-medium text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10',
        ],
    ];

    $paleta = $paletas[$tono] ?? $paletas['normal'];
    $classes = $base.' '.($active ?? false ? $paleta['activo'] : $paleta['inactivo']);
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} @if ($active ?? false) aria-current="page" @endif>
    @if (isset($icon))
        <span @class([
            'shrink-0',
            'text-current opacity-70' => $tono === 'peligro',
            'text-gray-400 dark:text-gray-500' => $tono !== 'peligro',
        ])>{{ $icon }}</span>
    @endif

    <span class="truncate">{{ $slot }}</span>
</a>