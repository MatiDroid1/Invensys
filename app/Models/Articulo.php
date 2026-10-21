<?php

namespace App\Models;

use App\Models\Concerns\PerteneceAOrganizacion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Articulo extends Model
{
    use HasFactory, PerteneceAOrganizacion;

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

    /**
     * Los IDs de organización no deben asignarse desde formularios o requests.
     */
    protected $guarded = ['id', 'organizacion_id'];

    protected $casts = [
        'stock_minimo' => 'decimal:2',
        'costo_promedio' => 'decimal:4',
        'precio_venta' => 'decimal:2',
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
     * Valor del stock actual al costo promedio del artículo.
     *
     * Devuelve null mientras el artículo no tenga costo registrado: así el
     * reporte muestra "sin valorizar" en lugar de un cero engañoso.
     */
    public function getValorInventarioAttribute(): ?float
    {
        if ($this->costo_promedio === null) {
            return null;
        }

        return round($this->stock_actual * (float) $this->costo_promedio, 2);
    }

    /**
     * Margen unitario de venta sobre el costo promedio.
     *
     * Null mientras falte el costo o el precio: no tiene sentido un margen a
     * medias.
     */
    public function getMargenAttribute(): ?float
    {
        if ($this->costo_promedio === null || $this->precio_venta === null) {
            return null;
        }

        return round((float) $this->precio_venta - (float) $this->costo_promedio, 2);
    }

    /**
     * Margen como porcentaje del precio de venta.
     *
     * Null si no hay margen calculable, o si el precio de venta es cero (no se
     * puede dividir por cero).
     */
    public function getMargenPorcentajeAttribute(): ?float
    {
        $margen = $this->margen;

        if ($margen === null || (float) $this->precio_venta === 0.0) {
            return null;
        }

        return round($margen / (float) $this->precio_venta * 100, 2);
    }

    /**
     * Valor del stock actual al precio de venta.
     */
    public function getValorVentaAttribute(): ?float
    {
        if ($this->precio_venta === null) {
            return null;
        }

        return round($this->stock_actual * (float) $this->precio_venta, 2);
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
