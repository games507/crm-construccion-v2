<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            if (!Schema::hasColumn('proyectos', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('nombre');
            }

            if (!Schema::hasColumn('proyectos', 'responsable_id')) {
                $table->unsignedBigInteger('responsable_id')->nullable()->after('empresa_id');
                $table->foreign('responsable_id')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            if (Schema::hasColumn('proyectos', 'responsable_id')) {
                $table->dropForeign(['responsable_id']);
                $table->dropColumn('responsable_id');
            }

            if (Schema::hasColumn('proyectos', 'descripcion')) {
                $table->dropColumn('descripcion');
            }
        });
    }
};