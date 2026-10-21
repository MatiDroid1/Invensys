<?php

namespace App\Models;

use App\Models\Concerns\PerteneceAOrganizacion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnidadMedida extends Model
{
    use HasFactory, PerteneceAOrganizacion;

    protected $table = 'unidad_medidas';

    /**
     * Los IDs de organización no deben asignarse desde formularios o requests.
     */
    protected $guarded = ['id', 'organizacion_id'];

    public function articulos(): HasMany
    {
        return $this->hasMany(Articulo::class);
    }
}
