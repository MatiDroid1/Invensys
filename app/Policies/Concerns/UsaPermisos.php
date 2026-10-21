<?php

namespace App\Policies\Concerns;

use App\Enums\Permiso;
use App\Models\User;

/**
 * Atajo para las Policies que solo dependen de un permiso.
 *
 * Evita repetir la misma comprobación en cada método de cada policy.
 */
trait UsaPermisos
{
    protected function permitido(User $usuario, Permiso $permiso): bool
    {
        return $usuario->puede($permiso);
    }
}
