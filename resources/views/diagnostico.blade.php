<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="md-title">
                Diagnóstico del sistema
            </h2>

            <a href="{{ route('dashboard') }}" class="md-btn md-btn-text md-btn-sm">
                Volver al panel
            </a>
        </div>
    </x-slot>

    <div class="md-page md-page-body space-y-6">
        <section class="md-card p-5 sm:p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Comprobaciones de infraestructura: base de datos, permisos de
                escritura, assets compilados y configuración. Si algo falla,
                aquí aparece con el detalle y la acción sugerida.
            </p>
        </section>

        <section class="grid grid-cols-1 gap-4 md:grid-cols-2">
            @foreach ($chequeos as $chequeo)
                @php
                    $estilos = [
                        'ok' => 'border-emerald-200 bg-emerald-50/60 dark:border-emerald-800/60 dark:bg-emerald-500/10',
                        'advertencia' => 'border-amber-200 bg-amber-50/60 dark:border-amber-800/60 dark:bg-amber-500/10',
                        'error' => 'border-red-200 bg-red-50/60 dark:border-red-800/60 dark:bg-red-500/10',
                    ];

                    $puntos = [
                        'ok' => 'bg-emerald-500',
                        'advertencia' => 'bg-amber-500',
                        'error' => 'bg-red-500',
                    ];

                    $etiquetas = [
                        'ok' => 'Correcto',
                        'advertencia' => 'Revisar',
                        'error' => 'Error',
                    ];
                @endphp

                <article class="rounded-2xl border p-4 {{ $estilos[$chequeo['estado']] }}">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full {{ $puntos[$chequeo['estado']] }}"></span>

                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ $chequeo['nombre'] }}
                            </h3>
                        </div>

                        <span class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            {{ $etiquetas[$chequeo['estado']] }}
                        </span>
                    </div>

                    <p class="mt-2 break-words text-sm text-gray-600 dark:text-gray-300">
                        {{ $chequeo['detalle'] }}
                    </p>
                </article>
            @endforeach
        </section>

        <section class="md-card p-5 sm:p-6">
            <h3 class="md-section-title">Comandos útiles</h3>

            <ul class="mt-3 space-y-1.5 font-mono text-xs text-gray-600 dark:text-gray-300">
                <li>php artisan migrate --force</li>
                <li>php artisan config:clear && php artisan view:clear</li>
                <li>npm install && npm run build</li>
                <li>php artisan storage:link</li>
            </ul>
        </section>
    </div>
</x-app-layout>
