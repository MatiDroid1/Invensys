<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizacionUsuario extends Model
{
    protected $table = 'organizacion_usuario';

    protected $fillable = [
        'organizacion_id',
        'user_id',
        'rol',
        'predeterminada',
    ];

    protected function casts(): array
    {
        return [
            'predeterminada' => 'boolean',
        ];
    }

    public function organizacion(): BelongsTo
    {
        return $this->belongsTo(Organizacion::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
