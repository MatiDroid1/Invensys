<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar persona
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if ($errors->any())
                        <div class="mb-4">
                            <ul class="list-disc list-inside text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('personas.update', $persona) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <div>
                                <label for="nombre" class="block font-medium text-sm">
                                    Nombre
                                </label>

                                <input
                                    type="text"
                                    name="nombre"
                                    id="nombre"
                                    value="{{ old('nombre', $persona->nombre) }}"
                                    maxlength="100"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                >
                            </div>

                            <div>
                                <label for="apellido" class="block font-medium text-sm">
                                    Apellido
                                </label>

                                <input
                                    type="text"
                                    name="apellido"
                                    id="apellido"
                                    value="{{ old('apellido', $persona->apellido) }}"
                                    maxlength="100"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                >
                            </div>

                            <div>
                                <label for="identificador" class="block font-medium text-sm">
                                    Identificador
                                </label>

                                <input
                                    type="text"
                                    name="identificador"
                                    id="identificador"
                                    value="{{ old('identificador', $persona->identificador) }}"
                                    maxlength="50"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                >
                            </div>

                            <div>
                                <label for="email" class="block font-medium text-sm">
                                    Correo electrónico
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    value="{{ old('email', $persona->email) }}"
                                    maxlength="150"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                >
                            </div>

                            <div>
                                <label for="area" class="block font-medium text-sm">
                                    Área
                                </label>

                                <input
                                    type="text"
                                    name="area"
                                    id="area"
                                    value="{{ old('area', $persona->area) }}"
                                    maxlength="100"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                >
                            </div>

                            <div>
                                <label for="cargo" class="block font-medium text-sm">
                                    Cargo
                                </label>

                                <input
                                    type="text"
                                    name="cargo"
                                    id="cargo"
                                    value="{{ old('cargo', $persona->cargo) }}"
                                    maxlength="100"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                >
                            </div>

                            <div>
                                <label for="activo" class="block font-medium text-sm">
                                    Estado
                                </label>

                                <select
                                    name="activo"
                                    id="activo"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                    required
                                >
                                    <option value="1" @selected(old('activo', $persona->activo) == 1)>
                                        Activo
                                    </option>

                                    <option value="0" @selected(old('activo', $persona->activo) == 0)>
                                        Inactivo
                                    </option>
                                </select>
                            </div>

                        </div>

                        <div class="flex items-center gap-3 mt-6">

                            <button
                                type="submit"
                                class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700"
                            >
                                Guardar cambios
                            </button>

                            <a
                                href="{{ route('personas.index') }}"
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