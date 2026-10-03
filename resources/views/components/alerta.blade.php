{{--
    Avisos de resultado de una acción.

    Antes cada vista repetía su propio `div` verde o su lista de errores en
    rojo, cada uno con su propio padding y su propio tono. Aquí se unifican
    el estilo y, sobre todo, se evita que el mensaje desaparezca a mitad
    de una recarga: los errores de validación se muestran aunque no haya sesión.
--}}

@php
    $exito = session('success') ?? session('status') ?? null;
    $fallo = session('error') ?? null;
    $errores = $errors->all();
@endphp

@if ($exito || $fallo || $errores)
    <div {{ $attributes->merge(['class' => 'space-y-3']) }}>
        @if ($exito)
            <div class="flex items-start gap-3 rounded-2xl bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="mt-0.5 h-5 w-5 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>

                <span class="min-w-0 break-words">{{ $exito }}</span>
            </div>
        @endif

        @if ($fallo)
            <div class="flex items-start gap-3 rounded-2xl bg-red-50 px-4 py-3 text-sm text-red-800 dark:bg-red-500/10 dark:text-red-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="mt-0.5 h-5 w-5 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>

                <span class="min-w-0 break-words">{{ $fallo }}</span>
            </div>
        @endif

        @if ($errores)
            <div class="flex items-start gap-3 rounded-2xl bg-red-50 px-4 py-3 text-sm text-red-800 dark:bg-red-500/10 dark:text-red-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="mt-0.5 h-5 w-5 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>

                <div class="min-w-0">
                    @if (count($errores) === 1)
                        <span class="break-words">{{ $errores[0] }}</span>
                    @else
                        <p class="font-medium">No se pudo guardar. Revisa estos {{ count($errores) }} campos:</p>

                        <ul class="mt-1 list-inside list-disc space-y-0.5">
                            @foreach ($errores as $error)
                                <li class="break-words">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endif