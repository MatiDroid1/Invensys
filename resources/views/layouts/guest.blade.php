<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title.' · '.config('app.name', 'Invensys') : config('app.name', 'Invensys') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tema -->
    @include('layouts.tema')

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <div class="px-4">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-2">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />

                <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                    {{ config('app.name', 'Invensys') }}
                </span>
            </a>
        </div>

        <div class="w-full px-4 sm:max-w-md">
            <div class="md-card p-6 sm:p-8">
                @isset($title)
                    <h1 class="md-title mb-5">
                        {{ $title }}
                    </h1>
                @endisset

                {{ $slot }}
            </div>
        </div>
    </div>
</body>

</html>
