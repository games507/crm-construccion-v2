<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;



    /**
     * Run the migrations.
     */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->decimal('porcentaje', 5, 2)->default(0)->after('presupuesto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->dropColumn('porcentaje');
        });
    }
};