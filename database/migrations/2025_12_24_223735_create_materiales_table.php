<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('materiales', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 50)->unique();
            $table->string('descripcion', 200);
            $table->foreignId('unidad_id')->constrained('unidades');
            $table->foreignId('clase_construccion_id')->nullable()->constrained('clases_construccion');
            $table->decimal('costo_estandar', 14, 4)->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('materiales');
    }
};
