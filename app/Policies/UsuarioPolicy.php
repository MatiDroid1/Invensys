<?php

namespace App\Policies;

use App\Enums\Permiso;
use App\Models\User;
use App\Policies\Concerns\UsaPermisos;

/**
 * Permisos sobre las cuentas de usuario.
 */
class UsuarioPolicy
{
    use UsaPermisos;

    public function viewAny(User $usuario): bool
    {
        return $this->permitido($usuario, Permiso::UsuariosGestionar);
    }

    public function create(User $usuario): bool
    {
        return $this->permitido($usuario, Permiso::UsuariosGestionar);
    }

    public function update(User $usuario, User $modelo): bool
    {
        return $this->permitido($usuario, Permiso::UsuariosGestionar);
    }

    public function delete(User $usuario, User $modelo): bool
    {
        return $this->permitido($usuario, Permiso::UsuariosGestionar);
    }
}
