@props(['active'])

@php
    /*
     * La entrada activa ya no se marca con una línea inferior de dos píxeles
     * sino con una "pastilla" de fondo. Con ocho entradas en la barra esa línea
     * competía con los separadores y la única referencia de dónde estaba el
     * usuario era una raya difícil de ver; la pastilla además deja el mismo
     * peso visual que usan los desplegables y el menú móvil.
     */
    $classes = ($active ?? false)
        ? 'inline-flex items-center rounded-lg px-3 py-2 text-sm font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300'
        : 'inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>