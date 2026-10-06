<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="md-title">
                Usuarios
            </h2>

            <a
                href="{{ route('usuarios.create') }}"
                class="md-btn md-btn-filled"
            >
                Nuevo usuario
            </a>
        </div>
    </x-slot>

    <div class="md-page md-page-body space-y-6">
        <x-alerta />

        <section class="md-card">
            @if ($usuarios->isEmpty())
                <x-estado-vacio :descripcion="'No hay usuarios registrados.'" />
            @else
                <x-tabla>
                    <thead>
                        <tr class="md-tr">
                            <th scope="col" class="md-th">
                                Nombre
                            </th>

                            <th scope="col" class="md-th">
                                Correo
                            </th>

                            <th scope="col" class="md-th">
                                Rol
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
                        @foreach ($usuarios as $usuario)
                            <tr class="md-tr">
                                <td class="md-td md-td-strong">
                                    {{ $usuario->name }}
                                </td>

                                <td class="md-td">
                                    {{ $usuario->email }}
                                </td>

                                <td class="md-td">
                                    <span class="md-badge">
                                        {{ $usuario->rol === 'admin' ? 'Administrador' : 'Usuario' }}
                                    </span>
                                </td>

                                <td class="md-td">
                                    <span @class([
                                        'md-badge',
                                        'md-badge-success' => $usuario->activo,
                                    ])>
                                        {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>

                                <td class="md-td">
                                    <a
                                        href="{{ route('usuarios.edit', $usuario) }}"
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