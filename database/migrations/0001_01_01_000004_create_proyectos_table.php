<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->string('nombre', 160);
            $table->string('ubicacion', 220)->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->string('estado', 30)->default('activo'); // activo, pausado, cerrado
            $table->decimal('presupuesto', 14, 2)->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['empresa_id','estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyectos');
    }
};
