<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Usuarios
            </h2>

            <a
                href="{{ route('usuarios.create') }}"
                class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700"
            >
                Nuevo usuario
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if ($usuarios->isEmpty())
                        <p>No hay usuarios registrados.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
                                        <th class="px-4 py-3 text-left">Nombre</th>
                                        <th class="px-4 py-3 text-left">Email</th>
                                        <th class="px-4 py-3 text-left">Rol</th>
                                        <th class="px-4 py-3 text-left">Estado</th>
                                        <th class="px-4 py-3 text-left">Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($usuarios as $usuario)
                                        <tr class="border-b border-gray-200 dark:border-gray-700">

                                            <td class="px-4 py-3 font-medium">
                                                {{ $usuario->name }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $usuario->email }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $usuario->rol === 'admin' ? 'Administrador' : 'Usuario' }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                                            </td>

                                            <td class="px-4 py-3">
                                                <a
                                                    href="{{ route('usuarios.edit', $usuario) }}"
                                                    class="text-blue-600 hover:underline"
                                                >
                                                    Editar
                                                </a>
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