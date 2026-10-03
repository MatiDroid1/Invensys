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

<body class="bg-gray-100 font-sans text-gray-900 antialiased dark:bg-gray-900 dark:text-gray-100">
        <div class="flex min-h-screen flex-col items-center justify-center px-4 pt-6 sm:pt-0">
            <div class="flex flex-col items-center gap-2">
                <a href="{{ route('dashboard') }}" class="md-icon-btn h-20 w-20 text-gray-400 dark:text-gray-500">
                    <x-application-logo class="h-14 w-14 fill-current" />
                </a>

                <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                    {{ config('app.name', 'Invensys') }}
                </span>
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
    </body>

</html>
