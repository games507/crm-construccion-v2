<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            // Si tu tabla ya tiene algunas columnas, esto no las borra.
            // Solo agrega las que falten.
            if (!Schema::hasColumn('empresas','ruc')) {
                $table->string('ruc', 40)->nullable()->after('nombre');
            }
            if (!Schema::hasColumn('empresas','telefono')) {
                $table->string('telefono', 30)->nullable()->after('ruc');
            }
            if (!Schema::hasColumn('empresas','email')) {
                $table->string('email', 150)->nullable()->after('telefono');
            }
            if (!Schema::hasColumn('empresas','direccion')) {
                $table->string('direccion', 220)->nullable()->after('email');
            }
            if (!Schema::hasColumn('empresas','activa')) {
                $table->boolean('activa')->default(true)->after('direccion');
            }

            // IMPORTANTE: no usamos "dv" para evitar tu error.
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            foreach (['ruc','telefono','email','direccion','activa'] as $c) {
                if (Schema::hasColumn('empresas', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
