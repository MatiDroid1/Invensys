<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="md-title">Artículos</h2>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ number_format($articulos->total()) }} {{ $articulos->total() === 1 ? 'artículo' : 'artículos' }} en el catálogo.
                </p>
            </div>

            <a href="{{ route('articulos.create') }}" class="md-btn md-btn-md md-btn-filled">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>

                Nuevo artículo
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            <x-alerta tipo="success" />
            <x-alerta tipo="error" />

            {{-- Filtros --}}
            <section class="md-card p-5">
                <form method="GET" action="{{ route('articulos.index') }}">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                        <div class="sm:col-span-2">
                            <label for="q" class="md-label">Buscar</label>

                            <input
                                type="search"
                                name="q"
                                id="q"
                                value="{{ request('q') }}"
                                placeholder="Código, nombre o descripción"
                                class="md-field mt-1.5"
                            >
                        </div>

                        <div>
                            <label for="categoria_id" class="md-label">Categoría</label>

                            <select name="categoria_id" id="categoria_id" class="md-field mt-1.5">
                                <option value="">Todas</option>

                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" @selected(request('categoria_id') == $categoria->id)>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="estado" class="md-label">Estado</label>

                            <select name="estado" id="estado" class="md-field mt-1.5">
                                <option value="">Todos</option>
                                <option value="activos" @selected(request('estado') === 'activos')>Activos</option>
                                <option value="inactivos" @selected(request('estado') === 'inactivos')>Inactivos</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <button type="submit" class="md-btn md-btn-md md-btn-filled">Filtrar</button>

                        @if (request()->hasAny(['q', 'categoria_id', 'estado']))
                            <a href="{{ route('articulos.index') }}" class="md-btn md-btn-md md-btn-text">
                                Limpiar filtros
                            </a>
                        @endif
                    </div>
                </form>
            </section>

            {{-- Resultados --}}
            <section class="md-card overflow-hidden">
                @if ($articulos->isEmpty())

                    <x-estado-vacio>
                        @if (request()->hasAny(['q', 'categoria_id', 'estado']))
                            No se encontraron artículos con los filtros aplicados.
                        @else
                            No hay artículos registrados.
                        @endif

                        <x-slot name="accion">
                            @if (request()->hasAny(['q', 'categoria_id', 'estado']))
                                <a href="{{ route('articulos.index') }}" class="md-btn md-btn-sm md-btn-outlined">
                                    Limpiar filtros
                                </a>
                            @else
                                <a href="{{ route('articulos.create') }}" class="md-btn md-btn-sm md-btn-filled">
                                    Crear el primer artículo
                                </a>
                            @endif
                        </x-slot>
                    </x-estado-vacio>

                @else

                    <div class="overflow-x-auto">
                        <table class="md-table">
                            <thead>
                                <tr>
                                    <th scope="col">Código</th>
                                    <th scope="col">Artículo</th>
                                    <th scope="col">Categoría</th>
                                    <th scope="col" class="text-end">Mínimo</th>
                                    <th scope="col" class="text-end">Stock actual</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col" class="text-end">Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($articulos as $articulo)
                                    @php
                                        $stock = (float) $articulo->stock_calculado;
                                        $minimo = (float) $articulo->stock_minimo;
                                        $stockBajo = $stock <= $minimo;
                                    @endphp

                                    <tr>
                                        <td class="whitespace-nowrap tabular-nums text-gray-500 dark:text-gray-400">
                                            {{ $articulo->codigo }}
                                        </td>

                                        <td>
                                            <a
                                                href="{{ route('articulos.show', $articulo) }}"
                                                class="font-medium text-gray-900 transition-colors hover:text-indigo-600 dark:text-white dark:hover:text-indigo-400"
                                            >
                                                {{ $articulo->nombre }}
                                            </a>

                                            @if ($articulo->control_individual)
                                                <span class="md-badge mt-1">Control individual</span>
                                            @endif
                                        </td>

                                        <td>{{ $articulo->categoria?->nombre ?? '—' }}</td>

                                        <td class="whitespace-nowrap text-end tabular-nums text-gray-500 dark:text-gray-400">
                                            {{ number_format($minimo, 2, ',', '.') }}
                                        </td>

                                        <td class="whitespace-nowrap text-end">
                                            {{-- El stock va en una etiqueta y no en texto suelto
                                                 porque es el número que se busca al mirar
                                                 la tabla: en rojo si hay que reponer. --}}
                                            <span @class([
                                                'inline-flex items-center gap-1 whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold tabular-nums',
                                                'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-300' => $stockBajo,
                                                'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' => ! $stockBajo,
                                            ])>
                                                {{ number_format($stock, 2, ',', '.') }}
                                                {{ $articulo->unidadMedida?->abreviatura ?? '' }}
                                            </span>
                                        </td>

                                        <td class="whitespace-nowrap">
                                            <span @class([
                                                'md-chip',
                                                'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' => $articulo->activo,
                                                'bg-gray-100 text-gray-600 dark:bg-gray-700/70 dark:text-gray-300' => ! $articulo->activo,
                                            ])>
                                                {{ $articulo->activo ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>

                                        <td class="whitespace-nowrap text-end">
                                            <div class="inline-flex items-center gap-1">
                                                <a
                                                    href="{{ route('articulos.edit', $articulo) }}"
                                                    class="md-icon-btn"
                                                    title="Editar {{ $articulo->nombre }}"
                                                    aria-label="Editar {{ $articulo->nombre }}"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-4 w-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                                    </svg>
                                                </a>

                                                @if ($articulo->activo)
                                                    {{-- Desactivar es una acción destructiva: usa el
                                                         diálogo en vez del confirm() del navegador,
                                                         que no se puede estilar y en algunos
                                                         navegadores ya ni se ve. --}}
                                                    <x-modal name="desactivar-articulo-{{ $articulo->id }}" maxWidth="lg">
                                                        <x-slot name="title">
                                                            Desactivar artículo
                                                        </x-slot>

                                                        {{--
                                                            La prosa va corta y lo único
                                                            que cambia de largo (el nombre)
                                                            va en su propia caja. Así el
                                                            diálogo mantiene siempre la
                                                            misma forma y el texto se acopla
                                                            al ancho en vez de estirar el
                                                            recuadro o salirse de él.
                                                        --}}
                                                        <x-slot name="content">
                                                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                                                Dejará de aparecer en los formularios
                                                                de movimientos.
                                                            </p>

                                                            <p class="mt-3 break-words rounded-xl bg-gray-50 px-3 py-2 text-sm font-medium text-gray-900 dark:bg-gray-700/40 dark:text-gray-100">
                                                                {{ $articulo->nombre }}
                                                            </p>
                                                        </x-slot>

                                                        <x-slot name="footer">
                                                            <button
                                                                type="button"
                                                                class="md-btn md-btn-md md-btn-outlined"
                                                                x-on:click="show = false"
                                                            >
                                                                Cancelar
                                                            </button>

                                                            <form method="POST" action="{{ route('articulos.destroy', $articulo) }}">
                                                                @csrf
                                                                @method('DELETE')

                                                                <button
                                                                    type="submit"
                                                                    class="md-btn md-btn-md md-btn-danger"
                                                                    x-on:click="show = false"
                                                                >
                                                                    Desactivar
                                                                </button>
                                                            </form>
                                                        </x-slot>
                                                    </x-modal>

                                                    <button
                                                        type="button"
                                                        x-on:click="$dispatch('open-modal', 'desactivar-articulo-{{ $articulo->id }}')"
                                                        class="md-icon-btn hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-500/10 dark:hover:text-red-400"
                                                        title="Desactivar {{ $articulo->nombre }}"
                                                        aria-label="Desactivar {{ $articulo->nombre }}"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-4 w-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 9.122L18.375 12.74zm-9.665-9.665a3 3 0 011.06-1.06l2.122 2.122a3 3 0 01-1.06 1.06l-2.122-2.122z" />
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t border-gray-100 px-5 py-4 dark:border-gray-700/70">
                        {{ $articulos->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>