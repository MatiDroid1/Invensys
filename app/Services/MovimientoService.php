<?php

namespace App\Services;

use App\Models\Articulo;
use App\Models\Movimiento;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MovimientoService
{
    public function __construct(
        private AuditoriaService $auditoriaService
    ) {}

    /**
     * Registra una entrada de inventario.
     *
     * @param  string|null  $idempotencyKey  Clave única de la operación. Si ya
     *                                       existe un movimiento con esa
     *                                       clave se devuelve ese movimiento
     *                                       en vez de crear otro: un doble
     *                                       clic o un reintento no duplican
     *                                       la entrada.
     */
    public function registrarEntrada(
        int $articuloId,
        float $cantidad,
        string $fechaMovimiento,
        ?float $costoUnitario,
        ?string $referencia,
        ?string $observaciones,
        int $usuarioId,
        ?string $idempotencyKey = null
    ): Movimiento {
        return DB::transaction(function () use (
            $articuloId,
            $cantidad,
            $fechaMovimiento,
            $costoUnitario,
            $referencia,
            $observaciones,
            $usuarioId,
            $idempotencyKey
        ) {
            if ($idempotencyKey !== null) {
                $existente = $this->movimientoConClave($idempotencyKey);

                if ($existente !== null) {
                    return $existente;
                }
            }

            $stockAnterior = $this->stockActual($articuloId);
            $promedioAnterior = $this->promedioActual($articuloId);

            $nuevoPromedio = $this->promedioTrasEntrada(
                $stockAnterior,
                $promedioAnterior,
                $cantidad,
                $costoUnitario
            );

            $movimiento = $this->crearMovimiento([
                'idempotency_key' => $idempotencyKey,
                'articulo_id' => $articuloId,
                'tipo' => 'ENTRADA',
                'cantidad' => $cantidad,
                'costo_unitario' => $costoUnitario ?? $promedioAnterior,
                'fecha_movimiento' => $fechaMovimiento,
                'persona_id' => null,
                'usuario_id' => $usuarioId,
                'referencia' => $referencia,
                'observaciones' => $observaciones,
            ], $idempotencyKey);

            $this->actualizarCostoPromedio($articuloId, $promedioAnterior, $nuevoPromedio);

            $this->auditoriaService->registrar(
                modulo: 'movimientos',
                accion: 'ENTRADA',
                modelo: $movimiento,
                descripcion: 'Entrada de inventario registrada.',
                datos: [
                    'articulo_id' => $articuloId,
                    'cantidad' => $cantidad,
                    'costo_unitario' => $movimiento->costo_unitario,
                    'referencia' => $referencia,
                ],
                usuarioId: $usuarioId,
            );

            return $movimiento;
        });
    }

    /**
     * Registra una salida de inventario.
     */
    public function registrarSalida(
        int $articuloId,
        int $personaId,
        float $cantidad,
        string $fechaMovimiento,
        ?string $referencia,
        ?string $observaciones,
        int $usuarioId,
        ?string $idempotencyKey = null
    ): Movimiento {
        return DB::transaction(function () use (
            $articuloId,
            $personaId,
            $cantidad,
            $fechaMovimiento,
            $referencia,
            $observaciones,
            $usuarioId,
            $idempotencyKey
        ) {
            if ($idempotencyKey !== null) {
                $existente = $this->movimientoConClave($idempotencyKey);

                if ($existente !== null) {
                    return $existente;
                }
            }

            $stockActual = $this->stockActual($articuloId);

            if ($cantidad > $stockActual) {
                throw ValidationException::withMessages([
                    'cantidad' => "Stock insuficiente. Stock disponible: {$stockActual}.",
                ]);
            }

            $promedioAnterior = $this->promedioActual($articuloId);

            $movimiento = $this->crearMovimiento([
                'idempotency_key' => $idempotencyKey,
                'articulo_id' => $articuloId,
                'tipo' => 'SALIDA',
                'cantidad' => $cantidad,
                // La salida congela el costo vigente; nunca modifica el promedio.
                'costo_unitario' => $promedioAnterior,
                'fecha_movimiento' => $fechaMovimiento,
                'persona_id' => $personaId,
                'usuario_id' => $usuarioId,
                'referencia' => $referencia,
                'observaciones' => $observaciones,
            ], $idempotencyKey);

            $this->auditoriaService->registrar(
                modulo: 'movimientos',
                accion: 'SALIDA',
                modelo: $movimiento,
                descripcion: 'Salida de inventario registrada.',
                datos: [
                    'articulo_id' => $articuloId,
                    'cantidad' => $cantidad,
                    'costo_unitario' => $movimiento->costo_unitario,
                    'persona_id' => $personaId,
                    'referencia' => $referencia,
                ],
                usuarioId: $usuarioId,
            );

            return $movimiento;
        });
    }

    /**
     * Registra un ajuste de inventario.
     */
    public function registrarAjuste(
        int $articuloId,
        string $tipo,
        float $cantidad,
        string $fechaMovimiento,
        ?float $costoUnitario,
        ?string $referencia,
        ?string $observaciones,
        int $usuarioId,
        ?string $idempotencyKey = null
    ): Movimiento {
        return DB::transaction(function () use (
            $articuloId,
            $tipo,
            $cantidad,
            $fechaMovimiento,
            $costoUnitario,
            $referencia,
            $observaciones,
            $usuarioId,
            $idempotencyKey
        ) {
            if ($idempotencyKey !== null) {
                $existente = $this->movimientoConClave($idempotencyKey);

                if ($existente !== null) {
                    return $existente;
                }
            }

            $stockAnterior = $this->stockActual($articuloId);
            $promedioAnterior = $this->promedioActual($articuloId);

            if ($tipo === 'AJUSTE_NEGATIVO' && $cantidad > $stockAnterior) {
                throw ValidationException::withMessages([
                    'cantidad' => "El ajuste negativo no puede superar el stock disponible. Stock actual: {$stockAnterior}.",
                ]);
            }

            if ($tipo === 'AJUSTE_POSITIVO') {
                $nuevoPromedio = $this->promedioTrasEntrada(
                    $stockAnterior,
                    $promedioAnterior,
                    $cantidad,
                    $costoUnitario
                );
            } else {
                $nuevoPromedio = $promedioAnterior;
            }

            $movimiento = $this->crearMovimiento([
                'idempotency_key' => $idempotencyKey,
                'articulo_id' => $articuloId,
                'tipo' => $tipo,
                'cantidad' => $cantidad,
                'costo_unitario' => $costoUnitario ?? $promedioAnterior,
                'fecha_movimiento' => $fechaMovimiento,
                'persona_id' => null,
                'usuario_id' => $usuarioId,
                'referencia' => $referencia,
                'observaciones' => $observaciones,
            ], $idempotencyKey);

            $this->actualizarCostoPromedio($articuloId, $promedioAnterior, $nuevoPromedio);

            $this->auditoriaService->registrar(
                modulo: 'movimientos',
                accion: $tipo,
                modelo: $movimiento,
                descripcion: 'Ajuste de inventario registrado.',
                datos: [
                    'articulo_id' => $articuloId,
                    'tipo' => $tipo,
                    'cantidad' => $cantidad,
                    'costo_unitario' => $movimiento->costo_unitario,
                    'referencia' => $referencia,
                ],
                usuarioId: $usuarioId,
            );

            return $movimiento;
        });
    }

    /**
     * Anula un movimiento ya confirmado.
     *
     * Un movimiento confirmado no se edita ni se borra: se anula. La anulación
     * genera un movimiento compensatorio de signo contrario, de modo que el
     * historial cuadra por sí solo y no hay que corregir nada a mano.
     *
     * El movimiento original queda marcado como `ANULADO`, pero **sigue
     * contando** en el cálculo del stock: lo cancela el compensatorio.
     * Excluirlo dejaría el inventario con el signo cambiado.
     *
     * El costo promedio del artículo no se recalcula: la valorización
     * histórica no se mueve por una anulación.
     */
    public function anularMovimiento(
        int $movimientoId,
        string $motivo,
        int $usuarioId
    ): Movimiento {
        return DB::transaction(function () use ($movimientoId, $motivo, $usuarioId) {
            $movimiento = Movimiento::whereKey($movimientoId)
                ->lockForUpdate()
                ->first();

            if ($movimiento === null) {
                throw ValidationException::withMessages([
                    'movimiento' => 'El movimiento no existe.',
                ]);
            }

            if ($movimiento->estaAnulado()) {
                throw ValidationException::withMessages([
                    'movimiento' => 'Este movimiento ya fue anulado.',
                ]);
            }

            if ($movimiento->compensatorio()->exists()) {
                throw ValidationException::withMessages([
                    'movimiento' => 'Este movimiento ya tiene una anulación registrada.',
                ]);
            }

            $motivo = trim($motivo);

            if ($motivo === '') {
                throw ValidationException::withMessages([
                    'motivo' => 'Indica el motivo de la anulación.',
                ]);
            }

            $tipoCompensatorio = $this->tipoCompensatorio($movimiento->tipo);

            $stock = $this->stockActual($movimiento->articulo_id);

            if (in_array($tipoCompensatorio, Movimiento::TIPOS_SALIDA, true)
                && (float) $movimiento->cantidad > $stock) {
                throw ValidationException::withMessages([
                    'movimiento' => 'No se puede anular: las existencias actuales ('
                        .$stock.') no alcanzan a compensar el movimiento.',
                ]);
            }

            $compensatorio = Movimiento::create([
                'articulo_id' => $movimiento->articulo_id,
                'tipo' => $tipoCompensatorio,
                'cantidad' => $movimiento->cantidad,
                'costo_unitario' => $movimiento->costo_unitario,
                'fecha_movimiento' => now(),
                'persona_id' => null,
                'usuario_id' => $usuarioId,
                'referencia' => $movimiento->referencia,
                'observaciones' => 'Anulación del movimiento #'.$movimiento->id.': '.$motivo,
                'estado' => Movimiento::ESTADO_CONFIRMADO,
                'motivo' => $motivo,
                'reversa_de_id' => $movimiento->id,
            ]);

            $movimiento->update([
                'estado' => Movimiento::ESTADO_ANULADO,
                'motivo' => $motivo,
                'anulado_por_id' => $usuarioId,
                'anulado_at' => now(),
            ]);

            $this->auditoriaService->registrar(
                modulo: 'movimientos',
                accion: 'ANULAR',
                modelo: $movimiento,
                descripcion: 'Movimiento anulado con movimiento compensatorio #'
                    .$compensatorio->id.'.',
                datos: [
                    'movimiento_id' => $movimiento->id,
                    'compensatorio_id' => $compensatorio->id,
                    'tipo' => $movimiento->tipo,
                    'cantidad' => $movimiento->cantidad,
                    'motivo' => $motivo,
                ],
                usuarioId: $usuarioId,
            );

            return $compensatorio;
        });
    }

    /**
     * Tipo de movimiento que compensa al indicado.
     */
    private function tipoCompensatorio(string $tipo): string
    {
        return match ($tipo) {
            'ENTRADA' => 'SALIDA',
            'SALIDA' => 'ENTRADA',
            'AJUSTE_POSITIVO' => 'AJUSTE_NEGATIVO',
            'AJUSTE_NEGATIVO' => 'AJUSTE_POSITIVO',
            default => throw ValidationException::withMessages([
                'movimiento' => 'Este tipo de movimiento no se puede anular.',
            ]),
        };
    }

    /**
     * Busca un movimiento ya registrado con esa clave de idempotencia.
     */
    private function movimientoConClave(string $idempotencyKey): ?Movimiento
    {
        return Movimiento::where('idempotency_key', $idempotencyKey)->first();
    }

    /**
     * Crea el movimiento respetando la clave de idempotencia.
     *
     * El índice único es la garantía real contra dos peticiones simultáneas
     * con la misma clave: si una gana la carrera, la otra recibe el error de
     * base de datos y se convierte en "ya estaba registrado".
     *
     * @param  array<string, mixed>  $datos
     */
    private function crearMovimiento(array $datos, ?string $idempotencyKey): Movimiento
    {
        try {
            return Movimiento::create($datos);
        } catch (QueryException $e) {
            if ($idempotencyKey === null || ! $this->esViolacionDeUnicidad($e)) {
                throw $e;
            }

            $existente = $this->movimientoConClave($idempotencyKey);

            if ($existente === null) {
                throw $e;
            }

            return $existente;
        }
    }

    /**
     * ¿La excepción corresponde a una violación de unicidad?
     *
     * MySQL y SQLite reportan SQLSTATE 23000; el texto se compara también
     * para no depender del driver.
     */
    private function esViolacionDeUnicidad(QueryException $e): bool
    {
        $sqlState = (string) ($e->errorInfo[0] ?? '');

        if ($sqlState === '23000') {
            return true;
        }

        return str_contains($e->getMessage(), 'UNIQUE constraint failed')
            || str_contains($e->getMessage(), 'Duplicate entry');
    }

    /**
     * Bloquea la fila del artículo para serializar movimientos concurrentes.
     * El stock se lee siempre desde los movimientos, nunca desde un contador.
     */
    private function bloquearArticulo(int $articuloId): Articulo
    {
        $articulo = Articulo::whereKey($articuloId)
            ->lockForUpdate()
            ->first();

        if ($articulo === null) {
            throw ValidationException::withMessages([
                'articulo_id' => 'El artículo ya no existe.',
            ]);
        }

        return $articulo;
    }

    private function stockActual(int $articuloId): float
    {
        $articulo = $this->bloquearArticulo($articuloId);

        return (float) $articulo->movimientos()
            ->selectRaw(Articulo::EXPRESION_STOCK.' AS stock')
            ->value('stock');
    }

    private function promedioActual(int $articuloId): ?float
    {
        $articulo = $this->bloquearArticulo($articuloId);

        return $articulo->costo_promedio !== null
            ? (float) $articulo->costo_promedio
            : null;
    }

    /**
     * Costo promedio ponderado después de sumar una entrada.
     *
     * Si el artículo aún no tiene costo registrado, la entrada sin costo deja
     * el promedio vacío; la entrada con costo lo siembra por primera vez.
     */
    private function promedioTrasEntrada(
        float $stockAnterior,
        ?float $promedioAnterior,
        float $cantidad,
        ?float $costoUnitario
    ): ?float {
        if ($costoUnitario === null) {
            return $promedioAnterior;
        }

        $stockUtil = max($stockAnterior, 0);
        $stockNuevo = $stockUtil + $cantidad;

        if ($stockNuevo <= 0) {
            return $costoUnitario;
        }

        $valorAnterior = $stockUtil * ($promedioAnterior ?? 0);
        $valorEntrada = $cantidad * $costoUnitario;

        return round(($valorAnterior + $valorEntrada) / $stockNuevo, 4);
    }

    private function actualizarCostoPromedio(
        int $articuloId,
        ?float $promedioAnterior,
        ?float $nuevoPromedio
    ): void {
        if ($nuevoPromedio === $promedioAnterior) {
            return;
        }

        Articulo::whereKey($articuloId)->update([
            'costo_promedio' => $nuevoPromedio,
        ]);
    }
}
