<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inv_movimientos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->enum('tipo', ['entrada', 'salida', 'traslado', 'ajuste']);
            $table->foreignId('material_id')->constrained('materiales');

            // Para traslado: origen y destino. Para entrada: solo destino. Para salida: solo origen.
            $table->foreignId('almacen_origen_id')->nullable()->constrained('almacenes');
            $table->foreignId('almacen_destino_id')->nullable()->constrained('almacenes');

            $table->decimal('cantidad', 14, 4);
            $table->decimal('costo_unitario', 14, 4)->nullable(); // para entradas/ajustes con costo
            $table->string('referencia', 80)->nullable(); // OC-0001, TR-0003, etc.
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['material_id', 'fecha']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('inv_movimientos');
    }
};
