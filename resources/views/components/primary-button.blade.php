<button {{ $attributes->merge(['type' => 'submit', 'class' => 'md-btn md-btn-filled']) }}>
    {{ $slot }}
</button>