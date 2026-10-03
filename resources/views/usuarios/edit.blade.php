<x-pagina-form
    titulo="Editar usuario"
    :ruta="route('usuarios.update', $usuario)"
    :ruta-listado="route('usuarios.index')"
    metodo="PUT"
    texto-accion="Guardar cambios"
>
    <x-slot:descripcion>
        Los movimientos que registró este usuario se mantienen intactos aunque
        lo desactives.
    </x-slot:descripcion>

    <x-usuario-campos :usuario="$usuario" />
</x-pagina-form>