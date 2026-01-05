<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('clases_construccion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 80)->unique(); // Concreto, Acero, Eléctrico...
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('clases_construccion');
    }
};
