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
      Schema::create('proyecto_fases', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('proyecto_id');
    $table->string('nombre', 150);
    $table->integer('orden')->default(0);
    $table->decimal('porcentaje', 5,2)->default(0);
    $table->timestamps();

    $table->foreign('proyecto_id')
        ->references('id')
        ->on('proyectos')
        ->cascadeOnDelete();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyecto_fases');
    }
};
