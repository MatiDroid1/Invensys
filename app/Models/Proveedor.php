<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'organizacion_id',
        'nombre',
        'identificador_tributario',
        'email',
        'telefono',
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
}
