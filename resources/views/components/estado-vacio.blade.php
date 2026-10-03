@props([
    'icono' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z',
    'titulo' => null,
    'descripcion' => null,
])

{{--
    Estado vacío.

    Un listado sin resultados tiene que explicar por qué está vacío y qué hacer
    al respecto. Antes cada pantalla repetía su ícono gigante, su texto y su
    enlace; ahora es el mismo bloque con el contenido de cada caso.

    La descripción llega por atributo cuando es una línea corta y por slot
    cuando hace falta lógica (por ejemplo, "no hay resultados" frente a "todavía
    no hay nada", según si hay filtros aplicados).
--}}
<div {{ $attributes->merge(['class' => 'flex flex-col items-center px-6 py-14 text-center']) }}>
    <span class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 text-gray-400 dark:bg-gray-700/60 dark:text-gray-500">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icono }}" />
        </svg>
    </span>

    @if ($titulo)
        <h3 class="mt-4 text-sm font-semibold text-gray-900 dark:text-white">
            {{ $titulo }}
        </h3>
    @endif

    @if (filled($descripcion) || ! $slot->isEmpty())
        <div class="mt-1 max-w-sm text-sm text-gray-500 dark:text-gray-400">
            {{ $descripcion ?? $slot }}
        </div>
    @endif

    @isset($accion)
        <div class="mt-5">
            {{ $accion }}
        </div>
    @endisset
</div>