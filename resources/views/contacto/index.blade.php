<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Bandeja de contacto
            </h2>

            <a
                href="{{ route('contacto.create') }}"
                class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700"
            >
                Nuevo mensaje
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

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if ($contactos->isEmpty())

                        <p>No hay mensajes de contacto registrados.</p>

                    @else

                        <div class="overflow-x-auto">
                            <table class="min-w-full">

                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-700">

                                        <th class="px-4 py-3 text-left">
                                            Fecha
                                        </th>

                                        <th class="px-4 py-3 text-left">
                                            Nombre
                                        </th>

                                        <th class="px-4 py-3 text-left">
                                            Email
                                        </th>

                                        <th class="px-4 py-3 text-left">
                                            Asunto
                                        </th>

                                        <th class="px-4 py-3 text-left">
                                            Mensaje
                                        </th>

                                        <th class="px-4 py-3 text-left">
                                            Estado
                                        </th>

                                        <th class="px-4 py-3 text-left">
                                            Acción
                                        </th>

                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach ($contactos as $contacto)

                                        <tr class="border-b border-gray-200 dark:border-gray-700 align-top">

                                            <td class="px-4 py-3 whitespace-nowrap">
                                                {{ $contacto->created_at->format('d/m/Y H:i') }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $contacto->nombre }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $contacto->email }}
                                            </td>

                                            <td class="px-4 py-3 font-medium">
                                                {{ $contacto->asunto }}
                                            </td>

                                            <td class="px-4 py-3 max-w-md">
                                                {{ $contacto->mensaje }}
                                            </td>

                                            <td class="px-4 py-3">
                                                @if ($contacto->estado === 'PENDIENTE')
                                                    <span class="text-red-600 font-semibold">
                                                        Pendiente
                                                    </span>
                                                @else
                                                    <span class="text-green-600 font-semibold">
                                                        Atendido
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="px-4 py-3">

                                                @if ($contacto->estado === 'PENDIENTE')
                                                    <form
                                                        method="POST"
                                                        action="{{ route('contacto.atender', $contacto) }}"
                                                    >
                                                        @csrf
                                                        @method('PATCH')

                                                        <button
                                                            type="submit"
                                                            class="text-blue-600 hover:underline"
                                                        >
                                                            Marcar atendido
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-gray-500">
                                                        -
                                                    </span>
                                                @endif

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