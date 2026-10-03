<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="md-title">
                Personas
            </h2>

            <a
                href="{{ route('personas.create') }}"
                class="md-btn md-btn-filled"
            >
                Nueva persona
            </a>
        </div>
    </x-slot>

    <div class="md-page md-page-body space-y-6">
        <x-alerta />

        <section class="md-card">
            @if ($personas->isEmpty())
                <x-estado-vacio :descripcion="'No hay personas registradas.'" />
            @else
                <x-tabla>
                    <thead>
                        <tr class="md-tr">
                            <th scope="col" class="md-th">
                                Nombre
                            </th>

                            <th scope="col" class="md-th">
                                Identificador
                            </th>

                            <th scope="col" class="md-th">
                                Correo
                            </th>

                            <th scope="col" class="md-th">
                                Área
                            </th>

                            <th scope="col" class="md-th">
                                Cargo
                            </th>

                            <th scope="col" class="md-th">
                                Estado
                            </th>

                            <th scope="col" class="md-th">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($personas as $persona)
                            <tr class="md-tr">
                                <td class="md-td md-td-strong">
                                    {{ $persona->nombre_completo }}
                                </td>

                                <td class="md-td">
                                    {{ $persona->identificador ?? '-' }}
                                </td>

                                <td class="md-td">
                                    {{ $persona->email ?? '-' }}
                                </td>

                                <td class="md-td">
                                    {{ $persona->area ?? '-' }}
                                </td>

                                <td class="md-td">
                                    {{ $persona->cargo ?? '-' }}
                                </td>

                                <td class="md-td">
                                    <span @class([
                                        'md-badge',
                                        'md-badge-success' => $persona->activo,
                                    ])>
                                        {{ $persona->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>

                                <td class="md-td">
                                    <a
                                        href="{{ route('personas.edit', $persona) }}"
                                        class="md-btn md-btn-text md-btn-sm"
                                    >
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-tabla>
            @endif
        </section>
    </div>
</x-app-layout>