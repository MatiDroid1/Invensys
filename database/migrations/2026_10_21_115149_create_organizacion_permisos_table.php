<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizacion_permisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->constrained('organizaciones')->cascadeOnDelete();
            $table->string('rol', 30);
            $table->string('permiso', 80);
            $table->boolean('permitido')->default(true);
            $table->timestamps();

            $table->unique(['organizacion_id', 'rol', 'permiso']);
            $table->index(['organizacion_id', 'rol']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizacion_permisos');
    }
};
