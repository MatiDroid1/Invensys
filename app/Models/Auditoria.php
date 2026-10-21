<?php

namespace App\Models;

use App\Models\Concerns\PerteneceAOrganizacion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auditoria extends Model
{
    use HasFactory, PerteneceAOrganizacion;

    protected $table = 'auditorias';

    /**
     * Los IDs de organización no deben asignarse desde formularios o requests.
     */
    protected $guarded = ['id', 'organizacion_id'];

    protected $casts = [
        'datos' => 'array',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
