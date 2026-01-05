<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('materiales', function (Blueprint $table) {
            $table->string('codigo', 50)->after('id');
            $table->index(['empresa_id', 'codigo']);
        });
    }

    public function down(): void
    {
        Schema::table('materiales', function (Blueprint $table) {
            $table->dropIndex(['empresa_id', 'codigo']);
            $table->dropColumn('codigo');
        });
    }
};
