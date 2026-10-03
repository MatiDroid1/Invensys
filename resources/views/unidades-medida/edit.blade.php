<x-pagina-form
    titulo="Editar unidad de medida"
    :ruta="route('unidades-medida.update', $unidadMedida)"
    :ruta-listado="route('unidades-medida.index')"
    metodo="PUT"
    texto-accion="Guardar cambios"
>
    <x-slot:descripcion>
        <x-alerta tipo="info" :mensaje="'Estás editando «' . $unidadMedida->nombre . '».'" />
    </x-slot:descripcion>

    <x-unidad-campos :unidad="$unidadMedida" />
</x-pagina-form>