@props(['active' => false])

{{--
    Destino del menú desplegable (el que aparece por debajo de `lg`).

    Mantiene la fila con el icono a la izquierda y el texto a la derecha, así
    que todos los destinos del menú tienen la misma altura y el título del grupo
    se lee como separador, no como otro enlace.
--}}
@php
    $classes = 'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors '
        . ($active
            ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-200'
            : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700');
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>