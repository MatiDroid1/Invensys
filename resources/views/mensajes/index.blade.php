<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Mensajes
            </h2>

            <a
                href="{{ route('mensajes.create') }}"
                class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700"
            >
                Nuevo mensaje
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if ($sinResponder->isNotEmpty())

                <div class="mb-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-red-500">
                    <div class="p-6">

                        <h3 class="font-semibold text-lg text-red-600 mb-4">
                            Sin responder
                            <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                                ({{ $sinResponder->count() }}
                                {{ $sinResponder->count() === 1 ? 'conversación' : 'conversaciones' }})
                            </span>
                        </h3>

                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($sinResponder as $conversacion)
                                @php
                                    $otro = $conversacion->interlocutor(auth()->user());
                                    $ultimo = $conversacion->ultimoMensaje;
                                @endphp

                                <li class="py-3">
                                    <a
                                        href="{{ route('mensajes.show', $conversacion) }}"
                                        class="flex justify-between items-center gap-4 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md px-2 py-1"
                                    >
                                        <div class="min-w-0">
                                            <div class="font-medium text-gray-900 dark:text-gray-100">
                                                {{ $otro->name }}
                                            </div>

                                            <div class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                                {{ $ultimo?->cuerpo ?? 'Conversación sin mensajes' }}
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-3 shrink-0">
                                            <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                                {{ $ultimo?->created_at->format('d/m/Y H:i') }}
                                            </span>

                                            <span class="inline-flex items-center justify-center min-w-6 h-6 px-2 rounded-full bg-red-600 text-white text-xs font-bold">
                                                {{ $conversacion->mensajes_no_leidos }}
                                            </span>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                    </div>
                </div>

            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if ($conversaciones->isEmpty())

                        <p>No tienes conversaciones todavía.</p>

                    @else

                        <div class="overflow-x-auto">
                            <table class="min-w-full">

                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-700">

                                        <th class="px-4 py-3 text-left">
                                            Con
                                        </th>

                                        <th class="px-4 py-3 text-left">
                                            Último mensaje
                                        </th>

                                        <th class="px-4 py-3 text-left">
                                            Fecha
                                        </th>

                                        <th class="px-4 py-3 text-left">
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

                                        <tr class="border-b border-gray-200 dark:border-gray-700 align-top">

                                            <td class="px-4 py-3 font-medium whitespace-nowrap">
                                                <a
                                                    href="{{ route('mensajes.show', $conversacion) }}"
                                                    class="text-blue-600 hover:underline"
                                                >
                                                    {{ $otro->name }}
                                                </a>
                                            </td>

                                            <td class="px-4 py-3 max-w-md">
                                                @if ($ultimo)
                                                    @if ($ultimo->fueEnviadoPor(auth()->id()))
                                                        <span class="text-gray-500 dark:text-gray-400">Tú: </span>
                                                    @endif
                                                    {{ $ultimo->cuerpo }}
                                                @else
                                                    <span class="text-gray-500 dark:text-gray-400">
                                                        Conversación sin mensajes
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="px-4 py-3 whitespace-nowrap">
                                                {{ $ultimo?->created_at->format('d/m/Y H:i') ?? '—' }}
                                            </td>

                                            <td class="px-4 py-3 whitespace-nowrap">
                                                @if ($conversacion->mensajes_no_leidos > 0)
                                                    <span class="text-red-600 font-semibold">
                                                        {{ $conversacion->mensajes_no_leidos }} sin leer
                                                    </span>
                                                @else
                                                    <span class="text-green-600 font-semibold">
                                                        Al día
                                                    </span>
                                                @endif
                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>
                        </div>

                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
