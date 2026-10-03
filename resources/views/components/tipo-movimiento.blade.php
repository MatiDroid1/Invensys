@props(['tipo'])

@php
    $variantes = [
        'ENTRADA' => ['md-badge-success', 'Entrada'],
        'SALIDA' => ['md-badge-danger', 'Salida'],
        'AJUSTE_POSITIVO' => ['md-badge-info', 'Ajuste +'],
        'AJUSTE_NEGATIVO' => ['md-badge-warning', 'Ajuste -'],
    ];

    [$clase, $etiqueta] = $variantes[$tipo] ?? ['md-badge', $tipo];
@endphp

{{--
    Un solo lugar decide el color de cada tipo de movimiento. Antes cada vista
    repetía el mismo `rounded-full bg-green-100` con su propio tono, así que el
    mismo "Ajuste +" salía de un color en el panel y de otro en el reporte.
--}}
<span {{ $attributes->merge(['class' => $clase]) }}>{{ $etiqueta }}</span>