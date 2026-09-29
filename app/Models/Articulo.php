<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Articulo extends Model
{
    use HasFactory;

    protected $table = 'articulos';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'categoria_id',
        'unidad_medida_id',
        'stock_minimo',
        'control_individual',
        'activo',
    ];

    protected $casts = [
        'stock_minimo' => 'decimal:2',
        'control_individual' => 'boolean',
        'activo' => 'boolean',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function unidadMedida(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class);
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(Movimiento::class);
    }

    public function getStockActualAttribute(): float
    {
        return (float) $this->movimientos()
            ->selectRaw("
            COALESCE(
                SUM(
                    CASE
                        WHEN tipo IN ('ENTRADA', 'AJUSTE_POSITIVO') THEN cantidad
                        WHEN tipo IN ('SALIDA', 'AJUSTE_NEGATIVO') THEN -cantidad
                        ELSE 0
                    END
                ),
                0
            ) AS stock
        ")
            ->value('stock');
    }
}
