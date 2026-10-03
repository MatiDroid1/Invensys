<x-pagina-form
    titulo="Nuevo usuario"
    :ruta="route('usuarios.store')"
    :ruta-listado="route('usuarios.index')"
    texto-accion="Crear usuario"
>
    <x-slot:descripcion>
        El usuario entra al sistema con su correo y la contraseña que definas aquí.
    </x-slot:descripcion>

    <x-usuario-campos />
</x-pagina-form>