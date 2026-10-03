<x-pagina-form
    titulo="Nueva entrada de inventario"
    :ruta="route('movimientos.store')"
    :ruta-listado="route('dashboard')"
    texto-volver="Volver al inicio"
    texto-accion="Registrar entrada"
>
    <x-slot:descripcion>
        Una entrada suma unidades al stock del artículo y queda asentada en el Kardex.
    </x-slot:descripcion>

    <x-movimiento-campos
        :articulos="$articulos"
        :articulo-preseleccionado="$articuloPreseleccionado"
        placeholder-referencia="Ej: Envío Casa Matriz #123"
    />
</x-pagina-form>