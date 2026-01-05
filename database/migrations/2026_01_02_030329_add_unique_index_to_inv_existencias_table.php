<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('inv_existencias', function (Blueprint $table) {
      // Ajusta el nombre si ya tienes uno parecido
      $table->unique(['empresa_id','almacen_id','material_id'], 'inv_existencias_emp_alm_mat_unique');
    });
  }

  public function down(): void
  {
    Schema::table('inv_existencias', function (Blueprint $table) {
      $table->dropUnique('inv_existencias_emp_alm_mat_unique');
    });
  }
};
