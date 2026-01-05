<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inv_existencias', function (Blueprint $table) {
            if (!Schema::hasColumn('inv_existencias','empresa_id')) {
                $table->unsignedBigInteger('empresa_id')->nullable()->after('id')->index();
            }

            if (!Schema::hasColumn('inv_existencias','cantidad')) {
                $table->decimal('cantidad', 14, 4)->default(0)->after('material_id');
            }

            // índice único recomendado (empresa + almacén + material)
            // OJO: solo si no existe ya. Si te da error, me dices el nombre del índice y lo ajustamos.
            // $table->unique(['empresa_id','almacen_id','material_id'], 'inv_existencias_emp_alm_mat_unique');
        });
    }

    public function down(): void
    {
        Schema::table('inv_existencias', function (Blueprint $table) {
            if (Schema::hasColumn('inv_existencias','empresa_id')) $table->dropColumn('empresa_id');
            if (Schema::hasColumn('inv_existencias','cantidad')) $table->dropColumn('cantidad');
            // $table->dropUnique('inv_existencias_emp_alm_mat_unique');
        });
    }
};
