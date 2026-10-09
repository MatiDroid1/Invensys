<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Invensys') }}</title>

    <meta name="description"
        content="Invensys: sistema de control de inventario con kardex, reportes y trazabilidad de movimientos.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-900">

    {{-- Encabezado --}}
    <header class="md-appbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    <span class="font-semibold text-lg">Invensys</span>
                </a>

                <div class="flex items-center gap-2">
                    @auth
                        <a
                            href="{{ route('dashboard') }}"
                            class="md-btn md-btn-sm md-btn-text"
                        >
                            Ir al panel
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="md-btn md-btn-sm md-btn-text"
                        >
                            Iniciar sesión
                        </a>

                        <!-- @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-md hover:bg-gray-700"
                            >
                                Crear cuenta
                            </a>
                        @endif -->
                    @endauth
                </div>
            </div>
        </div>
    </header>

    {{-- Portada --}}
    <main class="relative overflow-hidden">
        <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-[30rem] bg-gradient-to-br from-indigo-100/80 via-sky-50/50 to-transparent dark:from-indigo-500/10 dark:via-sky-500/5 dark:to-transparent"></div>

        <section class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
            <div class="max-w-3xl">
                <span
                    class="md-badge md-badge-info"
                >
                    Control de inventario
                </span>

                <h1 class="mt-6 text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight">
                    Inventario ordenado, decisiones al día
                </h1>

                <p class="mt-6 text-lg text-gray-600">
                    Invensys centraliza el stock de tus artículos en un solo lugar: registra entradas,
                    salidas y ajustes, consulta el Kardex de cada producto y genera reportes con la
                    trazabilidad de cada operación.
                </p>

                <div class="mt-10 flex flex-col sm:flex-row gap-3">
                    @auth
                        <a
                            href="{{ route('dashboard') }}"
                            class="md-btn md-btn-lg md-btn-filled"
                        >
                            Abrir el panel
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="md-btn md-btn-lg md-btn-filled"
                        >
                            Iniciar sesión
                        </a>
                    @endauth

                    <a
                        href="#modulos"
                        class="md-btn md-btn-lg md-btn-outlined"
                    >
                        Conocer el sistema
                    </a>
                </div>
            </div>
        </section>

        {{-- Módulos --}}
        <section id="modulos" class="border-y border-gray-100 bg-white py-16 dark:border-gray-700/70 dark:bg-gray-800 sm:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight">
                    Módulos
                </h2>

                <p class="mt-4 max-w-2xl text-gray-600">
                    Todo el ciclo de vida del inventario, desde el alta de un artículo hasta su
                    kardex histórico.
                </p>

                <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                    @php
                        $modulos = [
                            [
                                'titulo' => 'Artículos y catálogo',
                                'descripcion' => 'Alta de artículos con código, categoría, unidad de medida y stock mínimo. Filtros por texto, categoría y estado.',
                                'icono' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z',
                            ],
                            [
                                'titulo' => 'Movimientos',
                                'descripcion' => 'Entradas, salidas con persona receptora y ajustes de inventario, siempre validados contra el stock disponible.',
                                'icono' => 'M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5',
                            ],
                            [
                                'titulo' => 'Kardex',
                                'descripcion' => 'Historial completo por artículo con saldo acumulado: entradas, salidas y saldo en cada movimiento.',
                                'icono' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z',
                            ],
                            [
                                'titulo' => 'Personas',
                                'descripcion' => 'Registro de quienes reciben equipos o insumos, con área, cargo e identificador para los reportes de entrega.',
                                'icono' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
                            ],
                            [
                                'titulo' => 'Reportes',
                                'descripcion' => 'Stock actual, movimientos por período y entregas por persona, con filtros combinables.',
                                'icono' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
                            ],
                            [
                                'titulo' => 'Auditoría',
                                'descripcion' => 'Cada alta, edición y movimiento queda registrado con usuario, fecha y detalle del cambio.',
                                'icono' => 'M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.746 3.746 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.746 3.746 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z',
                            ],
                        ];
                    @endphp

                    @foreach ($modulos as $modulo)
                        <div
                            class="md-card-plain p-6 transition-colors hover:border-indigo-200 dark:hover:border-indigo-500/40"
                        >
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-600 text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5"
                                    viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="{{ $modulo['icono'] }}" />
                                </svg>
                            </div>

                            <h3 class="md-section-title mt-5">
                                {{ $modulo['titulo'] }}
                            </h3>

                            <p class="mt-2 text-sm text-gray-600">
                                {{ $modulo['descripcion'] }}
                            </p>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>
    </main>

    {{-- Pie --}}
    <footer class="border-t border-gray-100 bg-white dark:border-gray-700/70 dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <p class="text-center text-sm text-gray-500">
                &copy; {{ now()->year }} {{ config('app.name', 'Invensys') }}.
                Sistema de control de inventario.
            </p>
        </div>
    </footer>

</body>

</html>
