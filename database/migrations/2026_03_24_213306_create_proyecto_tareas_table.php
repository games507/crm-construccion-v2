<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('proyecto_tareas', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('proyecto_id');
    $table->unsignedBigInteger('fase_id')->nullable();
    $table->unsignedBigInteger('responsable_id')->nullable();
    $table->string('nombre', 180);
    $table->text('descripcion')->nullable();
    $table->string('estado', 30)->default('pendiente');
    $table->date('fecha_inicio')->nullable();
    $table->date('fecha_fin')->nullable();
    $table->decimal('porcentaje', 5, 2)->default(0);
    $table->timestamps();

    $table->foreign('proyecto_id')
        ->references('id')
        ->on('proyectos')
        ->cascadeOnDelete();

    $table->foreign('fase_id')
        ->references('id')
        ->on('proyecto_fases')
        ->nullOnDelete();

    $table->foreign('responsable_id')
        ->references('id')
        ->on('users')
        ->nullOnDelete();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyecto_tareas');
    }
};
