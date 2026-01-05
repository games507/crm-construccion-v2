<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('unidades', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)->unique();   // UND, KG, M3...
            $table->string('descripcion', 80);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('unidades');
    }
};
