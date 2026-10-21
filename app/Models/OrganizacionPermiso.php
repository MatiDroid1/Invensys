<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizacionPermiso extends Model
{
    protected $table = 'organizacion_permisos';

    protected $fillable = [
        'organizacion_id',
        'rol',
        'permiso',
        'permitido',
    ];

    protected function casts(): array
    {
        return [
            'permitido' => 'boolean',
        ];
    }

    public function organizacion(): BelongsTo
    {
        return $this->belongsTo(Organizacion::class);
    }
}
