<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    // Si tu tabla se llama diferente, cámbiala aquí:
    protected $table = 'unidades';

    protected $fillable = [
        'codigo',
        'descripcion',
        'activo',
        'empresa_id',
    ];
}
