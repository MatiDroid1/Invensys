<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('articulo_id')
                ->constrained('articulos')
                ->restrictOnDelete();

            $table->enum('tipo', [
                'ENTRADA',
                'SALIDA',
                'AJUSTE_POSITIVO',
                'AJUSTE_NEGATIVO',
            ]);

            $table->decimal('cantidad', 12, 2);

            $table->dateTime('fecha_movimiento');

            $table->foreignId('persona_id')
                ->nullable()
                ->constrained('personas')
                ->restrictOnDelete();

            $table->foreignId('usuario_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('referencia', 100)
                ->nullable();

            $table->text('observaciones')
                ->nullable();

            $table->timestamps();

            $table->index(['articulo_id', 'fecha_movimiento']);
            $table->index(['tipo', 'fecha_movimiento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
