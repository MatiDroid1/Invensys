<?php

namespace App\Support;

/**
 * Límites numéricos alineados con el tipo de columna en la base de datos.
 *
 * Sin estos topes, un valor mayor que el que admite la columna produce un
 * `QueryException` y una pantalla de error 500 en vez de un mensaje de
 * validación. Los valores coinciden exactamente con:
 *
 * - `movimientos.cantidad`      → decimal(12,2)
 * - `articulos.stock_minimo`    → decimal(12,2)
 * - `articulos.precio_venta`    → decimal(12,2)
 * - `movimientos.costo_unitario`→ decimal(12,4)
 */
final class Limites
{
    /** Máximo de una cantidad decimal(12,2). */
    public const CANTIDAD_MAX = '9999999999.99';

    /** Máximo de un costo decimal(12,4). */
    public const COSTO_MAX = '99999999.9999';

    /** Máximo de un precio decimal(12,2). */
    public const PRECIO_MAX = '9999999999.99';

    /** Máximo de un stock mínimo decimal(12,2). */
    public const STOCK_MINIMO_MAX = '9999999999.99';

    /**
     * Reglas de una cantidad de movimiento.
     *
     * @return array<int, string>
     */
    public static function cantidad(): array
    {
        return ['numeric', 'gt:0', 'decimal:0,2', 'max:'.self::CANTIDAD_MAX];
    }

    /**
     * Reglas de un costo unitario.
     *
     * @return array<int, string>
     */
    public static function costo(): array
    {
        return ['numeric', 'min:0', 'decimal:0,4', 'max:'.self::COSTO_MAX];
    }

    /**
     * Reglas de un precio de venta.
     *
     * @return array<int, string>
     */
    public static function precio(): array
    {
        return ['numeric', 'min:0', 'decimal:0,2', 'max:'.self::PRECIO_MAX];
    }

    /**
     * Reglas de un stock mínimo.
     *
     * @return array<int, string>
     */
    public static function stockMinimo(): array
    {
        return ['numeric', 'min:0', 'decimal:0,2', 'max:'.self::STOCK_MINIMO_MAX];
    }
}
