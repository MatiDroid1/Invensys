@props([
    'action' => null,
    'method' => 'POST',
    'ancho' => 'max-w-3xl',
])

{{--
    Esqueleto de las pantallas de alta y edición.

    Estas pantallas se repetían enteras en cada `create` y `edit`: mismo `div`
    blanco, mismo `p-4 sm:p-8`, mismos botones al pie. Lo único que cambia entre
    pares es el título y el botón, así que el esqueleto vive una sola vez.

    `ancho` limita la columna de campos a un ancho cómodo de leer y la centra
    dentro del contenedor de página.
--}}
<form method="POST" action="{{ $action }}" {{ $attributes->except('class') }}>
    @csrf

    @if (strtoupper($method) !== 'POST')
        @method($method)
    @endif

    <div class="md-page md-page-body">
        <div class="{{ $ancho }} mx-auto space-y-5">
            <x-alerta />

            <section class="md-card p-5 sm:p-6">
                {{ $slot }}
            </section>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center">
                {{ $acciones }}
            </div>
        </div>
    </div>
</form>