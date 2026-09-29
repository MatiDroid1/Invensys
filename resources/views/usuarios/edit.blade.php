<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar usuario
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-md">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('usuarios.update', $usuario) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label
                                for="name"
                                class="block font-medium text-sm"
                            >
                                Nombre
                            </label>

                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old('name', $usuario->name) }}"
                                maxlength="255"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                            >
                        </div>

                        <div class="mb-4">
                            <label
                                for="email"
                                class="block font-medium text-sm"
                            >
                                Correo electrónico
                            </label>

                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old('email', $usuario->email) }}"
                                maxlength="255"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                            >
                        </div>

                        <div class="mb-4">
                            <label
                                for="rol"
                                class="block font-medium text-sm"
                            >
                                Rol
                            </label>

                            <select
                                name="rol"
                                id="rol"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                            >
                                <option
                                    value="usuario"
                                    @selected(old('rol', $usuario->rol) === 'usuario')
                                >
                                    Usuario
                                </option>

                                <option
                                    value="admin"
                                    @selected(old('rol', $usuario->rol) === 'admin')
                                >
                                    Administrador
                                </option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label
                                for="activo"
                                class="block font-medium text-sm"
                            >
                                Estado
                            </label>

                            <select
                                name="activo"
                                id="activo"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                            >
                                <option
                                    value="1"
                                    @selected(old('activo', $usuario->activo) == 1)
                                >
                                    Activo
                                </option>

                                <option
                                    value="0"
                                    @selected(old('activo', $usuario->activo) == 0)
                                >
                                    Inactivo
                                </option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label
                                for="password"
                                class="block font-medium text-sm"
                            >
                                Nueva contraseña
                            </label>

                            <input
                                type="password"
                                name="password"
                                id="password"
                                minlength="8"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                            >

                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Déjalo vacío para conservar la contraseña actual.
                            </p>
                        </div>

                        <div class="mb-6">
                            <label
                                for="password_confirmation"
                                class="block font-medium text-sm"
                            >
                                Confirmar nueva contraseña
                            </label>

                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                minlength="8"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                            >
                        </div>

                        <div class="flex items-center gap-3">

                            <button
                                type="submit"
                                class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700"
                            >
                                Guardar cambios
                            </button>

                            <a
                                href="{{ route('usuarios.index') }}"
                                class="px-4 py-2 text-gray-600 dark:text-gray-300"
                            >
                                Cancelar
                            </a>

                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>