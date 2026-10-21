<?php

namespace App\Policies;

use App\Enums\Permiso;
use App\Models\Movimiento;
use App\Models\User;
use App\Policies\Concerns\UsaPermisos;

/**
 * Permisos sobre los movimientos de inventario.
 *
 * Los tres tipos de registro (entrada, salida y ajuste) tienen permisos
 * separados: autorizar un ajuste es una decisión distinta a registrar una
 * entrada, porque un ajuste modifica el stock sin una operación de por medio.
 */
class MovimientoPolicy
{
    use UsaPermisos;

    public function viewAny(User $usuario): bool
    {
        return $this->permitido($usuario, Permiso::MovimientosVer);
    }

    public function view(User $usuario, Movimiento $movimiento): bool
    {
        return $this->permitido($usuario, Permiso::MovimientosVer);
    }

    public function registrarEntrada(User $usuario): bool
    {
        return $this->permitido($usuario, Permiso::MovimientosEntrada);
    }

    public function registrarSalida(User $usuario): bool
    {
        return $this->permitido($usuario, Permiso::MovimientosSalida);
    }

    public function registrarAjuste(User $usuario): bool
    {
        return $this->permitido($usuario, Permiso::MovimientosAjuste);
    }

    public function anular(User $usuario, Movimiento $movimiento): bool
    {
        return $this->permitido($usuario, Permiso::MovimientosAnular);
    }
}
