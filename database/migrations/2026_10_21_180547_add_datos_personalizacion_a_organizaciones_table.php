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
        Schema::table('organizaciones', function (Blueprint $table) {
            if (! Schema::hasColumn('organizaciones', 'razon_social')) {
                $table->string('razon_social')->nullable()->after('slug');
            }
            if (! Schema::hasColumn('organizaciones', 'rut')) {
                $table->string('rut')->nullable()->after('razon_social');
            }
            if (! Schema::hasColumn('organizaciones', 'identificador_tributario')) {
                $table->string('identificador_tributario')->nullable()->after('rut');
            }
            if (! Schema::hasColumn('organizaciones', 'identificador_tipo')) {
                $table->string('identificador_tipo')->nullable()->after('identificador_tributario');
            }
            if (! Schema::hasColumn('organizaciones', 'direccion')) {
                $table->string('direccion')->nullable()->after('identificador_tipo');
            }
            if (! Schema::hasColumn('organizaciones', 'telefono')) {
                $table->string('telefono')->nullable()->after('direccion');
            }
            if (! Schema::hasColumn('organizaciones', 'email')) {
                $table->string('email')->nullable()->after('telefono');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizaciones', function (Blueprint $table) {
            $columnas = ['razon_social', 'rut', 'identificador_tributario', 'identificador_tipo', 'direccion', 'telefono', 'email'];
            foreach ($columnas as $columna) {
                if (Schema::hasColumn('organizaciones', $columna)) {
                    $table->dropColumn($columna);
                }
            }
        });
    }
};
