<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class OrganizacionActual
{
    private static bool $sinOrganizacion = false;

    private static ?int $organizacionId = null;

    public static function id(): ?int
    {
        if (self::$sinOrganizacion) {
            return null;
        }

        if (self::$organizacionId !== null) {
            return self::$organizacionId;
        }

        $id = Session::get('organizacion_id');

        if ($id !== null) {
            self::$organizacionId = (int) $id;
        }

        return self::$organizacionId;
    }

    public static function set(?int $organizacionId): void
    {
        self::$sinOrganizacion = false;
        self::$organizacionId = $organizacionId;

        if ($organizacionId === null) {
            Session::forget('organizacion_id');

            return;
        }

        Session::put('organizacion_id', $organizacionId);
    }

    public static function olvide(): void
    {
        self::$organizacionId = null;
        self::$sinOrganizacion = false;
        Session::forget('organizacion_id');
    }

    /**
     * Desactiva el scope por organización. Solo debe usarse en consola,
     * seeders, jobs o migraciones, y debe registrarse en el log.
     */
    public static function sinOrganizacion(string $razon = ''): void
    {
        self::$sinOrganizacion = true;
        self::$organizacionId = null;

        $contexto = [
            'razon' => $razon,
            'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 6),
        ];

        Log::warning('OrganizacionActual::sinOrganizacion() activado', $contexto);
    }

    public static function conOrganizacion(): bool
    {
        return ! self::$sinOrganizacion && self::id() !== null;
    }
}
