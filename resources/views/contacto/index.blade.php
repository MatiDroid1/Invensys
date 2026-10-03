<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="md-title">Bandeja de contacto</h2>

                <p class="md-subtitle mt-0.5">Consultas y solicitudes enviadas por los usuarios.</p>
            </div>

            <a href="{{ route('contacto.create') }}" class="md-btn md-btn-md md-btn-filled">
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

            <section class="md-card overflow-hidden">
                @if ($contactos->isEmpty())
                    <x-estado-vacio
                        titulo="Bandeja vacía"
                        descripcion="No hay mensajes de contacto registrados."
                    >
                        <x-slot:accion>
                            <a href="{{ route('contacto.create') }}" class="md-btn md-btn-md md-btn-filled">
                                Escribir el primero
                            </a>
                        </x-slot:accion>
                    </x-estado-vacio>
                @else
                    {{--
                        El mensaje se recorta a tres líneas con `line-clamp-3`: la
                        bandeja es para leer de un vistazo, no para leer todo. El
                        texto íntegro sigue disponible en el atributo `title`, que
                        aparece al dejar el cursor encima.
                    --}}
                    <div class="overflow-x-auto">
                        <table class="md-table">
                            <thead>
                                <tr>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Correo</th>
                                    <th scope="col">Asunto</th>
                                    <th scope="col">Mensaje</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col" class="text-right">Acción</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($contactos as $contacto)
                                    <tr>
                                        <td class="whitespace-nowrap tabular-nums text-gray-500 dark:text-gray-400">
                                            {{ $contacto->created_at->format('d/m/Y H:i') }}
                                        </td>

                                        <td class="font-medium text-gray-900 dark:text-white">
                                            {{ $contacto->nombre }}
                                        </td>

                                        <td>
                                            <a href="mailto:{{ $contacto->email }}" class="text-indigo-600 hover:underline dark:text-indigo-300">
                                                {{ $contacto->email }}
                                            </a>
                                        </td>

                                        <td>{{ $contacto->asunto }}</td>

                                        <td class="max-w-md">
                                            <span class="line-clamp-3" title="{{ $contacto->mensaje }}">
                                                {{ $contacto->mensaje }}
                                            </span>
                                        </td>

                                        <td>
                                            <span @class([
                                                'md-badge',
                                                'md-badge-danger' => $contacto->estado === 'PENDIENTE',
                                                'md-badge-success' => $contacto->estado !== 'PENDIENTE',
                                            ])>
                                                {{ $contacto->estado === 'PENDIENTE' ? 'Pendiente' : 'Atendido' }}
                                            </span>
                                        </td>

                                        <td class="text-right">
                                            @if ($contacto->estado === 'PENDIENTE')
                                                <form
                                                    method="POST"
                                                    action="{{ route('contacto.atender', $contacto) }}"
                                                >
                                                    @csrf
                                                    @method('PATCH')

                                                    <button
                                                        type="submit"
                                                        class="md-btn md-btn-sm md-btn-tonal"
                                                        title="Marcar {{ $contacto->nombre }} como atendido"
                                                    >
                                                        Marcar atendido
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500">—</span>
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