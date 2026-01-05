<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Unidad;

class Material extends Model
{
    protected $table = 'materiales';

    protected $fillable = [
        'codigo',
        'empresa_id',
        'sku',
        'descripcion',
        'unidad',
        'unidad_id',
        'clase_construccion_id',
        'costo_estandar',
        'activo',
    ];
// RELACIÓN: unidad_id -> unidades.id
    public function unidadRef()
    {
        return $this->belongsTo(Unidad::class, 'unidad_id');
    }
}