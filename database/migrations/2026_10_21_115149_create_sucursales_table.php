<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sucursales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->constrained('organizaciones')->cascadeOnDelete();
            $table->string('nombre', 150);
            $table->string('direccion', 255)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['organizacion_id', 'nombre']);
            $table->index(['organizacion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sucursales');
    }
};
