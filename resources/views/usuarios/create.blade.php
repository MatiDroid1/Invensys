<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Nuevo usuario
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

                    <form method="POST" action="{{ route('usuarios.store') }}">
                        @csrf

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
                                value="{{ old('name') }}"
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
                                Email
                            </label>

                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old('email') }}"
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
                                    @selected(old('rol', 'usuario') === 'usuario')
                                >
                                    Usuario
                                </option>

                                <option
                                    value="admin"
                                    @selected(old('rol') === 'admin')
                                >
                                    Administrador
                                </option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label
                                for="password"
                                class="block font-medium text-sm"
                            >
                                Contraseña
                            </label>

                            <input
                                type="password"
                                name="password"
                                id="password"
                                minlength="8"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                            >
                        </div>

                        <div class="mb-6">
                            <label
                                for="password_confirmation"
                                class="block font-medium text-sm"
                            >
                                Confirmar contraseña
                            </label>

                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                minlength="8"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                            >
                        </div>

                        <div class="flex items-center gap-3">

                            <button
                                type="submit"
                                class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700"
                            >
                                Crear usuario
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