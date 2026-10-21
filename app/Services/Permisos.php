<?php

namespace App\Services;

use App\Enums\Permiso;
use App\Models\User;

/**
 * Resuelve qué permisos tiene cada usuario.
 *
 * Esta clase es el único punto donde se decide la autorización. Todo lo demás
 * pregunta aquí (a través de Gates y Policies), de modo que cambiar el modelo
 * de permisos es tocar un solo archivo.
 *
 * Estado actual (Fase 2): el permiso depende del rol heredado de `users.rol`.
 * Estado objetivo (Fase 3): el permiso depende del rol dentro de la
 * organización activa más las anomalías registradas por organización. El
 * cambio se hará en `de()` y en ningún otro lado.
 */
class Permisos
{
    /**
     * Permisos de un usuario.
     *
     * @return array<int, Permiso>
     */
    public function de(?User $usuario): array
    {
        if ($usuario === null) {
            return [];
        }

        if (! $usuario->activo) {
            return [];
        }

        return match ($usuario->rol) {
            'admin' => Permiso::cases(),
            default => $this->delOperador(),
        };
    }

    /**
     * ¿El usuario tiene ese permiso?
     */
    public function puede(?User $usuario, Permiso $permiso): bool
    {
        return in_array($permiso, $this->de($usuario), true);
    }

    /**
     * Permisos del rol operativo por defecto.
     *
     * Conserva exactamente lo que el sistema permitía antes de existir el
     * control de permisos: operar el inventario y mantener el catálogo.
     *
     * Lo que **no** incluye, y es deliberado:
     *
     * - `costos.editar`: un operador puede registrar entradas sin costo, pero
     *   no puede fijar el costo unitario ni el precio de venta. Modificar el
     *   costo falsea la valorización del inventario que se entrega al
     *   contador, así que queda reservado a quien administra.
     * - `auditoria.ver`, `usuarios.gestionar`, `categorias.gestionar`,
     *   `unidades.gestionar`, `importar`, `configuracion.editar`: son
     *   tareas de administración, no de operación diaria.
     *
     * Sí incluye `movimientos.anular`: quien registra un movimiento también
     * necesita corregirlo. La anulación no borra nada, genera un movimiento
     * compensatorio y queda auditada, así que el riesgo está controlado.
     *
     * @return array<int, Permiso>
     */
    private function delOperador(): array
    {
        return [
            Permiso::ArticulosVer,
            Permiso::ArticulosCrear,
            Permiso::ArticulosEditar,
            Permiso::ArticulosEliminar,
            Permiso::MovimientosVer,
            Permiso::MovimientosEntrada,
            Permiso::MovimientosSalida,
            Permiso::MovimientosAjuste,
            Permiso::MovimientosAnular,
            Permiso::PersonasVer,
            Permiso::PersonasCrear,
            Permiso::PersonasEditar,
            Permiso::PersonasEliminar,
            Permiso::CostosVer,
            Permiso::Exportar,
            Permiso::MensajeriaUsar,
        ];
    }
}
