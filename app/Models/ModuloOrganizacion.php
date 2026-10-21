<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuloOrganizacion extends Model
{
    protected $table = 'modulos_organizacion';

    protected $fillable = [
        'organizacion_id',
        'modulo',
        'habilitado',
        'configuracion',
    ];

    protected function casts(): array
    {
        return [
            'habilitado' => 'boolean',
            'configuracion' => 'array',
        ];
    }

    public function organizacion(): BelongsTo
    {
        return $this->belongsTo(Organizacion::class);
    }
}
