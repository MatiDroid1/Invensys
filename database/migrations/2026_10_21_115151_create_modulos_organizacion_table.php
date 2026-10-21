<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modulos_organizacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->constrained('organizaciones')->cascadeOnDelete();
            $table->string('modulo', 80);
            $table->boolean('habilitado')->default(true);
            $table->json('configuracion')->nullable();
            $table->timestamps();

            $table->unique(['organizacion_id', 'modulo']);
            $table->index(['organizacion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modulos_organizacion');
    }
};
