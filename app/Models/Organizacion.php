<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organizacion extends Model
{
    protected $table = 'organizaciones';

    protected $fillable = [
        'nombre',
        'slug',
        'razon_social',
        'rut',
        'identificador_tributario',
        'identificador_tipo',
        'direccion',
        'telefono',
        'email',
        'perfil',
        'configuracion',
        'plan',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'configuracion' => 'array',
            'activo' => 'boolean',
        ];
    }

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organizacion_usuario')
            ->withPivot('rol', 'predeterminada')
            ->withTimestamps();
    }

    public function membresias(): HasMany
    {
        return $this->hasMany(OrganizacionUsuario::class);
    }

    public function sucursales(): HasMany
    {
        return $this->hasMany(Sucursal::class);
    }

    public function bodegas(): HasMany
    {
        return $this->hasMany(Bodega::class);
    }

    public function proveedores(): HasMany
    {
        return $this->hasMany(Proveedor::class);
    }

    public function modulos(): HasMany
    {
        return $this->hasMany(ModuloOrganizacion::class);
    }
}
