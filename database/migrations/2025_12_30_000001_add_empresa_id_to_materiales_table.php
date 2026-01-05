<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('materiales')) {
            return;
        }

        Schema::table('materiales', function (Blueprint $table) {
            if (!Schema::hasColumn('materiales', 'empresa_id')) {
                $table->unsignedBigInteger('empresa_id')->nullable()->after('id');
                $table->index('empresa_id', 'materiales_empresa_id_index');
            }
        });

        // Si ya hay registros, asigna empresa_id=1 a los que estén NULL (ajusta si aplica)
        if (Schema::hasColumn('materiales', 'empresa_id') && Schema::hasTable('empresas')) {
            DB::table('materiales')->whereNull('empresa_id')->update(['empresa_id' => 1]);
        }

        // Agrega FK si existe tabla empresas (y si no existe ya)
        if (Schema::hasTable('empresas')) {
            // Evita error si ya existe FK
            $fkExists = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'materiales'
                  AND COLUMN_NAME = 'empresa_id'
                  AND REFERENCED_TABLE_NAME = 'empresas'
                LIMIT 1
            ");

            if (empty($fkExists)) {
                Schema::table('materiales', function (Blueprint $table) {
                    $table->foreign('empresa_id', 'materiales_empresa_id_fk')
                        ->references('id')->on('empresas')
                        ->onDelete('set null');
                });
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('materiales')) {
            return;
        }

        // Quita FK si existe
        if (Schema::hasColumn('materiales', 'empresa_id')) {
            Schema::table('materiales', function (Blueprint $table) {
                // intenta ambos nombres por si MySQL generó otro
                try { $table->dropForeign('materiales_empresa_id_fk'); } catch (\Throwable $e) {}
                try { $table->dropForeign(['empresa_id']); } catch (\Throwable $e) {}
            });

            Schema::table('materiales', function (Blueprint $table) {
                try { $table->dropIndex('materiales_empresa_id_index'); } catch (\Throwable $e) {}
                if (Schema::hasColumn('materiales', 'empresa_id')) {
                    $table->dropColumn('empresa_id');
                }
            });
        }
    }
};
