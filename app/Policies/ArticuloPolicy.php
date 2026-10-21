<?php

namespace App\Policies;

use App\Enums\Permiso;
use App\Models\Articulo;
use App\Models\User;
use App\Policies\Concerns\UsaPermisos;

/**
 * Permisos sobre el catálogo de artículos.
 */
class ArticuloPolicy
{
    use UsaPermisos;

    public function viewAny(User $usuario): bool
    {
        return $this->permitido($usuario, Permiso::ArticulosVer);
    }

    public function view(User $usuario, Articulo $articulo): bool
    {
        return $this->permitido($usuario, Permiso::ArticulosVer);
    }

    public function create(User $usuario): bool
    {
        return $this->permitido($usuario, Permiso::ArticulosCrear);
    }

    public function update(User $usuario, Articulo $articulo): bool
    {
        return $this->permitido($usuario, Permiso::ArticulosEditar);
    }

    public function delete(User $usuario, Articulo $articulo): bool
    {
        return $this->permitido($usuario, Permiso::ArticulosEliminar);
    }
}
