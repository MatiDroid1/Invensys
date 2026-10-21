<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Articulo extends Model
{
    use HasFactory;

    /**
     * Expresión única para calcular el stock a partir de los movimientos.
     * Se reutiliza en el accesor y en los reportes para que todos los
     * listados coincidan siempre con la misma regla de negocio.
     */
    public const EXPRESION_STOCK = "
        COALESCE(
            SUM(
                CASE
                    WHEN tipo IN ('ENTRADA', 'AJUSTE_POSITIVO') THEN cantidad
                    WHEN tipo IN ('SALIDA', 'AJUSTE_NEGATIVO') THEN -cantidad
                    ELSE 0
                END
            ),
            0
        )
    ";

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

    /**
     * Añade la columna calculada `stock_calculado` a la consulta.
     *
     * Evita el N+1 que producía el accesor `stock_actual` cuando se recorrían
     * artículos en un listado o en un `<select>`.
     */
    public function scopeConStock(Builder $query): Builder
    {
        return $query
            ->select('articulos.*')
            ->selectSub(
                Movimiento::selectRaw(self::EXPRESION_STOCK)
                    ->whereColumn('movimientos.articulo_id', 'articulos.id'),
                'stock_calculado'
            );
    }

    public function getStockActualAttribute(): float
    {
        if (array_key_exists('stock_calculado', $this->attributes)) {
            return (float) $this->attributes['stock_calculado'];
        }

        return (float) $this->movimientos()
            ->selectRaw(self::EXPRESION_STOCK.' AS stock')
            ->value('stock');
    }

    /**
     * Artículos activos cuyo stock calculado es igual o inferior al mínimo.
     *
     * @return Collection<int, Articulo>
     */
    public static function stockBajo()
    {
        return static::query()
            ->conStock()
            ->where('activo', true)
            ->get()
            ->filter(fn (self $articulo) => (float) $articulo->stock_calculado
                <= (float) $articulo->stock_minimo)
            ->values();
    }
}
