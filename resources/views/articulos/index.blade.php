<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="md-title">
                Artículos
            </h2>

            <a
                href="{{ route('articulos.create') }}"
                class="md-btn md-btn-filled"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>

                Nuevo artículo
            </a>
        </div>
    </x-slot>

    <div
        class="md-page md-page-body space-y-6"
        x-data="{ nombre: '', accion: '' }"
        x-on:confirmar-desactivacion.window="nombre = $event.detail.nombre; accion = $event.detail.accion"
    >
        {{-- Un solo modal para toda la tabla: se rellena con el artículo elegido
             desde la fila, en lugar de imprimir un confirm() del navegador por
             cada fila. --}}
        <x-modal name="desactivar-articulo" maxWidth="md">
            <form method="POST" x-bind:action="accion">
                @csrf
                @method('DELETE')

                <div class="p-6">
                    <h2 class="md-section-title">Desactivar artículo</h2>

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

        {{-- Filtros --}}
        <section class="md-card p-5 sm:p-6">
            <form method="GET" action="{{ route('articulos.index') }}">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                    <div class="sm:col-span-2">
                        <label for="q" class="md-label">
                            Buscar
                        </label>

                        <input
                            type="search"
                            name="q"
                            id="q"
                            value="{{ request('q') }}"
                            placeholder="Código, nombre o descripción"
                            class="md-field mt-1"
                        >
                    </div>

                    <div>
                        <label for="categoria_id" class="md-label">
                            Categoría
                        </label>

                        <select
                            name="categoria_id"
                            id="categoria_id"
                            class="md-field mt-1"
                        >
                            <option value="">Todas</option>

                            @foreach ($categorias as $categoria)
                                <option
                                    value="{{ $categoria->id }}"
                                    @selected(request('categoria_id') == $categoria->id)
                                >
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="estado" class="md-label">
                            Estado
                        </label>

                        <select
                            name="estado"
                            id="estado"
                            class="md-field mt-1"
                        >
                            <option value="">Todos</option>
                            <option value="activos" @selected(request('estado') === 'activos')>
                                Activos
                            </option>
                            <option value="inactivos" @selected(request('estado') === 'inactivos')>
                                Inactivos
                            </option>
                        </select>
                    </div>

                </div>

                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <button
                        type="submit"
                        class="md-btn md-btn-filled"
                    >
                        Filtrar
                    </button>

                    @if (request()->hasAny(['q', 'categoria_id', 'estado']))
                        <a
                            href="{{ route('articulos.index') }}"
                            class="md-btn md-btn-text md-btn-sm"
                        >
                            Limpiar filtros
                        </a>
                    @endif
                </div>
            </form>
        </section>

        {{-- Resultados --}}
        <section class="md-card">
            @if ($articulos->isEmpty())
                {{-- El icono va en una variable: escrito como atributo Blade, el SVG
                     saldría sin comillas en el PHP compilado. --}}
                @php
                    $iconoSinArticulos = '<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />';
                @endphp

                <x-estado-vacio
                    :icono="$iconoSinArticulos"
                    :descripcion="request()->hasAny(['q', 'categoria_id', 'estado']) ? 'No se encontraron artículos con los filtros aplicados.' : 'No hay artículos registrados.'"
                >
                    <x-slot:accion>
                        <a
                            href="{{ route('articulos.create') }}"
                            class="md-btn md-btn-filled"
                        >
                            Crear el primer artículo
                        </a>
                    </x-slot:accion>
                </x-estado-vacio>
            @else
                <x-tabla>
                    <thead>
                        <tr class="md-tr">
                            <th scope="col" class="md-th">
                                Código
                            </th>
                            <th scope="col" class="md-th">
                                Artículo
                            </th>
                            <th scope="col" class="md-th">
                                Categoría
                            </th>
                            <th scope="col" class="md-th md-th-num">
                                Mínimo
                            </th>
                            <th scope="col" class="md-th md-th-num">
                                Stock actual
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
                        @foreach ($articulos as $articulo)
                            @php
                                $stock = (float) $articulo->stock_calculado;
                                $minimo = (float) $articulo->stock_minimo;
                                $stockBajo = $stock <= $minimo;
                            @endphp

                            <tr class="md-tr">
                                <td class="md-td font-mono text-gray-500 dark:text-gray-400">
                                    {{ $articulo->codigo }}
                                </td>

                                <td class="md-td">
                                    <a
                                        href="{{ route('articulos.show', $articulo) }}"
                                        class="md-td-strong hover:text-indigo-600 dark:hover:text-indigo-400"
                                    >
                                        {{ $articulo->nombre }}
                                    </a>

                                    @if ($articulo->control_individual)
                                        <span class="mt-1 block text-xs text-gray-400 dark:text-gray-500">
                                            Control individual
                                        </span>
                                    @endif
                                </td>

                                <td class="md-td">
                                    {{ $articulo->categoria?->nombre ?? '-' }}
                                </td>

                                <td class="md-td md-td-num text-gray-500 dark:text-gray-400">
                                    {{ number_format($minimo, 2, ',', '.') }}
                                </td>

                                <td class="md-td md-td-num">
                                    <span @class([
                                        'md-badge',
                                        'md-badge-danger' => $stockBajo,
                                        'md-badge-success' => ! $stockBajo,
                                    ])>
                                        {{ number_format($stock, 2, ',', '.') }}
                                        {{ $articulo->unidadMedida?->abreviatura ?? '' }}
                                    </span>
                                </td>

                                <td class="md-td">
                                    <span @class([
                                        'md-badge',
                                        'md-badge-success' => $articulo->activo,
                                    ])>
                                        {{ $articulo->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>

                                <td class="md-td">
                                    <div class="flex items-center gap-1">
                                        <a
                                            href="{{ route('articulos.edit', $articulo) }}"
                                            class="md-btn md-btn-text md-btn-sm"
                                        >
                                            Editar
                                        </a>

@if ($articulo->activo)
                                            <button
                                                type="button"
                                                class="md-btn md-btn-text md-btn-sm !text-red-600 dark:!text-red-300"
                                                @click="$dispatch('confirmar-desactivacion', {
                                                    nombre: @js($articulo->nombre),
                                                    accion: @js(route('articulos.destroy', $articulo))
                                                })"
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

                <div class="md-divider mt-5 px-5 pb-5 pt-5">
                    {{ $articulos->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>