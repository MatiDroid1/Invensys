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
    <body class="font-sans antialiased">
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
            class="min-h-screen bg-gray-100 dark:bg-gray-900"
        >
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow relative z-[90]">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            @unless ($esChat)
                @include('layouts.aviso-mensajes')
            @endunless
        </div>
    </body>
</html>