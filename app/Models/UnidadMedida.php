<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnidadMedida extends Model
{
    use HasFactory;

    protected $table = 'unidad_medidas';

    protected $fillable = [
        'nombre',
        'abreviatura',
        'activo',
    ];

    public function articulos(): HasMany
    {
        return $this->hasMany(Articulo::class);
    }
}
