@props(['tipo', 'estado' => 'CONFIRMADO'])

@php
    $variantes = [
        'ENTRADA' => ['md-badge-success', 'Entrada'],
        'SALIDA' => ['md-badge-danger', 'Salida'],
        'AJUSTE_POSITIVO' => ['md-badge-info', 'Ajuste +'],
        'AJUSTE_NEGATIVO' => ['md-badge-warning', 'Ajuste -'],
    ];

    [$clase, $etiqueta] = $variantes[$tipo] ?? ['md-badge', $tipo];

    $anulado = $estado === 'ANULADO';
@endphp

{{--
    Un solo lugar decide el color de cada tipo de movimiento. Antes cada vista
    repetía el mismo `rounded-full bg-green-100` con su propio tono, así que el
    mismo "Ajuste +" salía de un color en el panel y de otro en el reporte.

    Cuando el movimiento está anulado se marca como tal: el hecho de que el
    movimiento siga en el historial no significa que siga en vigor.
--}}
<span {{ $attributes->merge(['class' => $clase]) }}>{{ $etiqueta }}</span>

@if ($anulado)
    <span {{ $attributes->merge(['class' => 'md-badge']) }}>Anulado</span>
@endif
