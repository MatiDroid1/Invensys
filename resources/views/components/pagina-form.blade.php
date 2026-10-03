@props([
    'titulo',
    'ruta',
    'rutaListado',
    'metodo' => 'POST',
    'textoAccion' => 'Guardar',
    'textoVolver' => 'Volver al listado',
    'ancho' => 'max-w-4xl',
])

{{--
    Contenedor de las pantallas de alta y edición.

    Antes cada create/edit repetía el mismo esqueleto (cabecera con "volver",
    tarjeta blanca, formulario, botón guardar y cancelar): seis archivos con
    diferencias de espaciado entre uno y otro. Aquí el esqueleto está una vez y
    cada pantalla pone solo sus campos.

    El bloque de errores del formulario va aquí porque se repetía igual en todas
    y era la única parte que se veía igual en todas: una lista de viñetas rojas
    arriba del todo.
--}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="md-title">{{ $titulo }}</h2>

            <div class="flex flex-wrap items-center gap-2">
                {{-- Acción secundaria opcional de la cabecera (p. ej. "Ver detalle"). --}}
                {{ $extras ?? '' }}

                <a href="{{ $rutaListado }}" class="md-btn md-btn-sm md-btn-outlined">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>

                    {{ $textoVolver }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto {{ $ancho }} px-4 sm:px-6 lg:px-8">
            <section class="md-card p-6">
                @isset($descripcion)
                    <div class="mb-6 rounded-xl bg-gray-50 px-4 py-3 text-sm text-gray-600 dark:bg-gray-700/40 dark:text-gray-300">
                        {{ $descripcion }}
                    </div>
                @endisset

                @if ($errors->any())
                    <div class="mb-6 rounded-2xl bg-red-50 px-4 py-3 dark:bg-red-500/10">
                        <p class="text-sm font-semibold text-red-800 dark:text-red-200">
                            Revisa estos {{ $errors->count() }}
                            {{ $errors->count() === 1 ? 'campo' : 'campos' }}:
                        </p>

                        <ul class="mt-1 space-y-0.5 text-sm text-red-700 dark:text-red-300">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ $ruta }}">
                    @csrf

                    @if ($metodo !== 'POST')
                        @method($metodo)
                    @endif

                    {{ $slot }}

                    <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:items-center">
                        <button type="submit" class="md-btn md-btn-md md-btn-filled">
                            {{ $textoAccion }}
                        </button>

                        <a href="{{ $rutaListado }}" class="md-btn md-btn-md md-btn-outlined">
                            Cancelar
                        </a>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>