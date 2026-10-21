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

    <div
        class="md-page md-page-body space-y-6"
        x-data="{ nombre: '', accion: '' }"
        x-on:confirmar-desactivacion.window="nombre = $event.detail.nombre; accion = $event.detail.accion"
    >
        {{-- Un solo modal para toda la tabla: se rellena con la persona elegida
             desde la fila, en lugar de imprimir un confirm() del navegador por
             cada fila. --}}
        <x-modal name="desactivar-persona" maxWidth="md">
            <form method="POST" x-bind:action="accion">
                @csrf
                @method('DELETE')

                <div class="p-6">
                    <h2 class="md-section-title">Desactivar persona</h2>

                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        <span class="font-medium text-gray-900 dark:text-gray-100" x-text="nombre"></span>
                        dejará de aparecer en los formularios de movimientos. Su historial no se elimina.
                    </p>
                </div>

                <div class="flex flex-col-reverse gap-3 bg-gray-50 px-6 py-4 dark:bg-gray-800/60 sm:flex-row sm:justify-end">
                    <button type="button" class="md-btn md-btn-text" x-on:click="$dispatch('close')">
                        Cancelar
                    </button>

                    <button type="submit" class="md-btn md-btn-danger">
                        Desactivar
                    </button>
                </div>
            </form>
        </x-modal>

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
                                    <div class="flex items-center gap-1">
                                        <a
                                            href="{{ route('personas.edit', $persona) }}"
                                            class="md-btn md-btn-text md-btn-sm"
                                        >
                                            Editar
                                        </a>

                                        @if ($persona->activo)
                                            <button
                                                type="button"
                                                class="md-btn md-btn-text md-btn-sm !text-red-600 dark:!text-red-300"
                                                @click="$dispatch('confirmar-desactivacion', {
                                                    nombre: @js($persona->nombre_completo),
                                                    accion: @js(route('personas.destroy', $persona))
                                                }); $dispatch('open-modal', 'desactivar-persona')"
                                            >
                                                Desactivar
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-tabla>
            @endif
        </section>
    </div>
</x-app-layout>