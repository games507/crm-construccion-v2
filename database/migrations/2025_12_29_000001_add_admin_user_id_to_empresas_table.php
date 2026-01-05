<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            if (!Schema::hasColumn('empresas', 'admin_user_id')) {
                $table->unsignedBigInteger('admin_user_id')->nullable()->after('email');

                $table->foreign('admin_user_id')
                    ->references('id')->on('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            if (Schema::hasColumn('empresas', 'admin_user_id')) {
                $table->dropForeign(['admin_user_id']);
                $table->dropColumn('admin_user_id');
            }
        });
    }
};
