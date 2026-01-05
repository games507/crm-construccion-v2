<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('inv_existencias')) return;

        // 1) Agregar empresa_id si no existe
        Schema::table('inv_existencias', function (Blueprint $table) {
            if (!Schema::hasColumn('inv_existencias', 'empresa_id')) {
                $table->unsignedBigInteger('empresa_id')->nullable()->after('id');
            }
        });

        // 2) (Opcional) rellenar empresa_id en base al almacén si almacenes tiene empresa_id
        //    Si tu tabla inv_almacenes tiene empresa_id, esto llena automático.
        try {
            DB::statement("
                UPDATE inv_existencias e
                JOIN inv_almacenes a ON a.id = e.almacen_id
                SET e.empresa_id = a.empresa_id
                WHERE e.empresa_id IS NULL
            ");
        } catch (\Throwable $e) {
            // Si no existe inv_almacenes o no tiene empresa_id, ignoramos.
        }

        // 3) Crear índice UNIQUE nuevo: empresa_id + material_id + almacen_id
        //    Antes, borra si ya existe algún índice con el mismo nombre.
        $indexes = DB::select("SHOW INDEX FROM inv_existencias");
        $indexNames = array_unique(array_map(fn($i) => $i->Key_name, $indexes));

        if (!in_array('inv_existencias_empresa_material_almacen_unique', $indexNames, true)) {
            Schema::table('inv_existencias', function (Blueprint $table) {
                $table->unique(['empresa_id','material_id','almacen_id'], 'inv_existencias_empresa_material_almacen_unique');
            });
        }

        // 4) Ya NO intentamos dropear el UNIQUE anterior porque te está rompiendo por FK.
        //    Ese UNIQUE puede quedar (no molesta), pero si quieres quitarlo, hay que identificar
        //    exactamente qué FK lo necesita primero.
    }

    public function down(): void
    {
        if (!Schema::hasTable('inv_existencias')) return;

        Schema::table('inv_existencias', function (Blueprint $table) {
            if (Schema::hasColumn('inv_existencias', 'empresa_id')) {
                // primero bajar índice nuevo si existe
                $table->dropUnique('inv_existencias_empresa_material_almacen_unique');
                $table->dropColumn('empresa_id');
            }
        });
    }
};
