<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Auditoría del sistema
        </h2>
    </x-slot>

    <div class="md-page md-page-body space-y-5">
        <x-alerta />

        {{-- Filtros --}}
        <section class="md-card p-5 sm:p-6">
            <form method="GET" action="{{ route('auditoria.index') }}">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5">
                    <div>
                        <label for="fecha_desde" class="md-label">
                            Fecha desde
                        </label>

                        <input
                            type="date"
                            name="fecha_desde"
                            id="fecha_desde"
                            value="{{ $fechaDesde }}"
                            class="md-field mt-1"
                        >
                    </div>

                    <div>
                        <label for="fecha_hasta" class="md-label">
                            Fecha hasta
                        </label>

                        <input
                            type="date"
                            name="fecha_hasta"
                            id="fecha_hasta"
                            value="{{ $fechaHasta }}"
                            class="md-field mt-1"
                        >
                    </div>

                    <div>
                        <label for="usuario_id" class="md-label">
                            Usuario
                        </label>

                        <select
                            name="usuario_id"
                            id="usuario_id"
                            class="md-field mt-1"
                        >
                            <option value="">
                                Todos los usuarios
                            </option>

                            @foreach ($usuarios as $usuario)
                                <option
                                    value="{{ $usuario->id }}"
                                    @selected($usuarioId == $usuario->id)
                                >
                                    {{ $usuario->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="modulo" class="md-label">
                            Módulo
                        </label>

                        <select
                            name="modulo"
                            id="modulo"
                            class="md-field mt-1"
                        >
                            <option value="">
                                Todos los módulos
                            </option>

                            @foreach ($modulos as $item)
                                <option
                                    value="{{ $item }}"
                                    @selected($modulo === $item)
                                >
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="accion" class="md-label">
                            Acción
                        </label>

                        <select
                            name="accion"
                            id="accion"
                            class="md-field mt-1"
                        >
                            <option value="">
                                Todas las acciones
                            </option>

                            @foreach ($acciones as $item)
                                <option
                                    value="{{ $item }}"
                                    @selected($accion === $item)
                                >
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-5 flex items-center gap-3">
                    <button type="submit" class="md-btn md-btn-filled">
                        Buscar
                    </button>

                    <a href="{{ route('auditoria.index') }}" class="md-btn md-btn-text">
                        Limpiar filtros
                    </a>
                </div>
            </form>
        </section>

        {{-- Resultados --}}
        <section class="md-card">
            <div class="flex items-center justify-between px-5 pb-4 pt-5 sm:px-6 sm:pt-6">
                <h3 class="md-section-title">
                    Registros encontrados:
                    <span class="font-normal text-gray-500 dark:text-gray-400">{{ $auditorias->count() }}</span>
                </h3>
            </div>

            @if ($auditorias->isEmpty())
                <x-estado-vacio :descripcion="'No se encontraron registros de auditoría.'" />
            @else
                <x-tabla :alto="true">
                    <thead>
                        <tr class="md-tr">
                            <th scope="col" class="md-th md-th-num">Fecha</th>
                            <th scope="col" class="md-th">Usuario</th>
                            <th scope="col" class="md-th">Módulo</th>
                            <th scope="col" class="md-th">Acción</th>
                            <th scope="col" class="md-th">Modelo</th>
                            <th scope="col" class="md-th md-th-num">ID</th>
                            <th scope="col" class="md-th">Descripción</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($auditorias as $auditoria)
                            <tr class="md-tr">
                                <td class="md-td md-td-num">
                                    {{ $auditoria->created_at->format('d/m/Y H:i:s') }}
                                </td>

                                <td class="md-td md-td-strong">
                                    {{ $auditoria->usuario?->name ?? 'Sistema' }}
                                </td>

                                <td class="md-td">
                                    {{ $auditoria->modulo }}
                                </td>

                                <td class="md-td">{{ $auditoria->accion }}</td>

                                <td class="md-td">{{ $auditoria->modelo ?? '-' }}</td>

                                <td class="md-td md-td-num">{{ $auditoria->modelo_id ?? '-' }}</td>

                                <td class="md-td">
                                    @if ($auditoria->descripcion)
                                        <span class="md-td-clip" title="{{ $auditoria->descripcion }}">{{ $auditoria->descripcion }}</span>
                                    @else
                                        -
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