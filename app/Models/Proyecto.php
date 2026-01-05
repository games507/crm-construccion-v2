<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $table = 'proyectos';

protected $fillable = [
    'empresa_id',
    'codigo',
    'nombre',
    'ubicacion',
    'fecha_inicio',
    'fecha_fin',
    'estado',
    'presupuesto',
    'activo',
];


    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'activo'       => 'boolean',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
}
