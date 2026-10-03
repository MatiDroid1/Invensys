<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="md-title">Personas</h2>

                <p class="md-subtitle mt-0.5">Destinatarios de las salidas de inventario.</p>
            </div>

            <a href="{{ route('personas.create') }}" class="md-btn md-btn-md md-btn-filled">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>

                Nueva persona
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
            <x-alerta />

            <section class="md-card overflow-hidden">
                @if ($personas->isEmpty())
                    <x-estado-vacio
                        titulo="Sin personas"
                        descripcion="No hay personas registradas."
                    >
                        <x-slot:accion>
                            <a href="{{ route('personas.create') }}" class="md-btn md-btn-md md-btn-filled">
                                Registrar la primera persona
                            </a>
                        </x-slot:accion>
                    </x-estado-vacio>
                @else
                    <div class="overflow-x-auto">
                        <table class="md-table">
                            <thead>
                                <tr>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Identificador</th>
                                    <th scope="col">Correo</th>
                                    <th scope="col">Área</th>
                                    <th scope="col">Cargo</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col" class="text-right">Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($personas as $persona)
                                    <tr>
                                        <td class="font-medium text-gray-900 dark:text-white">
                                            {{ $persona->nombre_completo }}
                                        </td>

                                        <td class="font-mono text-xs">{{ $persona->identificador ?? '—' }}</td>

                                        <td>
                                            @if ($persona->email)
                                                <a href="mailto:{{ $persona->email }}" class="text-indigo-600 hover:underline dark:text-indigo-300">
                                                    {{ $persona->email }}
                                                </a>
                                            @else
                                                —
                                            @endif
                                        </td>

                                        <td>{{ $persona->area ?? '—' }}</td>
                                        <td>{{ $persona->cargo ?? '—' }}</td>

                                        <td>
                                            <span @class([
                                                'md-badge',
                                                'md-badge-success' => $persona->activo,
                                                'md-badge-warning' => ! $persona->activo,
                                            ])>
                                                {{ $persona->activo ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>

                                        <td class="text-right">
                                            <a
                                                href="{{ route('personas.edit', $persona) }}"
                                                class="md-icon-btn"
                                                title="Editar {{ $persona->nombre_completo }}"
                                                aria-label="Editar {{ $persona->nombre_completo }}"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                                                </svg>
                                            </a>
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