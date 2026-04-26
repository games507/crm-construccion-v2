<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model
{
    protected $table = 'ingresos';

    protected $fillable = [
        'empresa_id',
        'proyecto_id',
        'descripcion',
        'monto',
        'fecha',
        'user_id'
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }
}