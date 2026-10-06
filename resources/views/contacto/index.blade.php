<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="md-title">
                Bandeja de contacto
            </h2>

            <a href="{{ route('contacto.create') }}" class="md-btn md-btn-filled">
                Nuevo mensaje
            </a>
        </div>
    </x-slot>

    <div class="md-page md-page-body">
        <x-alerta />

        <section class="md-card">
            @if ($contactos->isEmpty())
                <x-estado-vacio :descripcion="'No hay mensajes de contacto registrados.'" />
            @else
                <x-tabla>
                    <thead>
                        <tr class="md-tr">
                            <th scope="col" class="md-th md-th-num">Fecha</th>
                            <th scope="col" class="md-th">Nombre</th>
                            <th scope="col" class="md-th">Email</th>
                            <th scope="col" class="md-th">Asunto</th>
                            <th scope="col" class="md-th">Mensaje</th>
                            <th scope="col" class="md-th">Estado</th>
                            <th scope="col" class="md-th">Acción</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($contactos as $contacto)
                            <tr class="md-tr">
                                <td class="md-td md-td-num">
                                    {{ $contacto->created_at->format('d/m/Y H:i') }}
                                </td>

                                <td class="md-td md-td-strong">{{ $contacto->nombre }}</td>

                                <td class="md-td">{{ $contacto->email }}</td>

                                <td class="md-td">{{ $contacto->asunto }}</td>

                                <td class="md-td">
                                    <span class="md-td-clip" title="{{ $contacto->mensaje }}">{{ $contacto->mensaje }}</span>
                                </td>

                                <td class="md-td">
                                    @if ($contacto->estado === 'PENDIENTE')
                                        <span class="md-badge md-badge-danger">Pendiente</span>
                                    @else
                                        <span class="md-badge md-badge-success">Atendido</span>
                                    @endif
                                </td>

                                <td class="md-td">
                                    @if ($contacto->estado === 'PENDIENTE')
                                        <form
                                            method="POST"
                                            action="{{ route('contacto.atender', $contacto) }}"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit" class="md-btn md-btn-sm md-btn-text">
                                                Marcar atendido
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
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