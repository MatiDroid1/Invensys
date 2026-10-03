<x-pagina-form
    titulo="Contacto"
    :ruta="route('contacto.store')"
    :ruta-listado="route('contacto.index')"
    texto-accion="Enviar mensaje"
>
    <x-slot:descripcion>
        Utiliza este formulario para enviar una consulta, sugerencia o solicitud.
    </x-slot:descripcion>

    <x-contacto-campos />
</x-pagina-form>