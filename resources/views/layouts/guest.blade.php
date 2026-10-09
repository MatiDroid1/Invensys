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
    <div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden bg-gradient-to-b from-slate-50 via-gray-100 to-indigo-100/50 px-4 py-12 dark:from-gray-950 dark:via-gray-900 dark:to-indigo-950/40 sm:py-16">

        <div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-indigo-200/50 blur-3xl dark:bg-indigo-500/10"></div>
            <div class="absolute -bottom-24 -left-24 h-72 w-72 rounded-full bg-sky-200/50 blur-3xl dark:bg-sky-500/10"></div>
        </div>

        <a href="{{ route('dashboard') }}" class="relative flex flex-col items-center gap-2">
            <x-application-logo class="h-20 w-20 fill-current text-indigo-600 dark:text-indigo-400" />

            <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                {{ config('app.name', 'Invensys') }}
            </span>
        </a>

        <div class="relative mt-6 w-full sm:max-w-md">
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
