<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="md-title">Auditoría del sistema</h2>

            <p class="md-subtitle mt-0.5">Rastro de las acciones realizadas sobre los datos.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
            <x-alerta />

            {{-- Filtros --}}
            <section class="md-card p-5">
                <form method="GET" action="{{ route('auditoria.index') }}">
                    <div class="grid grid-cols-1 gap-x-5 gap-y-4 md:grid-cols-2 lg:grid-cols-5">
                        <div>
                            <label for="fecha_desde" class="md-label">Fecha desde</label>

                            <input
                                type="date"
                                name="fecha_desde"
                                id="fecha_desde"
                                value="{{ $fechaDesde }}"
                                class="md-field mt-1.5"
                            >
                        </div>

                        <div>
                            <label for="fecha_hasta" class="md-label">Fecha hasta</label>

                            <input
                                type="date"
                                name="fecha_hasta"
                                id="fecha_hasta"
                                value="{{ $fechaHasta }}"
                                class="md-field mt-1.5"
                            >
                        </div>

                        <div>
                            <label for="usuario_id" class="md-label">Usuario</label>

                            <select name="usuario_id" id="usuario_id" class="md-field mt-1.5">
                                <option value="">Todos</option>

                                @foreach ($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" @selected($usuarioId == $usuario->id)>
                                        {{ $usuario->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="modulo" class="md-label">Módulo</label>

                            <select name="modulo" id="modulo" class="md-field mt-1.5">
                                <option value="">Todos</option>

                                @foreach ($modulos as $item)
                                    <option value="{{ $item }}" @selected($modulo === $item)>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="accion" class="md-label">Acción</label>

                            <select name="accion" id="accion" class="md-field mt-1.5">
                                <option value="">Todas</option>

                                @foreach ($acciones as $item)
                                    <option value="{{ $item }}" @selected($accion === $item)>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-5 flex flex-wrap items-center gap-3">
                        <button type="submit" class="md-btn md-btn-md md-btn-filled">Filtrar</button>

                        @if (request()->hasAny(['fecha_desde', 'fecha_hasta', 'usuario_id', 'modulo', 'accion']))
                            <a href="{{ route('auditoria.index') }}" class="md-btn md-btn-md md-btn-text">
                                Limpiar filtros
                            </a>
                        @endif
                    </div>
                </form>
            </section>

            {{-- Resultados --}}
            <section class="md-card overflow-hidden">
                <div class="md-section flex flex-wrap items-center justify-between gap-3">
                    <h3 class="md-section-title">Registros encontrados</h3>

                    <span class="md-badge">{{ $auditorias->count() }}</span>
                </div>

                @if ($auditorias->isEmpty())
                    <x-estado-vacio
                        titulo="Sin registros"
                        :descripcion="request()->hasAny(['fecha_desde', 'fecha_hasta', 'usuario_id', 'modulo', 'accion'])
                            ? 'No se encontraron registros con los filtros aplicados.'
                            : 'Todavía no hay actividad registrada en el sistema.'"
                    >
                        <x-slot:accion>
                            @if (request()->hasAny(['fecha_desde', 'fecha_hasta', 'usuario_id', 'modulo', 'accion']))
                                <a href="{{ route('auditoria.index') }}" class="md-btn md-btn-sm md-btn-outlined">
                                    Limpiar filtros
                                </a>
                            @endif
                        </x-slot:accion>
                    </x-estado-vacio>
                @else
                    <div class="overflow-x-auto">
                        <table class="md-table">
                            <thead>
                                <tr>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Usuario</th>
                                    <th scope="col">Módulo</th>
                                    <th scope="col">Acción</th>
                                    <th scope="col">Modelo</th>
                                    <th scope="col">ID</th>
                                    <th scope="col">Descripción</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($auditorias as $auditoria)
                                    <tr>
                                        <td class="whitespace-nowrap tabular-nums text-gray-500 dark:text-gray-400">
                                            {{ $auditoria->created_at->format('d/m/Y H:i:s') }}
                                        </td>

                                        <td class="font-medium text-gray-900 dark:text-white">
                                            {{ $auditoria->usuario?->name ?? 'Sistema' }}
                                        </td>

                                        <td>
                                            <span class="md-chip bg-gray-100 text-gray-700 dark:bg-gray-700/70 dark:text-gray-200">
                                                {{ $auditoria->modulo }}
                                            </span>
                                        </td>

                                        <td>{{ $auditoria->accion }}</td>

                                        <td class="font-mono text-xs">{{ $auditoria->modelo ?? '—' }}</td>

                                        <td class="font-mono text-xs">{{ $auditoria->modelo_id ?? '—' }}</td>

                                        <td class="max-w-md">
                                            <span class="line-clamp-2" title="{{ $auditoria->descripcion ?? '' }}">
                                                {{ $auditoria->descripcion ?? '—' }}
                                            </span>
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