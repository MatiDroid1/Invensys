<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Contacto
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
                        Utiliza este formulario para enviar una consulta, sugerencia o solicitud.
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

                    <form method="POST" action="{{ route('contacto.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label
                                for="nombre"
                                class="block font-medium text-sm"
                            >
                                Nombre
                            </label>

                            <input
                                type="text"
                                name="nombre"
                                id="nombre"
                                value="{{ old('nombre', auth()->user()->name) }}"
                                maxlength="150"
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
                                value="{{ old('email', auth()->user()->email) }}"
                                maxlength="150"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                            >
                        </div>

                        <div class="mb-4">
                            <label
                                for="asunto"
                                class="block font-medium text-sm"
                            >
                                Asunto
                            </label>

                            <input
                                type="text"
                                name="asunto"
                                id="asunto"
                                value="{{ old('asunto') }}"
                                maxlength="150"
                                required
                                placeholder="Ej: Consulta sobre inventario"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                            >
                        </div>

                        <div class="mb-6">
                            <label
                                for="mensaje"
                                class="block font-medium text-sm"
                            >
                                Mensaje
                            </label>

                            <textarea
                                name="mensaje"
                                id="mensaje"
                                rows="6"
                                required
                                placeholder="Escriba su mensaje..."
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                            >{{ old('mensaje') }}</textarea>
                        </div>

                        <div class="flex items-center gap-3">

                            <button
                                type="submit"
                                class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700"
                            >
                                Enviar mensaje
                            </button>

                            <a
                                href="{{ route('dashboard') }}"
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