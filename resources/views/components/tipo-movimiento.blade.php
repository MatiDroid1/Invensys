@props(['tipo'])

@php
    /*
     * Los fondos usan el tono claro del color en vez de un verde/rojo planos:
     * con las tablas densas de movimientos, un `bg-green-100` sólido satura la
     * pantalla y compite con los números.
     */
    $estilos = [
        'ENTRADA' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300',
        'SALIDA' => 'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-300',
        'AJUSTE_POSITIVO' => 'bg-sky-50 text-sky-700 dark:bg-sky-500/15 dark:text-sky-300',
        'AJUSTE_NEGATIVO' => 'bg-amber-50 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300',
    ];

    $etiquetas = [
        'ENTRADA' => 'Entrada',
        'SALIDA' => 'Salida',
        'AJUSTE_POSITIVO' => 'Ajuste +',
        'AJUSTE_NEGATIVO' => 'Ajuste -',
    ];

    $clase = $estilos[$tipo] ?? 'bg-gray-100 text-gray-600 dark:bg-gray-700/70 dark:text-gray-300';
    $etiqueta = $etiquetas[$tipo] ?? $tipo;
@endphp

<span {{ $attributes->merge([
    'class' => 'inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-medium ' . $clase,
]) }}>{{ $etiqueta }}</span>
