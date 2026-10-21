<?php

namespace App\Models;

use App\Models\Concerns\PerteneceAOrganizacion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mensaje extends Model
{
    use HasFactory, PerteneceAOrganizacion;

    protected $table = 'mensajes';

    /**
     * Los IDs de organización no deben asignarse desde formularios o requests.
     */
    protected $guarded = ['id', 'organizacion_id'];

    protected $casts = [
        'leido_en' => 'datetime',
    ];

    public function conversacion(): BelongsTo
    {
        return $this->belongsTo(Conversacion::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fueEnviadoPor(int $usuarioId): bool
    {
        return $this->usuario_id === $usuarioId;
    }
}
