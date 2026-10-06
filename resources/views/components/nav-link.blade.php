@props(['active' => false])

{{--
    Elemento de navegación de la barra superior.

    Antes llevaba el subrayado de Jetstream (`border-b-2` con `pt-1`), que
    además de ser de otro sistema visual se alineaba peor que los desplegables
    vizinhos: cada enlace quedaba con una altura y una línea base distintas.
    Ahora todos los destinos son cápsulas de la misma altura y el destino activo
    se marca con el fondo tonal, sin garisear nada.
--}}
@php
    $classes = 'md-nav-item' . ($active ? ' md-nav-item-active' : '');
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>