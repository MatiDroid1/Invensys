<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tablas = [
        'categorias',
        'unidad_medidas',
        'articulos',
        'personas',
        'movimientos',
        'contactos',
        'auditorias',
        'conversaciones',
        'mensajes',
    ];

    public function up(): void
    {
        foreach ($this->tablas as $tabla) {
            if (! Schema::hasTable($tabla)) {
                continue;
            }

            Schema::table($tabla, function (Blueprint $table) use ($tabla) {
                if (Schema::hasColumn($tabla, 'organizacion_id')) {
                    return;
                }

                $table->foreignId('organizacion_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('organizaciones')
                    ->nullOnDelete();

                $table->index(['organizacion_id']);
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tablas as $tabla) {
            if (! Schema::hasTable($tabla)) {
                continue;
            }

            Schema::table($tabla, function (Blueprint $table) use ($tabla) {
                if (! Schema::hasColumn($tabla, 'organizacion_id')) {
                    return;
                }

                $table->dropForeign(['organizacion_id']);
                $table->dropIndex(['organizacion_id']);
                $table->dropColumn('organizacion_id');
            });
        }
    }
};
