<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="md-title">Mensajes</h2>

                <p class="md-subtitle mt-0.5">Conversaciones con el resto de usuarios.</p>
            </div>

            <a href="{{ route('mensajes.create') }}" class="md-btn md-btn-md md-btn-filled">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>

                Nuevo mensaje
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
            <x-alerta />

            {{--
                Lo primero que hay que resolver es lo pendiente, así que el bloque
                va arriba del listado y con el borde rojo que ya usaba: es la
                única zona de la pantalla que exige una acción.
            --}}
            @if ($sinResponder->isNotEmpty())
                <section class="md-card overflow-hidden border-l-4 border-red-500">
                    <div class="md-section flex flex-wrap items-center justify-between gap-3">
                        <h3 class="md-section-title text-red-600 dark:text-red-400">Sin responder</h3>

                        <span class="md-badge md-badge-danger">
                            {{ $sinResponder->count() }}
                            {{ $sinResponder->count() === 1 ? 'conversación' : 'conversaciones' }}
                        </span>
                    </div>

                    <ul class="md-divider">
                        @foreach ($sinResponder as $conversacion)
                            @php
                                $otro = $conversacion->interlocutor(auth()->user());
                                $ultimo = $conversacion->ultimoMensaje;
                            @endphp

                            <li class="md-divider last:border-b-0">
                                <a
                                    href="{{ route('mensajes.show', $conversacion) }}"
                                    class="flex items-center justify-between gap-4 px-5 py-3 transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/25"
                                >
                                    <div class="min-w-0">
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            {{ $otro->name }}
                                        </div>

                                        <div class="truncate text-sm text-gray-500 dark:text-gray-400">
                                            {{ $ultimo?->cuerpo ?? 'Conversación sin mensajes' }}
                                        </div>
                                    </div>

                                    <div class="flex shrink-0 items-center gap-3">
                                        <span class="whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                            {{ $ultimo?->created_at->format('d/m/Y H:i') }}
                                        </span>

                                        @if ($conversacion->mensajes_no_leidos > 0)
                                            <span class="inline-flex h-6 min-w-6 items-center justify-center rounded-full bg-red-600 px-2 text-xs font-bold text-white">
                                                {{ $conversacion->mensajes_no_leidos }}
                                            </span>
                                        @endif
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            <section class="md-card overflow-hidden">
                @if ($conversaciones->isEmpty())
                    <x-estado-vacio
                        titulo="Sin conversaciones"
                        descripcion="No tienes conversaciones todavía."
                    >
                        <x-slot:accion>
                            <a href="{{ route('mensajes.create') }}" class="md-btn md-btn-md md-btn-filled">
                                Escribir el primer mensaje
                            </a>
                        </x-slot:accion>
                    </x-estado-vacio>
                @else
                    <div class="overflow-x-auto">
                        <table class="md-table">
                            <thead>
                                <tr>
                                    <th scope="col">Con</th>
                                    <th scope="col">Último mensaje</th>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Estado</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($conversaciones as $conversacion)
                                    @php
                                        $otro = $conversacion->interlocutor(auth()->user());
                                        $ultimo = $conversacion->ultimoMensaje;
                                    @endphp

                                    <tr>
                                        <td class="whitespace-nowrap font-medium text-gray-900 dark:text-white">
                                            <a
                                                href="{{ route('mensajes.show', $conversacion) }}"
                                                class="text-indigo-600 hover:underline dark:text-indigo-300"
                                            >
                                                {{ $otro->name }}
                                            </a>
                                        </td>

                                        <td class="max-w-md">
                                            @if ($ultimo)
                                                <span class="line-clamp-2" title="{{ $ultimo->cuerpo }}">
                                                    @if ($ultimo->fueEnviadoPor(auth()->id()))
                                                        <span class="text-gray-500 dark:text-gray-400">Tú: </span>
                                                    @endif

                                                    {{ $ultimo->cuerpo }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500">Conversación sin mensajes</span>
                                            @endif
                                        </td>

                                        <td class="whitespace-nowrap tabular-nums text-gray-500 dark:text-gray-400">
                                            {{ $ultimo?->created_at->format('d/m/Y H:i') ?? '—' }}
                                        </td>

                                        <td class="whitespace-nowrap">
                                            @if ($conversacion->mensajes_no_leidos > 0)
                                                <span class="md-badge md-badge-danger">
                                                    {{ $conversacion->mensajes_no_leidos }} sin leer
                                                </span>
                                            @else
                                                <span class="md-badge md-badge-success">Al día</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>