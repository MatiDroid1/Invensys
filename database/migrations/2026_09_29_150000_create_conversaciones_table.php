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
        Schema::create('conversaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('usuario_emisor_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('usuario_receptor_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamp('ultimo_mensaje_en')->nullable();

            $table->timestamps();

            $table->index(['usuario_emisor_id', 'usuario_receptor_id']);
            $table->index('ultimo_mensaje_en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversaciones');
    }
};
