<x-pagina-form
    titulo="Nueva unidad de medida"
    :ruta="route('unidades-medida.store')"
    :ruta-listado="route('unidades-medida.index')"
    texto-accion="Guardar unidad"
>
    <x-unidad-campos />
</x-pagina-form>