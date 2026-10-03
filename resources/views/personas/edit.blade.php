<x-pagina-form
    titulo="Editar persona"
    :ruta="route('personas.update', $persona)"
    :ruta-listado="route('personas.index')"
    metodo="PUT"
    texto-accion="Guardar cambios"
>
    <x-slot:descripcion>
        Los movimientos ya registrados no cambian: la edición afecta solo a los
        datos que se muestran de aquí en adelante.
    </x-slot:descripcion>

    <x-persona-campos :persona="$persona" />
</x-pagina-form>