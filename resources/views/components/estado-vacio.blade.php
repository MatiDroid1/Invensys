@props([
    'titulo' => null,
    'descripcion' => null,
    'icono' => null,
])

{{--
    Estado vacío de una lista o buscador.

    Antes cada pantalla repetía su propio párrafo centrado con un `<p>` suelto,
    y cada uno con un margen distinto, así que los "no hay datos" no coincidían
    ni en altura ni en posición entre tablas.

    El icono es opcional porque no todas las listas necesitan uno: en las
    tablas estrechas solo se usa el texto.
--}}
<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center px-6 py-14 text-center']) }}>
    @if ($icono)
        <span class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-gray-700/60 dark:text-gray-500">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-7 w-7">
                {!! $icono !!}
            </svg>
        </span>
    @endif

    @if ($titulo)
        <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $titulo }}</p>
    @endif

    @if ($descripcion)
        <p class="mt-1 max-w-md text-sm text-gray-500 dark:text-gray-400">{{ $descripcion }}</p>
    @endif

    @isset($accion)
        <div class="mt-6">{{ $accion }}</div>
    @endisset
</div>