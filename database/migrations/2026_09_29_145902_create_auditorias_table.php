<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('usuario_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('modulo', 50);

            $table->string('accion', 50);

            $table->string('modelo', 100)->nullable();

            $table->unsignedBigInteger('modelo_id')->nullable();

            $table->text('descripcion')->nullable();

            $table->json('datos')->nullable();

            $table->timestamps();

            $table->index(['modulo', 'accion']);
            $table->index(['modelo', 'modelo_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditorias');
    }
};
