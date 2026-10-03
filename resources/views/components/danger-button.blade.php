{{-- Acción destructiva. Se mantiene el relleno rojo sólido: borrar es lo que
     el usuario tiene que confirmar mirando de frente, no algo discreto. --}}
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'md-btn md-btn-md md-btn-danger']) }}>
    {{ $slot }}
</button>