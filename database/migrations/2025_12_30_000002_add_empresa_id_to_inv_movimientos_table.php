<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('inv_movimientos')) {
            return;
        }

        Schema::table('inv_movimientos', function (Blueprint $table) {
            if (!Schema::hasColumn('inv_movimientos', 'empresa_id')) {
                $table->unsignedBigInteger('empresa_id')->nullable()->after('id');
                $table->index('empresa_id', 'inv_movimientos_empresa_id_index');
            }
        });

        // Si ya hay registros, asigna empresa_id=1 a los que estén NULL (ajusta si aplica)
        if (Schema::hasColumn('inv_movimientos', 'empresa_id') && Schema::hasTable('empresas')) {
            DB::table('inv_movimientos')->whereNull('empresa_id')->update(['empresa_id' => 1]);
        }

        // Agrega FK si existe tabla empresas (y si no existe ya)
        if (Schema::hasTable('empresas')) {
            $fkExists = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'inv_movimientos'
                  AND COLUMN_NAME = 'empresa_id'
                  AND REFERENCED_TABLE_NAME = 'empresas'
                LIMIT 1
            ");

            if (empty($fkExists)) {
                Schema::table('inv_movimientos', function (Blueprint $table) {
                    $table->foreign('empresa_id', 'inv_movimientos_empresa_id_fk')
                        ->references('id')->on('empresas')
                        ->onDelete('set null');
                });
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('inv_movimientos')) {
            return;
        }

        if (Schema::hasColumn('inv_movimientos', 'empresa_id')) {
            Schema::table('inv_movimientos', function (Blueprint $table) {
                try { $table->dropForeign('inv_movimientos_empresa_id_fk'); } catch (\Throwable $e) {}
                try { $table->dropForeign(['empresa_id']); } catch (\Throwable $e) {}
            });

            Schema::table('inv_movimientos', function (Blueprint $table) {
                try { $table->dropIndex('inv_movimientos_empresa_id_index'); } catch (\Throwable $e) {}
                if (Schema::hasColumn('inv_movimientos', 'empresa_id')) {
                    $table->dropColumn('empresa_id');
                }
            });
        }
    }
};
