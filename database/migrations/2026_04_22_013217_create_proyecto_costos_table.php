<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyecto_costos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('proyecto_id');

            $table->string('tipo', 80);
            $table->string('categoria', 120)->nullable();
            $table->text('descripcion')->nullable();

            $table->decimal('monto', 14, 2)->default(0);

            $table->date('fecha')->nullable();

            $table->string('proveedor', 180)->nullable();

            $table->boolean('requiere_pago')->default(false);

            $table->enum('estado_pago', ['pendiente', 'parcial', 'pagado'])
                ->default('pendiente');

            $table->timestamps();

            $table->foreign('proyecto_id')
                ->references('id')
                ->on('proyectos')
                ->onDelete('cascade');

            $table->index(['proyecto_id']);
            $table->index(['tipo']);
            $table->index(['estado_pago']);
            $table->index(['fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyecto_costos');
    }
};