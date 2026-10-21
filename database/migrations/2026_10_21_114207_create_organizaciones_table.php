<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('slug', 100)->unique();
            $table->string('identificador_tributario', 50)->nullable();
            $table->string('identificador_tipo', 20)->nullable();
            $table->string('perfil', 20)->default('GENERAL');
            $table->json('configuracion')->nullable();
            $table->string('plan')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizaciones');
    }
};
