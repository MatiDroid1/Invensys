{{--
    Encabezado HTML compartido por el layout autenticado, el de invitados y la
    portada. Mantener una sola copia evita que el favicon, las fuentes o Vite se
    actualicen en un lugar y queden desincronizados en los demás.

    Variables opcionales: $titulo, $descripcion y $conTema (por defecto true).
--}}
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ $titulo ?? config('app.name', 'Invensys') }}</title>

@isset($descripcion)
    <meta name="description" content="{{ $descripcion }}">
@endisset

<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

@if ($conTema ?? true)
    @include('layouts.tema')
@endif

@vite(['resources/css/app.css', 'resources/js/app.js'])
