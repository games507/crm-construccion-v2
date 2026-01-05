<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {

            // dv
            if (!Schema::hasColumn('empresas', 'dv')) {
                $table->string('dv', 10)->nullable()->after('ruc');
            }

            // contacto
            if (!Schema::hasColumn('empresas', 'contacto')) {
                $table->string('contacto', 160)->nullable()->after('dv');
            }

            // telefono
            if (!Schema::hasColumn('empresas', 'telefono')) {
                $table->string('telefono', 60)->nullable()->after('contacto');
            }

            // email
            if (!Schema::hasColumn('empresas', 'email')) {
                $table->string('email', 160)->nullable()->after('telefono');
            }

            // direccion
            if (!Schema::hasColumn('empresas', 'direccion')) {
                $table->string('direccion', 220)->nullable()->after('email');
            }

            // logo_path
            if (!Schema::hasColumn('empresas', 'logo_path')) {
                $table->string('logo_path', 255)->nullable()->after('direccion');
            }

            // activa
            if (!Schema::hasColumn('empresas', 'activa')) {
                $table->boolean('activa')->default(true)->after('logo_path');
            }

            // activo (si lo estás usando en código)
            if (!Schema::hasColumn('empresas', 'activo')) {
                $table->boolean('activo')->default(true)->after('activa');
            }

            // admin_user_id (para asignar usuario admin a la empresa)
            if (!Schema::hasColumn('empresas', 'admin_user_id')) {
                $table->unsignedBigInteger('admin_user_id')->nullable()->after('activo')->index();
                // Si tienes users:
                $table->foreign('admin_user_id')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {

            if (Schema::hasColumn('empresas', 'admin_user_id')) {
                // intenta eliminar FK si existe
                try { $table->dropForeign(['admin_user_id']); } catch (\Throwable $e) {}
                $table->dropColumn('admin_user_id');
            }

            if (Schema::hasColumn('empresas', 'activo')) {
                $table->dropColumn('activo');
            }

            if (Schema::hasColumn('empresas', 'activa')) {
                $table->dropColumn('activa');
            }

            if (Schema::hasColumn('empresas', 'logo_path')) {
                $table->dropColumn('logo_path');
            }

            if (Schema::hasColumn('empresas', 'direccion')) {
                $table->dropColumn('direccion');
            }

            if (Schema::hasColumn('empresas', 'email')) {
                $table->dropColumn('email');
            }

            if (Schema::hasColumn('empresas', 'telefono')) {
                $table->dropColumn('telefono');
            }

            if (Schema::hasColumn('empresas', 'contacto')) {
                $table->dropColumn('contacto');
            }

            if (Schema::hasColumn('empresas', 'dv')) {
                $table->dropColumn('dv');
            }
        });
    }
};
