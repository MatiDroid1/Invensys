<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Auditoría del sistema
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Filtros -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="GET" action="{{ route('auditoria.index') }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">

                            <div>
                                <label
                                    for="fecha_desde"
                                    class="block font-medium text-sm"
                                >
                                    Fecha desde
                                </label>

                                <input
                                    type="date"
                                    name="fecha_desde"
                                    id="fecha_desde"
                                    value="{{ $fechaDesde }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                >
                            </div>

                            <div>
                                <label
                                    for="fecha_hasta"
                                    class="block font-medium text-sm"
                                >
                                    Fecha hasta
                                </label>

                                <input
                                    type="date"
                                    name="fecha_hasta"
                                    id="fecha_hasta"
                                    value="{{ $fechaHasta }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                >
                            </div>

                            <div>
                                <label
                                    for="usuario_id"
                                    class="block font-medium text-sm"
                                >
                                    Usuario
                                </label>

                                <select
                                    name="usuario_id"
                                    id="usuario_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
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
                                <label
                                    for="modulo"
                                    class="block font-medium text-sm"
                                >
                                    Módulo
                                </label>

                                <select
                                    name="modulo"
                                    id="modulo"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
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
                                <label
                                    for="accion"
                                    class="block font-medium text-sm"
                                >
                                    Acción
                                </label>

                                <select
                                    name="accion"
                                    id="accion"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
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

                        <div class="flex items-center gap-3 mt-6">

                            <button
                                type="submit"
                                class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700"
                            >
                                Buscar
                            </button>

                            <a
                                href="{{ route('auditoria.index') }}"
                                class="px-4 py-2 text-gray-600 dark:text-gray-300"
                            >
                                Limpiar filtros
                            </a>

                        </div>

                    </form>

                </div>
            </div>

            <!-- Resultados -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">
                            Registros encontrados:
                            {{ $auditorias->count() }}
                        </h3>
                    </div>

                    @if ($auditorias->isEmpty())

                        <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-md">
                            No se encontraron registros de auditoría.
                        </div>

                    @else

                        <div class="overflow-x-auto">
                            <table class="min-w-full">

                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-700">

                                        <th class="px-4 py-3 text-left">
                                            Fecha
                                        </th>

                                        <th class="px-4 py-3 text-left">
                                            Usuario
                                        </th>

                                        <th class="px-4 py-3 text-left">
                                            Módulo
                                        </th>

                                        <th class="px-4 py-3 text-left">
                                            Acción
                                        </th>

                                        <th class="px-4 py-3 text-left">
                                            Modelo
                                        </th>

                                        <th class="px-4 py-3 text-left">
                                            ID
                                        </th>

                                        <th class="px-4 py-3 text-left">
                                            Descripción
                                        </th>

                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach ($auditorias as $auditoria)

                                        <tr class="border-b border-gray-200 dark:border-gray-700 align-top">

                                            <td class="px-4 py-3 whitespace-nowrap">
                                                {{ $auditoria->created_at->format('d/m/Y H:i:s') }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $auditoria->usuario?->name ?? 'Sistema' }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $auditoria->modulo }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $auditoria->accion }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $auditoria->modelo ?? '-' }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $auditoria->modelo_id ?? '-' }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $auditoria->descripcion ?? '-' }}
                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>
                        </div>

                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>