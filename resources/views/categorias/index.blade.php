<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="md-title">
                Categorías
            </h2>

            <a href="{{ route('categorias.create') }}" class="md-btn md-btn-filled">
                Nueva categoría
            </a>
        </div>
    </x-slot>

    <div class="md-page md-page-body">
        <x-alerta />

        <section class="md-card">
            @if ($categorias->isEmpty())
                <x-estado-vacio :descripcion="'No hay categorías registradas.'" />
            @else
                <x-tabla>
                    <thead>
                        <tr class="md-tr">
                            <th scope="col" class="md-th">Nombre</th>
                            <th scope="col" class="md-th">Descripción</th>
                            <th scope="col" class="md-th">Estado</th>
                            <th scope="col" class="md-th">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($categorias as $categoria)
                            <tr class="md-tr">
                                <td class="md-td md-td-strong">{{ $categoria->nombre }}</td>

                                <td class="md-td">
                                    @if ($categoria->descripcion)
                                        <span class="md-td-clip" title="{{ $categoria->descripcion }}">{{ $categoria->descripcion }}</span>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="md-td">
                                    @if ($categoria->activo)
                                        <span class="md-badge md-badge-success">Activo</span>
                                    @else
                                        <span class="md-badge">Inactivo</span>
                                    @endif
                                </td>

                                <td class="md-td">
                                    <a
                                        href="{{ route('categorias.edit', $categoria) }}"
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