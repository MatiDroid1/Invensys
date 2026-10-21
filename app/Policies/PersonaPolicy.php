<?php

namespace App\Policies;

use App\Enums\Permiso;
use App\Models\Persona;
use App\Models\User;
use App\Policies\Concerns\UsaPermisos;

/**
 * Permisos sobre las personas (destinatarios de entregas).
 */
class PersonaPolicy
{
    use UsaPermisos;

    public function viewAny(User $usuario): bool
    {
        return $this->permitido($usuario, Permiso::PersonasVer);
    }

    public function view(User $usuario, Persona $persona): bool
    {
        return $this->permitido($usuario, Permiso::PersonasVer);
    }

    public function create(User $usuario): bool
    {
        return $this->permitido($usuario, Permiso::PersonasCrear);
    }

    public function update(User $usuario, Persona $persona): bool
    {
        return $this->permitido($usuario, Permiso::PersonasEditar);
    }

    public function delete(User $usuario, Persona $persona): bool
    {
        return $this->permitido($usuario, Permiso::PersonasEliminar);
    }
}
