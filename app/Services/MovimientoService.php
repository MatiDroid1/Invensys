<?php

namespace App\Services;

use App\AuditoriaService;
use App\Models\Articulo;
use App\Models\Movimiento;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MovimientoService
{
    public function __construct(
        private AuditoriaService $auditoriaService
    ) {}

    public function registrarEntrada(
        int $articuloId,
        float $cantidad,
        string $fechaMovimiento,
        ?string $referencia,
        ?string $observaciones,
        int $usuarioId
    ): Movimiento {
        return DB::transaction(function () use (
            $articuloId,
            $cantidad,
            $fechaMovimiento,
            $referencia,
            $observaciones,
            $usuarioId
        ) {
            Articulo::whereKey($articuloId)
                ->lockForUpdate()
                ->firstOrFail();

            $movimiento = Movimiento::create([
                'articulo_id' => $articuloId,
                'tipo' => 'ENTRADA',
                'cantidad' => $cantidad,
                'fecha_movimiento' => $fechaMovimiento,
                'persona_id' => null,
                'usuario_id' => $usuarioId,
                'referencia' => $referencia,
                'observaciones' => $observaciones,
            ]);

            $this->auditoriaService->registrar(
                modulo: 'movimientos',
                accion: 'ENTRADA',
                modelo: $movimiento,
                descripcion: 'Entrada de inventario registrada.',
                datos: [
                    'articulo_id' => $articuloId,
                    'cantidad' => $cantidad,
                    'referencia' => $referencia,
                ],
                usuarioId: $usuarioId,
            );

            return $movimiento;
        });
    }

    public function registrarSalida(
        int $articuloId,
        int $personaId,
        float $cantidad,
        string $fechaMovimiento,
        ?string $referencia,
        ?string $observaciones,
        int $usuarioId
    ): Movimiento {
        return DB::transaction(function () use (
            $articuloId,
            $personaId,
            $cantidad,
            $fechaMovimiento,
            $referencia,
            $observaciones,
            $usuarioId
        ) {
            $articulo = Articulo::whereKey($articuloId)
                ->lockForUpdate()
                ->firstOrFail();

            $stockActual = $articulo->movimientos()
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

            if ($cantidad > (float) $stockActual) {
                throw ValidationException::withMessages([
                    'cantidad' => "Stock insuficiente. Stock disponible: {$stockActual}.",
                ]);
            }

            $movimiento = Movimiento::create([
                'articulo_id' => $articuloId,
                'tipo' => 'SALIDA',
                'cantidad' => $cantidad,
                'fecha_movimiento' => $fechaMovimiento,
                'persona_id' => $personaId,
                'usuario_id' => $usuarioId,
                'referencia' => $referencia,
                'observaciones' => $observaciones,
            ]);

            $this->auditoriaService->registrar(
                modulo: 'movimientos',
                accion: 'SALIDA',
                modelo: $movimiento,
                descripcion: 'Salida de inventario registrada.',
                datos: [
                    'articulo_id' => $articuloId,
                    'cantidad' => $cantidad,
                    'persona_id' => $personaId,
                    'referencia' => $referencia,
                ],
                usuarioId: $usuarioId,
            );

            return $movimiento;
        });
    }

    public function registrarAjuste(
        int $articuloId,
        string $tipo,
        float $cantidad,
        string $fechaMovimiento,
        ?string $referencia,
        ?string $observaciones,
        int $usuarioId
    ): Movimiento {
        return DB::transaction(function () use (
            $articuloId,
            $tipo,
            $cantidad,
            $fechaMovimiento,
            $referencia,
            $observaciones,
            $usuarioId
        ) {
            $articulo = Articulo::whereKey($articuloId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($tipo === 'AJUSTE_NEGATIVO') {
                $stockActual = $articulo->movimientos()
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

                if ($cantidad > (float) $stockActual) {
                    throw ValidationException::withMessages([
                        'cantidad' => "El ajuste negativo no puede superar el stock disponible. Stock actual: {$stockActual}.",
                    ]);
                }
            }

            $movimiento = Movimiento::create([
                'articulo_id' => $articuloId,
                'tipo' => $tipo,
                'cantidad' => $cantidad,
                'fecha_movimiento' => $fechaMovimiento,
                'persona_id' => null,
                'usuario_id' => $usuarioId,
                'referencia' => $referencia,
                'observaciones' => $observaciones,
            ]);

            $this->auditoriaService->registrar(
                modulo: 'movimientos',
                accion: $tipo,
                modelo: $movimiento,
                descripcion: 'Ajuste de inventario registrado.',
                datos: [
                    'articulo_id' => $articuloId,
                    'tipo' => $tipo,
                    'cantidad' => $cantidad,
                    'referencia' => $referencia,
                ],
                usuarioId: $usuarioId,
            );

            return $movimiento;
        });
    }
}
