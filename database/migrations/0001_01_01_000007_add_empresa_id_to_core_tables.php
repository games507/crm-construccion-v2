<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        $tables = [
            'almacenes',
            'materiales',
            'inv_existencias',
            'inv_movimientos',
            'proyectos',  // si ya existe
        ];

        foreach ($tables as $t) {
            if (Schema::hasTable($t) && !Schema::hasColumn($t, 'empresa_id')) {
                Schema::table($t, function (Blueprint $table) {
                    $table->unsignedBigInteger('empresa_id')->nullable()->index();
                });
            }
        }
    }

    public function down(): void
    {
        $tables = ['almacenes','materiales','inv_existencias','inv_movimientos','proyectos'];

        foreach ($tables as $t) {
            if (Schema::hasTable($t) && Schema::hasColumn($t, 'empresa_id')) {
                Schema::table($t, function (Blueprint $table) {
                    $table->dropColumn('empresa_id');
                });
            }
        }
    }
};
