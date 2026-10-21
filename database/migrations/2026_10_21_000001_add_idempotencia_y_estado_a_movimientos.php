<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Integridad de los movimientos de inventario.
 *
 * - `idempotency_key`: evita que un doble clic, un reintento de red o un
 *   refresh con reenvío registren dos veces la misma operación. La clave la
 *   genera el formulario y es única por operación.
 *
 * - `estado`: marca los movimientos anulados. Un movimiento confirmado no se
 *   borra ni se edita: se anula, y la anulación queda registrada.
 *
 * - `reversa_de_id`: enlaza el movimiento compensatorio con el que anula.
 *
 * - `motivo`, `anulado_por_id`, `anulado_at`: quién anuló, cuándo y por qué.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->string('idempotency_key', 64)
                ->nullable()
                ->unique()
                ->after('id');

            $table->string('estado', 20)
                ->default('CONFIRMADO')
                ->after('tipo');

            $table->string('motivo', 150)
                ->nullable()
                ->after('referencia');

            $table->foreignId('anulado_por_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->after('usuario_id');

            $table->timestamp('anulado_at')
                ->nullable()
                ->after('anulado_por_id');

            $table->foreignId('reversa_de_id')
                ->nullable()
                ->constrained('movimientos')
                ->nullOnDelete()
                ->after('anulado_at');

            $table->index(['estado', 'fecha_movimiento']);
        });
    }

    public function down(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->dropIndex(['estado', 'fecha_movimiento']);

            $table->dropForeign(['anulado_por_id']);
            $table->dropForeign(['reversa_de_id']);
            $table->dropUnique('movimientos_idempotency_key_unique');

            $table->dropColumn([
                'idempotency_key',
                'estado',
                'motivo',
                'anulado_por_id',
                'anulado_at',
                'reversa_de_id',
            ]);
        });
    }
};
