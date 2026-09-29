@props(['tipo'])

@php
    $estilos = [
        'ENTRADA' => 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300',
        'SALIDA' => 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300',
        'AJUSTE_POSITIVO' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300',
        'AJUSTE_NEGATIVO' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/50 dark:text-orange-300',
    ];

    $etiquetas = [
        'ENTRADA' => 'Entrada',
        'SALIDA' => 'Salida',
        'AJUSTE_POSITIVO' => 'Ajuste +',
        'AJUSTE_NEGATIVO' => 'Ajuste -',
    ];

    $clase = $estilos[$tipo] ?? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300';
    $etiqueta = $etiquetas[$tipo] ?? $tipo;
@endphp

<span {{ $attributes->merge([
    'class' => 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap ' . $clase,
]) }}>{{ $etiqueta }}</span>
