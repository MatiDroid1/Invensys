<x-pagina-form
    titulo="Editar categoría"
    :ruta="route('categorias.update', $categoria)"
    :ruta-listado="route('categorias.index')"
    metodo="PUT"
    texto-accion="Guardar cambios"
>
    <x-slot:descripcion>
        <x-alerta tipo="info" :mensaje="'Estás editando «' . $categoria->nombre . '».'" />
    </x-slot:descripcion>

    <x-categoria-campos :categoria="$categoria" />
</x-pagina-form>