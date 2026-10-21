<?php

namespace App\Models;

use App\Models\Concerns\PerteneceAOrganizacion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Persona extends Model
{
    use HasFactory, PerteneceAOrganizacion;

    protected $table = 'personas';

    /**
     * Los IDs de organización no deben asignarse desde formularios o requests.
     */
    protected $guarded = ['id', 'organizacion_id'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function getNombreCompletoAttribute(): string
    {
        return trim($this->nombre.' '.$this->apellido);
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(Movimiento::class);
    }
}
