<?php

namespace App\Models;

use App\Enums\Permiso;
use App\Services\Permisos;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'sonido_mensajes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
            'sonido_mensajes' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    /**
     * ¿El usuario tiene ese permiso?
     *
     * Es la forma canónica de preguntar por autorización dentro del código.
     * La respuesta sale de `App\Services\Permisos`, que es el único punto
     * donde se decide el modelo de permisos.
     */
    public function puede(Permiso|string $permiso): bool
    {
        $permiso = $permiso instanceof Permiso
            ? $permiso
            : Permiso::tryFrom($permiso);

        if ($permiso === null) {
            return false;
        }

        return app(Permisos::class)->puede($this, $permiso);
    }

    public function conversacionesEnviadas(): HasMany
    {
        return $this->hasMany(Conversacion::class, 'usuario_emisor_id');
    }

    public function conversacionesRecibidas(): HasMany
    {
        return $this->hasMany(Conversacion::class, 'usuario_receptor_id');
    }

    public function mensajes(): HasMany
    {
        return $this->hasMany(Mensaje::class);
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(Movimiento::class, 'usuario_id');
    }

    /**
     * Mensajes recibidos que el usuario aún no ha leído. Alimenta el contador
     * de la barra de navegación.
     */
    public function mensajesNoLeidos(): int
    {
        return Mensaje::query()
            ->whereNull('leido_en')
            ->where('usuario_id', '!=', $this->id)
            ->whereIn('conversacion_id', function ($query) {
                $query->select('id')
                    ->from('conversaciones')
                    ->where('usuario_emisor_id', $this->id)
                    ->orWhere('usuario_receptor_id', $this->id);
            })
            ->count();
    }
}
