<x-pagina-form
    titulo="Nueva categoría"
    :ruta="route('categorias.store')"
    :ruta-listado="route('categorias.index')"
    texto-accion="Guardar categoría"
>
    <x-categoria-campos />
</x-pagina-form>