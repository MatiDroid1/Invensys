<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="md-title">
                Unidades de medida
            </h2>

            <a href="{{ route('unidades-medida.create') }}" class="md-btn md-btn-filled">
                Nueva unidad
            </a>
        </div>
    </x-slot>

    <div class="md-page md-page-body">
        <x-alerta />

        <section class="md-card">
            @if ($unidadesMedida->isEmpty())
                <x-estado-vacio :descripcion="'No hay unidades de medida registradas.'" />
            @else
                <x-tabla>
                    <thead>
                        <tr class="md-tr">
                            <th scope="col" class="md-th">Nombre</th>
                            <th scope="col" class="md-th">Abreviatura</th>
                            <th scope="col" class="md-th">Estado</th>
                            <th scope="col" class="md-th">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($unidadesMedida as $unidad)
                            <tr class="md-tr">
                                <td class="md-td md-td-strong">{{ $unidad->nombre }}</td>

                                <td class="md-td md-td-strong">{{ $unidad->abreviatura ?? '-' }}</td>

                                <td class="md-td">
                                    @if ($unidad->activo)
                                        <span class="md-badge md-badge-success">Activo</span>
                                    @else
                                        <span class="md-badge">Inactivo</span>
                                    @endif
                                </td>

                                <td class="md-td">
                                    <a
                                        href="{{ route('unidades-medida.edit', $unidad) }}"
                                        class="md-btn md-btn-sm md-btn-text"
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