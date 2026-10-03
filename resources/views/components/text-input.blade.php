@props(['disabled' => false])

{{--
    Campo de texto.

    Antes cada formulario repetía `border-gray-300 shadow-sm rounded-md` con
    combinaciones distintas de focus. Ahora todos los campos pasan por `.md-field`
    y el halo de foco es el mismo en toda la aplicación.
--}}
<input @disabled($disabled) {{ $attributes->merge(['class' => 'md-field']) }}>