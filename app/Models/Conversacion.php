<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversacion extends Model
{
    use HasFactory;

    protected $table = 'conversaciones';

    protected $fillable = [
        'usuario_emisor_id',
        'usuario_receptor_id',
        'ultimo_mensaje_en',
    ];

    protected $casts = [
        'ultimo_mensaje_en' => 'datetime',
    ];

    public function emisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_emisor_id');
    }

    public function receptor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_receptor_id');
    }

    public function mensajes(): HasMany
    {
        return $this->hasMany(Mensaje::class);
    }

    public function ultimoMensaje(): HasOne
    {
        return $this->hasOne(Mensaje::class)->latestOfMany();
    }

    /**
     * Conversaciones en las que participa el usuario, sin importar el orden
     * en que se haya creado la conversación.
     */
    public function scopeDelUsuario(Builder $query, int $usuarioId): Builder
    {
        return $query->where(function (Builder $query) use ($usuarioId) {
            $query->where('usuario_emisor_id', $usuarioId)
                ->orWhere('usuario_receptor_id', $usuarioId);
        });
    }

    public function participa(int $usuarioId): bool
    {
        return $this->usuario_emisor_id === $usuarioId
            || $this->usuario_receptor_id === $usuarioId;
    }

    /**
     * El usuario con el que se conversa, desde la perspectiva de $usuario.
     */
    public function interlocutor(User $usuario): User
    {
        return $this->usuario_emisor_id === $usuario->id
            ? $this->receptor
            : $this->emisor;
    }

    /**
     * Busca la conversación ya existente entre dos usuarios, en cualquier
     * orden, para no duplicar hilos.
     */
    public static function entre(int $usuarioId, int $otroUsuarioId): ?self
    {
        return static::query()
            ->where(function (Builder $query) use ($usuarioId, $otroUsuarioId) {
                $query->where(function (Builder $query) use ($usuarioId, $otroUsuarioId) {
                    $query->where('usuario_emisor_id', $usuarioId)
                        ->where('usuario_receptor_id', $otroUsuarioId);
                })->orWhere(function (Builder $query) use ($usuarioId, $otroUsuarioId) {
                    $query->where('usuario_emisor_id', $otroUsuarioId)
                        ->where('usuario_receptor_id', $usuarioId);
                });
            })
            ->first();
    }
}
