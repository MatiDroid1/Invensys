<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->constrained('organizaciones')->cascadeOnDelete();
            $table->string('nombre', 150);
            $table->string('identificador_tributario', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['organizacion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
