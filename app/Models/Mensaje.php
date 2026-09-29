<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mensaje extends Model
{
    use HasFactory;

    protected $table = 'mensajes';

    protected $fillable = [
        'conversacion_id',
        'usuario_id',
        'cuerpo',
        'leido_en',
    ];

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
