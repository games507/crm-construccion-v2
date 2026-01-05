<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inv_existencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('materiales');
            $table->foreignId('almacen_id')->constrained('almacenes');
            $table->decimal('stock', 14, 4)->default(0);
            $table->decimal('costo_promedio', 14, 4)->default(0);
            $table->timestamps();

            $table->unique(['material_id', 'almacen_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('inv_existencias');
    }
};
