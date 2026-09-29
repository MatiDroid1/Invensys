<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Movimientos de inventario
            </h2>

            <div class="flex gap-2">
                <a
                    href="{{ route('movimientos.create') }}"
                    class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700"
                >
                    Nueva entrada
                </a>

                <a
                    href="{{ route('movimientos.salida.create') }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                >
                    Nueva salida
                </a>
            </div>
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

                    @if ($movimientos->isEmpty())
                        <p>No hay movimientos registrados.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
                                        <th class="px-4 py-3 text-left">Fecha</th>
                                        <th class="px-4 py-3 text-left">Artículo</th>
                                        <th class="px-4 py-3 text-left">Tipo</th>
                                        <th class="px-4 py-3 text-left">Cantidad</th>
                                        <th class="px-4 py-3 text-left">Usuario</th>
                                        <th class="px-4 py-3 text-left">Persona</th>
                                        <th class="px-4 py-3 text-left">Referencia</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($movimientos as $movimiento)
                                        <tr class="border-b border-gray-200 dark:border-gray-700">

                                            <td class="px-4 py-3">
                                                {{ $movimiento->fecha_movimiento->format('d/m/Y H:i') }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $movimiento->articulo->codigo }}
                                                -
                                                {{ $movimiento->articulo->nombre }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $movimiento->tipo }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ number_format($movimiento->cantidad, 2, ',', '.') }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $movimiento->usuario->name }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $movimiento->persona?->nombre_completo ?? '-' }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $movimiento->referencia ?? '-' }}
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