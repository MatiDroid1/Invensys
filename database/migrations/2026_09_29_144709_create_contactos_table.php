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
        Schema::create('contactos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('usuario_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('nombre', 150);

            $table->string('email', 150);

            $table->string('asunto', 150);

            $table->text('mensaje');

            $table->enum('estado', [
                'PENDIENTE',
                'ATENDIDO',
            ])->default('PENDIENTE');

            $table->timestamps();

            $table->index(['estado', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contactos');
    }
};
