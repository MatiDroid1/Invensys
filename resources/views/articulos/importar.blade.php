<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="md-title">
                Importar artículos
            </h2>

            <a href="{{ route('articulos.index') }}" class="md-btn md-btn-text md-btn-sm">
                Volver al listado
            </a>
        </div>
    </x-slot>

    <div class="md-page md-page-body space-y-6">
        <x-alerta />

        @if (session('importacion'))
            @php
                $resultado = session('importacion');
            @endphp

            <section class="md-card p-5 sm:p-6">
                <h3 class="md-section-title">Resultado de la importación</h3>

                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="md-card-plain p-4">
                        <div class="md-overline">Artículos creados</div>
                        <div class="mt-2 text-2xl font-bold text-emerald-600">
                            {{ $resultado['creados'] }}
                        </div>
                    </div>

                    <div class="md-card-plain p-4">
                        <div class="md-overline">Artículos actualizados</div>
                        <div class="mt-2 text-2xl font-bold text-indigo-600">
                            {{ $resultado['actualizados'] }}
                        </div>
                    </div>

                    <div class="md-card-plain p-4">
                        <div class="md-overline">Filas con error</div>
                        <div @class([
                            'mt-2 text-2xl font-bold',
                            'text-red-600' => count($resultado['errores']) > 0,
                            'text-emerald-600' => count($resultado['errores']) === 0,
                        ])>
                            {{ count($resultado['errores']) }}
                        </div>
                    </div>
                </div>

                @if (count($resultado['errores']) > 0)
                    <div class="mt-5">
                        <h4 class="md-subtitle">Filas que no se pudieron importar</h4>

                        <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-700 dark:text-red-300">
                            @foreach ($resultado['errores'] as $linea => $motivo)
                                <li>
                                    Línea {{ $linea }}: {{ $motivo }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </section>
        @endif

        <section class="md-card mx-auto w-full max-w-3xl p-5 sm:p-6">
            <h3 class="md-section-title">Cargar archivo CSV</h3>

            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Sube un archivo CSV con los artículos a crear o actualizar. Las filas
                se cruzan por código: si el código ya existe se actualiza el artículo,
                y si no se crea uno nuevo. Las categorías y unidades deben existir y
                estar activas en el sistema.
            </p>

            <div class="mt-4 rounded-2xl bg-gray-50 p-4 text-sm dark:bg-gray-800/60">
                <p class="font-medium text-gray-900 dark:text-gray-100">
                    Columnas del archivo
                </p>

                <ul class="mt-2 grid grid-cols-1 gap-1 text-gray-600 dark:text-gray-300 sm:grid-cols-2">
                    <li><span class="font-medium">codigo</span> — obligatorio</li>
                    <li><span class="font-medium">nombre</span> — obligatorio</li>
                    <li><span class="font-medium">categoria</span> — obligatorio</li>
                    <li><span class="font-medium">unidad</span> — obligatorio</li>
                    <li><span class="font-medium">stock_minimo</span> — obligatorio</li>
                    <li><span class="font-medium">descripcion</span> — opcional</li>
                    <li><span class="font-medium">control_individual</span> — opcional (sí/no)</li>
                </ul>
            </div>

            <form
                method="POST"
                action="{{ route('articulos.importar.store') }}"
                enctype="multipart/form-data"
                class="mt-5 space-y-4"
            >
                @csrf

                <div>
                    <label for="archivo" class="md-label">
                        Archivo CSV
                    </label>

                    <input
                        type="file"
                        name="archivo"
                        id="archivo"
                        accept=".csv,.txt"
                        required
                        class="md-field mt-1"
                    />

                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Máximo {{ App\Services\ImportacionArticulosService::MAX_FILAS }} filas y 1 MB.
                    </p>
                </div>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <a
                        href="{{ route('articulos.importar.plantilla') }}"
                        class="md-btn md-btn-outlined"
                    >
                        Descargar plantilla CSV
                    </a>

                    <button type="submit" class="md-btn md-btn-filled">
                        Importar
                    </button>
                </div>
            </form>
        </section>
    </div>
</x-app-layout>
