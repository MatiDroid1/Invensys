<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Módulo de costos: el costo unitario se congela en cada movimiento y el
     * costo promedio ponderado se recalcula solo con entradas y ajustes
     * positivos. Todos los campos son opcionales: un cliente puede operar sin
     * valorizar inventario y activarlo cuando quiera cargando los costos en las
     * entradas.
     */
    public function up(): void
    {
        Schema::table('articulos', function (Blueprint $table) {
            $table->decimal('costo_promedio', 12, 4)->nullable()->after('stock_minimo');
            $table->decimal('precio_venta', 12, 2)->nullable()->after('costo_promedio');
        });

        Schema::table('movimientos', function (Blueprint $table) {
            $table->decimal('costo_unitario', 12, 4)->nullable()->after('cantidad');
        });
    }

    public function down(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->dropColumn('costo_unitario');
        });

        Schema::table('articulos', function (Blueprint $table) {
            $table->dropColumn(['costo_promedio', 'precio_venta']);
        });
    }
};
