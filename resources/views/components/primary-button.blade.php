{{--
    Botón principal de la pantalla. Todo el esqueleto (cápsula, foco, compresión
    al apretar) viene de `.md-btn`; el relleno, de `.md-btn-filled`.

    Antes llevaba `uppercase tracking-widest text-xs`, que es el estilo por
    defecto de Laravel, no el de Material: en Material el rótulo va en caja
    normal y con `text-sm`.
--}}
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'md-btn md-btn-md md-btn-filled']) }}>
    {{ $slot }}
</button>