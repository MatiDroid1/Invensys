@props([
    'tipo' => 'success',
    'mensaje' => null,
])

@php
    /*
     * Aviso de resultado (guardado, error, etc.).
     *
     * Antes cada pantalla tenía su propio bloque de "session('success')" con el
     * `bg-green-50` y el ícono de check escritos a mano: nueve archivos, tres
     * versiones distintas del mismo rectángulo. Ahora hay uno solo y todas las
     * pantallas se ven igual.
     *
     * El fondo es un tono suave del color y no un plano saturado: en Material el
     * aviso acompaña, no compite con la tabla que acaba de aparecer.
     */
    $tonos = [
        'success' => [
            'caja' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
            'texto' => 'text-emerald-900 dark:text-emerald-100',
            'icono' => 'M4.5 12.75l6 6 9-13.5',
        ],
        'error' => [
            'caja' => 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-300',
            'texto' => 'text-red-900 dark:text-red-100',
            'icono' => 'M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z',
        ],
        'warning' => [
            'caja' => 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300',
            'texto' => 'text-amber-900 dark:text-amber-100',
            'icono' => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z',
        ],
        'info' => [
            'caja' => 'bg-sky-50 text-sky-700 dark:bg-sky-500/10 dark:text-sky-300',
            'texto' => 'text-sky-900 dark:text-sky-100',
            'icono' => 'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z',
        ],
    ];

    $tono = $tonos[$tipo] ?? $tonos['info'];

    // Si no se pasa el mensaje se toma del flash de la sesión, que es como lo
    // usan casi todas las pantallas.
    $texto = $mensaje ?? session($tipo === 'error' ? 'error' : $tipo) ?? session('success');

    $role = $tipo === 'error' ? 'alert' : 'status';
@endphp

@if ($texto)
    <div role="{{ $role }}" class="flex items-start gap-3 rounded-2xl px-4 py-3 {{ $tono['texto'] }}">
        <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $tono['caja'] }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $tono['icono'] }}" />
            </svg>
        </span>

        {{-- `break-words` evita que un mensaje largo o una palabra sin espacios
             (una URL, un código) se salga de la caja en pantallas angostas y
             acabe cortado. --}}
        <p class="min-w-0 flex-1 break-words pt-1 text-sm font-medium">
            {{ $texto }}
        </p>
    </div>
@endif