<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->nullOnDelete();

            $table->string('codigo', 30)->nullable();
            $table->string('nombre', 160);
            $table->string('ruc', 50)->nullable();
            $table->string('dv', 10)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('email', 120)->nullable();
            $table->string('contacto', 120)->nullable();
            $table->string('direccion', 255)->nullable();

            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['empresa_id', 'activo']);
            $table->unique(['empresa_id', 'codigo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};