<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orden_compra_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('orden_compra_id')->constrained('ordenes_compra')->cascadeOnDelete();
            $table->foreignId('material_id')->nullable()->constrained('materiales')->nullOnDelete();

            $table->string('descripcion', 255);
            $table->decimal('cantidad', 14, 4)->default(1);
            $table->decimal('precio_unitario', 14, 4)->default(0);
            $table->decimal('impuesto', 14, 2)->default(0);
            $table->decimal('descuento', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);

            $table->timestamps();

            $table->index(['orden_compra_id', 'material_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orden_compra_items');
    }
};