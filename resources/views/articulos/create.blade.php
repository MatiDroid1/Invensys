{{-- El texto de ayuda incluye un enlace, por eso va como slot y no como atributo. --}}
<x-pagina-form
    titulo="Nuevo artículo"
    :ruta="route('articulos.store')"
    :ruta-listado="route('articulos.index')"
    texto-accion="Guardar artículo"
>
    <x-slot:descripcion>
        Registra un artículo. El stock inicial se registra después con una
        <a href="{{ route('movimientos.create') }}" class="font-semibold text-indigo-600 hover:underline dark:text-indigo-300">
            entrada de inventario
        </a>.
    </x-slot:descripcion>

    @include('articulos.partials.form')
</x-pagina-form>