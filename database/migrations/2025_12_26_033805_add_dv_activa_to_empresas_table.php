<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
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
        Schema::table('empresas', function (Blueprint $table) {
            if (Schema::hasColumn('empresas', 'dv')) $table->dropColumn('dv');
            if (Schema::hasColumn('empresas', 'activa')) $table->dropColumn('activa');
        });
    }
};
