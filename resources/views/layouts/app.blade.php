<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Invensys') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Tema -->
        @include('layouts.tema')

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        {{-- El notificador vive en el layout para que funcione en artículos,
             movimientos, kardex, reportes y cualquier otra pantalla.
             En el chat no se monta porque el hilo ya se encarga de pintarlo. --}}
        @php($esChat = request()->routeIs('mensajes.show'))

        <div
            @if (! $esChat)
                x-data="notificadorMensajes({
                    urlEstado: '{{ route('mensajes.estado') }}',
                    urlSonido: '{{ route('mensajes.sonido') }}',
                    sonido: {{ auth()->user()->sonido_mensajes ? 'true' : 'false' }},
                    sinResponder: {{ $mensajesNoLeidos }}
                })"
                x-init="iniciar()"
            @endif
            class="min-h-screen"
        >
            @include('layouts.navigation')

            {{--
                Cabecera de la página.

                Antes era una banda blanca con `shadow` debajo de la barra de
                navegación: dos superficies blancas apiladas con una sombra en el
                medio, que es lo que producía la sensación de "doble techo".
                Ahora la cabecera es la continuación de la barra (mismo fondo, sin
                sombra, separada por un filete) y comparte con ella el mismo
                ancho y el mismo padding lateral.
            --}}
            @isset($header)
                <header class="md-page-header">
                    <div class="md-page py-5">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                {{ $slot }}
            </main>

            @unless ($esChat)
                @include('layouts.aviso-mensajes')
            @endunless
        </div>
    </body>
</html>