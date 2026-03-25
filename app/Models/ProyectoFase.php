<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProyectoFase extends Model
{
    protected $fillable = [
        'proyecto_id',
        'nombre',
        'orden',
        'porcentaje',
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }
}