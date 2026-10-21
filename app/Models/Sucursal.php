<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sucursal extends Model
{
    protected $table = 'sucursales';

    protected $fillable = [
        'organizacion_id',
        'nombre',
        'direccion',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function organizacion(): BelongsTo
    {
        return $this->belongsTo(Organizacion::class);
    }

    public function bodegas(): HasMany
    {
        return $this->hasMany(Bodega::class);
    }
}
