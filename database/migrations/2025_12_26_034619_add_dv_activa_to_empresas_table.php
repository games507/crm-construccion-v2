<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Si la tabla no existe, no hacemos nada
        if (!Schema::hasTable('empresas')) {
            return;
        }

        Schema::table('empresas', function (Blueprint $table) {
            // ✅ Solo agrega si no existe
            if (!Schema::hasColumn('empresas', 'dv')) {
                $table->string('dv', 10)->nullable()->after('ruc');
            }

            if (!Schema::hasColumn('empresas', 'activa')) {
                $table->boolean('activa')->default(true)->after('direccion');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('empresas')) {
            return;
        }

        Schema::table('empresas', function (Blueprint $table) {
            // ✅ Solo elimina si existe
            if (Schema::hasColumn('empresas', 'dv')) {
                $table->dropColumn('dv');
            }

            if (Schema::hasColumn('empresas', 'activa')) {
                $table->dropColumn('activa');
            }
        });
    }
};
