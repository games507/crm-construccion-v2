<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            // Agrega código solo si no existe
            if (!Schema::hasColumn('proyectos', 'codigo')) {
                $table->string('codigo', 40)->nullable()->after('empresa_id');
                $table->index(['empresa_id', 'codigo']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            if (Schema::hasColumn('proyectos', 'codigo')) {
                $table->dropIndex(['empresa_id', 'codigo']);
                $table->dropColumn('codigo');
            }
        });
    }
};
