<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Nuevo mensaje
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <p class="mb-6 text-gray-600 dark:text-gray-300">
                        Elige un usuario para escribirle. Si ya existe una conversación entre ustedes, se continuará en el mismo hilo.
                    </p>

                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-100 text-red-800 rounded-md">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($disponibles->isEmpty())

                        <p class="mb-6 text-gray-600 dark:text-gray-300">
                            No hay otros usuarios activos a los que escribir.
                        </p>

                        <a
                            href="{{ route('mensajes.index') }}"
                            class="px-4 py-2 text-gray-600 dark:text-gray-300"
                        >
                            Volver a mis mensajes
                        </a>

                    @else

                        <form method="POST" action="{{ route('mensajes.store') }}">
                            @csrf

                            <div class="mb-4">
                                <label
                                    for="usuario_receptor_id"
                                    class="block font-medium text-sm"
                                >
                                    Destinatario
                                </label>

                                <select
                                    name="usuario_receptor_id"
                                    id="usuario_receptor_id"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100"
                                >
                                    <option value="">Seleccione un usuario...</option>

                                    @foreach ($disponibles as $usuario)
                                        <option
                                            value="{{ $usuario->id }}"
                                            @selected((int) old('usuario_receptor_id', $destinatario?->id) === $usuario->id)
                                        >
                                            {{ $usuario->name }}
                                            @if ($usuario->isAdmin())
                                                (admin)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-6">
                                <label
                                    for="cuerpo"
                                    class="block font-medium text-sm"
                                >
                                    Mensaje
                                </label>

                                <textarea
                                    name="cuerpo"
                                    id="cuerpo"
                                    rows="6"
                                    maxlength="2000"
                                    placeholder="Escriba su mensaje. Puede dejarlo vacío y enviarlo luego desde la conversación."
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100 dark:placeholder-gray-500"
                                >{{ old('cuerpo') }}</textarea>
                            </div>

                            <div class="flex items-center gap-3">

                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700"
                                >
                                    Enviar mensaje
                                </button>

                                <a
                                    href="{{ route('mensajes.index') }}"
                                    class="px-4 py-2 text-gray-600 dark:text-gray-300"
                                >
                                    Cancelar
                                </a>

                            </div>

                        </form>

                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
