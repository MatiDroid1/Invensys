{{-- Acción secundaria: mismo esqueleto, relleno con contorno. --}}
<button {{ $attributes->merge(['type' => 'button', 'class' => 'md-btn md-btn-md md-btn-outlined']) }}>
    {{ $slot }}
</button>