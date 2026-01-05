<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Si ya existe, no vuelvas a crearla
        if (Schema::hasTable('proyectos')) {
            return;
        }

        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('empresa_id')->index();

            $table->string('codigo', 40)->nullable();
            $table->string('nombre', 160);
            $table->string('ubicacion', 220)->nullable();

            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();

            $table->string('estado', 30)->default('activo');

            $table->decimal('presupuesto', 14, 2)->default(0);

            $table->boolean('activo')->default(true);

            $table->timestamps();

            // FK opcional (si ya tienes empresas)
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // ✅ Si no existe, no hagas nada
        if (!Schema::hasTable('proyectos')) {
            return;
        }

        Schema::dropIfExists('proyectos');
    }
};
