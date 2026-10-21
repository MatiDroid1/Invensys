<?php

namespace App\Models;

use App\Models\Concerns\PerteneceAOrganizacion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Movimiento extends Model
{
    use HasFactory, PerteneceAOrganizacion;

    protected $table = 'movimientos';

    /**
     * Movimiento confirmado: es el estado normal de todo asiento.
     */
    public const ESTADO_CONFIRMADO = 'CONFIRMADO';

    /**
     * Movimiento anulado: ya no cuenta como operación vigente.
     *
     * No se borra ni se edita. La anulación crea además un movimiento
     * compensatorio, de modo que el historial sigue cuadrando.
     */
    public const ESTADO_ANULADO = 'ANULADO';

    /**
     * Tipos que suman existencias.
     */
    public const TIPOS_ENTRADA = ['ENTRADA', 'AJUSTE_POSITIVO'];

    /**
     * Tipos que restan existencias.
     */
    public const TIPOS_SALIDA = ['SALIDA', 'AJUSTE_NEGATIVO'];

    /**
     * Los IDs de organización no deben asignarse desde formularios o requests.
     */
    protected $guarded = ['id', 'organizacion_id'];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'costo_unitario' => 'decimal:4',
        'fecha_movimiento' => 'datetime',
        'anulado_at' => 'datetime',
    ];

    /**
     * Valor monetario del movimiento (cantidad × costo congelado).
     *
     * Null si el artículo no tiene costos registrados.
     */
    public function getValorAttribute(): ?float
    {
        if ($this->costo_unitario === null) {
            return null;
        }

        return round((float) $this->cantidad * (float) $this->costo_unitario, 2);
    }

    /**
     * ¿El movimiento está vigente?
     */
    public function estaAnulado(): bool
    {
        return $this->estado === self::ESTADO_ANULADO;
    }

    /**
     * ¿El movimiento suma existencias?
     */
    public function esEntrada(): bool
    {
        return in_array($this->tipo, self::TIPOS_ENTRADA, true);
    }

    /**
     * ¿El movimiento resta existencias?
     */
    public function esSalida(): bool
    {
        return in_array($this->tipo, self::TIPOS_SALIDA, true);
    }

    /**
     * Etiqueta legible del tipo de movimiento.
     */
    public function getTipoEtiquetaAttribute(): string
    {
        return match ($this->tipo) {
            'ENTRADA' => 'Entrada',
            'SALIDA' => 'Salida',
            'AJUSTE_POSITIVO' => 'Ajuste positivo',
            'AJUSTE_NEGATIVO' => 'Ajuste negativo',
            default => $this->tipo,
        };
    }

    public function articulo(): BelongsTo
    {
        return $this->belongsTo(Articulo::class);
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function anuladoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'anulado_por_id');
    }

    /**
     * Movimiento original que este compensa (si es una reversión).
     */
    public function reversaDe(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reversa_de_id');
    }

    /**
     * Movimiento compensatorio que anula a este (si fue anulado).
     */
    public function compensatorio(): BelongsTo
    {
        return $this->belongsTo(self::class, 'id', 'reversa_de_id');
    }
}
