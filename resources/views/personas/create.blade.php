<x-pagina-form
    titulo="Nueva persona"
    :ruta="route('personas.store')"
    :ruta-listado="route('personas.index')"
    texto-accion="Guardar persona"
>
    <x-slot:descripcion>
        Registra a quién se le entregarán los materiales. La persona aparece como
        destinatario en el formulario de salida de inventario.
    </x-slot:descripcion>

    <x-persona-campos />
</x-pagina-form>