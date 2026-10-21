<?php

namespace App\Models\Concerns;

use App\Models\Organizacion;
use App\Support\OrganizacionActual;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

trait PerteneceAOrganizacion
{
    protected static function bootPerteneceAOrganizacion(): void
    {
        static::addGlobalScope('organizacion', function (Builder $builder) {
            $organizacionId = self::resolverOrganizacionId();

            if ($organizacionId !== null) {
                $builder->where($builder->getModel()->getTable().'.organizacion_id', $organizacionId);
            }
        });

        static::creating(function (Model $model) {
            if (isset($model->organizacion_id)) {
                return;
            }

            $organizacionId = self::resolverOrganizacionId(true);

            if ($organizacionId !== null) {
                $model->setAttribute('organizacion_id', $organizacionId);
            }
        });
    }

    private static function resolverOrganizacionId(bool $crearSiFalta = false): ?int
    {
        $organizacionId = OrganizacionActual::id();

        if ($organizacionId !== null) {
            return (int) $organizacionId;
        }

        if (! DB::getSchemaBuilder()->hasTable('organizaciones')) {
            return null;
        }

        $organizacionId = DB::table('organizaciones')->orderBy('id')->value('id');

        if ($organizacionId !== null) {
            return (int) $organizacionId;
        }

        if (App::runningUnitTests() && $crearSiFalta) {
            try {
                DB::statement('PRAGMA foreign_keys = OFF;');
                $organizacionId = DB::table('organizaciones')->insertGetId([
                    'nombre' => 'Organización Principal',
                    'slug' => 'principal',
                    'perfil' => 'GENERAL',
                    'activo' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                DB::statement('PRAGMA foreign_keys = ON;');

                OrganizacionActual::set((int) $organizacionId);

                return (int) $organizacionId;
            } catch (\Throwable $e) {
                try {
                    DB::statement('PRAGMA foreign_keys = ON;');
                } catch (\Throwable $ex) {
                }

                $organizacionId = DB::table('organizaciones')->orderBy('id')->value('id');

                if ($organizacionId !== null) {
                    return (int) $organizacionId;
                }
            }
        }

        return null;
    }

    public function verificarPertenencia(?int $organizacionId = null): bool
    {
        $objetivo = $organizacionId ?? OrganizacionActual::id() ?? self::resolverOrganizacionId();

        if ($objetivo === null) {
            return true;
        }

        return isset($this->organizacion_id) && (int) $this->organizacion_id === (int) $objetivo;
    }

    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class);
    }
}
