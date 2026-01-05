<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('almacenes')) {
            return;
        }

        Schema::table('almacenes', function (Blueprint $table) {
            if (!Schema::hasColumn('almacenes', 'empresa_id')) {
                $table->unsignedBigInteger('empresa_id')->nullable()->after('id');
                $table->index('empresa_id', 'almacenes_empresa_id_index');
            }
        });

        // Si ya hay registros, asigna empresa_id=1 a los que estén NULL (ajusta si aplica)
        if (Schema::hasColumn('almacenes', 'empresa_id') && Schema::hasTable('empresas')) {
            DB::table('almacenes')->whereNull('empresa_id')->update(['empresa_id' => 1]);
        }

        // FK (si existe empresas y aún no existe la FK)
        if (Schema::hasTable('empresas')) {
            $fkExists = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'almacenes'
                  AND COLUMN_NAME = 'empresa_id'
                  AND REFERENCED_TABLE_NAME = 'empresas'
                LIMIT 1
            ");

            if (empty($fkExists)) {
                Schema::table('almacenes', function (Blueprint $table) {
                    $table->foreign('empresa_id', 'almacenes_empresa_id_fk')
                        ->references('id')->on('empresas')
                        ->onDelete('set null');
                });
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('almacenes')) {
            return;
        }

        if (Schema::hasColumn('almacenes', 'empresa_id')) {
            Schema::table('almacenes', function (Blueprint $table) {
                try { $table->dropForeign('almacenes_empresa_id_fk'); } catch (\Throwable $e) {}
                try { $table->dropForeign(['empresa_id']); } catch (\Throwable $e) {}
            });

            Schema::table('almacenes', function (Blueprint $table) {
                try { $table->dropIndex('almacenes_empresa_id_index'); } catch (\Throwable $e) {}
                if (Schema::hasColumn('almacenes', 'empresa_id')) {
                    $table->dropColumn('empresa_id');
                }
            });
        }
    }
};
