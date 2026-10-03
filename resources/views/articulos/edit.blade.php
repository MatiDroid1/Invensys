<x-pagina-form
    titulo="Editar artículo"
    :ruta="route('articulos.update', $articulo)"
    :ruta-listado="route('articulos.index')"
    metodo="PUT"
    texto-accion="Guardar cambios"
>
    <x-slot:extras>
        <a href="{{ route('articulos.show', $articulo) }}" class="md-btn md-btn-sm md-btn-text">
            Ver detalle
        </a>
    </x-slot:extras>

    <x-slot:descripcion>
        <span class="md-overline">Código</span>
        <span class="font-mono font-semibold">{{ $articulo->codigo }}</span>

        <span class="hidden sm:inline text-gray-300 dark:text-gray-600">|</span>

        Stock actual:
        <span class="font-semibold text-gray-900 dark:text-white">
            {{ number_format($articulo->stock_actual, 2, ',', '.') }}
        </span>
    </x-slot:descripcion>

    @include('articulos.partials.form')
</x-pagina-form>