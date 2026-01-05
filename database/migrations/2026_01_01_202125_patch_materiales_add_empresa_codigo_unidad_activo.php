<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('materiales', function (Blueprint $table) {

            if (!Schema::hasColumn('materiales', 'empresa_id')) {
                $table->unsignedBigInteger('empresa_id')->nullable()->after('id')->index();
            }

            if (!Schema::hasColumn('materiales', 'codigo')) {
                $table->string('codigo', 60)->nullable()->after('empresa_id');
            }

            if (!Schema::hasColumn('materiales', 'descripcion')) {
                $table->string('descripcion', 220)->nullable()->after('codigo');
            }

            if (!Schema::hasColumn('materiales', 'unidad')) {
                $table->string('unidad', 30)->nullable()->after('descripcion');
            }

            if (!Schema::hasColumn('materiales', 'activo')) {
                $table->boolean('activo')->default(true)->after('unidad');
            }
        });

        // Indices únicos por empresa (si ya existen, se intentan crear y si falla no rompe)
        Schema::table('materiales', function (Blueprint $table) {
            try {
                if (Schema::hasColumn('materiales', 'empresa_id') && Schema::hasColumn('materiales', 'codigo')) {
                    $table->unique(['empresa_id', 'codigo'], 'materiales_empresa_codigo_unique');
                }
            } catch (\Throwable $e) {}

            try {
                if (Schema::hasColumn('materiales', 'empresa_id') && Schema::hasColumn('materiales', 'descripcion')) {
                    $table->unique(['empresa_id', 'descripcion'], 'materiales_empresa_descripcion_unique');
                }
            } catch (\Throwable $e) {}
        });
    }

    public function down(): void
    {
        Schema::table('materiales', function (Blueprint $table) {
            try { $table->dropUnique('materiales_empresa_codigo_unique'); } catch (\Throwable $e) {}
            try { $table->dropUnique('materiales_empresa_descripcion_unique'); } catch (\Throwable $e) {}

            if (Schema::hasColumn('materiales', 'activo')) $table->dropColumn('activo');
            if (Schema::hasColumn('materiales', 'unidad')) $table->dropColumn('unidad');
            if (Schema::hasColumn('materiales', 'descripcion')) $table->dropColumn('descripcion');
            if (Schema::hasColumn('materiales', 'codigo')) $table->dropColumn('codigo');
            if (Schema::hasColumn('materiales', 'empresa_id')) $table->dropColumn('empresa_id');
        });
    }
};
