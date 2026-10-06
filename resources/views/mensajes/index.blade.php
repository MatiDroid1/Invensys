<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="md-title">
                Mensajes
            </h2>

            <a
                href="{{ route('mensajes.create') }}"
                class="md-btn md-btn-filled"
            >
                Nuevo mensaje
            </a>
        </div>
    </x-slot>

    <div class="md-page md-page-body space-y-6">
        <x-alerta />

        @if ($sinResponder->isNotEmpty())

            <section class="md-card border-l-4 border-red-500 p-5 sm:p-6">
                <div class="flex flex-wrap items-center gap-3">
                    <h3 class="md-section-title">
                        Sin responder
                    </h3>

                    <span class="md-badge md-badge-danger">
                        {{ $sinResponder->count() }}
                        {{ $sinResponder->count() === 1 ? 'conversación' : 'conversaciones' }}
                    </span>
                </div>

                <ul class="mt-2">
                    @foreach ($sinResponder as $conversacion)
                        @php
                            $otro = $conversacion->interlocutor(auth()->user());
                            $ultimo = $conversacion->ultimoMensaje;
                        @endphp

                        <li class="md-divider first:border-t-0 py-3">
                            <a
                                href="{{ route('mensajes.show', $conversacion) }}"
                                class="flex items-center justify-between gap-4 rounded-md px-2 py-1 hover:bg-gray-50 dark:hover:bg-gray-700"
                            >
                                <div class="min-w-0">
                                    <div class="md-section-title">
                                        {{ $otro->name }}
                                    </div>

                                    <div class="md-subtitle truncate">
                                        {{ $ultimo?->cuerpo ?? 'Conversación sin mensajes' }}
                                    </div>
                                </div>

                                <div class="flex shrink-0 items-center gap-3">
                                    <span class="md-subtitle whitespace-nowrap">
                                        {{ $ultimo?->created_at->format('d/m/Y H:i') }}
                                    </span>

                                    <span class="md-badge md-badge-danger">
                                        {{ $conversacion->mensajes_no_leidos }}
                                    </span>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>

        @endif

        <section class="md-card">
            @if ($conversaciones->isEmpty())

                <x-estado-vacio :descripcion="'No tienes conversaciones todavía.'" />

            @else

                <x-tabla>
                    <thead>
                        <tr class="md-tr">
                            <th scope="col" class="md-th">
                                Con
                            </th>

                            <th scope="col" class="md-th">
                                Último mensaje
                            </th>

                            <th scope="col" class="md-th md-th-num">
                                Fecha
                            </th>

                            <th scope="col" class="md-th">
                                Estado
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($conversaciones as $conversacion)
                            @php
                                $otro = $conversacion->interlocutor(auth()->user());
                                $ultimo = $conversacion->ultimoMensaje;
                            @endphp

                            <tr class="md-tr">
                                <td class="md-td md-td-strong whitespace-nowrap">
                                    <a
                                        href="{{ route('mensajes.show', $conversacion) }}"
                                        class="md-td-strong hover:text-indigo-600 dark:hover:text-indigo-400"
                                    >
                                        {{ $otro->name }}
                                    </a>
                                </td>

                                <td class="md-td">
                                    @if ($ultimo)
                                        <span class="md-td-clip" title="{{ $ultimo->cuerpo }}">
                                            @if ($ultimo->fueEnviadoPor(auth()->id()))
                                                <span class="text-gray-500 dark:text-gray-400">Tú: </span>
                                            @endif
                                            {{ $ultimo->cuerpo }}
                                        </span>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">
                                            Conversación sin mensajes
                                        </span>
                                    @endif
                                </td>

                                <td class="md-td md-td-num">
                                    {{ $ultimo?->created_at->format('d/m/Y H:i') ?? '—' }}
                                </td>

                                <td class="md-td">
                                    @if ($conversacion->mensajes_no_leidos > 0)
                                        <span class="md-badge md-badge-danger">
                                            {{ $conversacion->mensajes_no_leidos }} sin leer
                                        </span>
                                    @else
                                        <span class="md-badge md-badge-success">
                                            Al día
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-tabla>

            @endif
        </section>
    </div>
</x-app-layout>