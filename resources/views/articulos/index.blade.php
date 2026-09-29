<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Artículos
            </h2>

            <a
                href="{{ route('articulos.create') }}"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo artículo
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div
                    role="status"
                    class="flex items-start gap-3 p-4 bg-green-50 text-green-800 border border-green-200 rounded-lg dark:bg-green-900/30 dark:text-green-200 dark:border-green-800"
                >
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            @endif

            {{-- Filtros --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100">
                    <form method="GET" action="{{ route('articulos.index') }}">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                            <div class="sm:col-span-2">
                                <label for="q" class="block text-sm font-medium mb-1">
                                    Buscar
                                </label>

                                <input
                                    type="search"
                                    name="q"
                                    id="q"
                                    value="{{ request('q') }}"
                                    placeholder="Código, nombre o descripción"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                >
                            </div>

                            <div>
                                <label for="categoria_id" class="block text-sm font-medium mb-1">
                                    Categoría
                                </label>

                                <select
                                    name="categoria_id"
                                    id="categoria_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
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
                                <label for="estado" class="block text-sm font-medium mb-1">
                                    Estado
                                </label>

                                <select
                                    name="estado"
                                    id="estado"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
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
                                class="px-4 py-2 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700"
                            >
                                Filtrar
                            </button>

                            @if (request()->hasAny(['q', 'categoria_id', 'estado']))
                                <a
                                    href="{{ route('articulos.index') }}"
                                    class="text-sm text-gray-600 hover:underline dark:text-gray-300"
                                >
                                    Limpiar filtros
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Resultados --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100">

                    @if ($articulos->isEmpty())
                        <div class="py-12 text-center">
                            <svg
                                class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"
                                />
                            </svg>

                            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                                @if (request()->hasAny(['q', 'categoria_id', 'estado']))
                                    No se encontraron artículos con los filtros aplicados.
                                @else
                                    No hay artículos registrados.
                                @endif
                            </p>

                            <a
                                href="{{ route('articulos.create') }}"
                                class="mt-4 inline-block text-sm font-medium text-indigo-600 hover:underline dark:text-indigo-400"
                            >
                                Crear el primer artículo
                            </a>
                        </div>
                    @else
                        <div class="overflow-x-auto -mx-4 sm:-mx-6">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900/50">
                                    <tr>
                                        <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Código
                                        </th>
                                        <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Artículo
                                        </th>
                                        <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Categoría
                                        </th>
                                        <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Mínimo
                                        </th>
                                        <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Stock actual
                                        </th>
                                        <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Estado
                                        </th>
                                        <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($articulos as $articulo)
                                        @php
                                            $stock = (float) $articulo->stock_calculado;
                                            $minimo = (float) $articulo->stock_minimo;
                                            $stockBajo = $stock <= $minimo;
                                        @endphp

                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/40 transition-colors">
                                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500 dark:text-gray-400">
                                                {{ $articulo->codigo }}
                                            </td>

                                            <td class="px-4 sm:px-6 py-4">
                                                <a
                                                    href="{{ route('articulos.show', $articulo) }}"
                                                    class="text-sm font-medium text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400"
                                                >
                                                    {{ $articulo->nombre }}
                                                </a>

                                                @if ($articulo->control_individual)
                                                    <span class="mt-1 inline-block text-xs text-gray-400">
                                                        Control individual
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="px-4 sm:px-6 py-4 text-sm">
                                                {{ $articulo->categoria?->nombre ?? '-' }}
                                            </td>

                                            <td class="px-4 sm:px-6 py-4 text-right text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                                {{ number_format($minimo, 2, ',', '.') }}
                                            </td>

                                            <td class="px-4 sm:px-6 py-4 text-right whitespace-nowrap">
                                                <span
                                                    @class([
                                                        'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold',
                                                        'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300' => $stockBajo,
                                                        'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300' => ! $stockBajo,
                                                    ])
                                                >
                                                    {{ number_format($stock, 2, ',', '.') }}
                                                    {{ $articulo->unidadMedida?->abreviatura ?? '' }}
                                                </span>
                                            </td>

                                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                                <span
                                                    @class([
                                                        'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium',
                                                        'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300' => $articulo->activo,
                                                        'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' => ! $articulo->activo,
                                                    ])
                                                >
                                                    {{ $articulo->activo ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>

                                            <td class="px-4 sm:px-6 py-4 text-right whitespace-nowrap">
                                                <div class="inline-flex items-center gap-3">
                                                    <a
                                                        href="{{ route('articulos.edit', $articulo) }}"
                                                        class="text-sm font-medium text-indigo-600 hover:underline dark:text-indigo-400"
                                                    >
                                                        Editar
                                                    </a>

                                                    @if ($articulo->activo)
                                                        <form
                                                            method="POST"
                                                            action="{{ route('articulos.destroy', $articulo) }}"
                                                            onsubmit="return confirm('¿Está seguro de desactivar este artículo? No se eliminarán sus movimientos.');"
                                                        >
                                                            @csrf
                                                            @method('DELETE')

                                                            <button
                                                                type="submit"
                                                                class="text-sm font-medium text-red-600 hover:underline dark:text-red-400"
                                                            >
                                                                Desactivar
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $articulos->links() }}
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
