<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    protected $table = 'almacenes';

    protected $fillable = [
        'empresa_id',
        'codigo',
        'nombre',
        'ubicacion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}
