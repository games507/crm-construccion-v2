<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvMovimiento extends Model
{
    protected $table = 'inv_movimientos';

    protected $fillable = [
        'empresa_id',
        'fecha',
        'tipo',
        'material_id',
        'almacen_origen_id',
        'almacen_destino_id',
        'cantidad',
        'costo_unitario',
        'referencia',
    ];

    protected $casts = [
        'fecha' => 'date',
        'cantidad' => 'decimal:4',
        'costo_unitario' => 'decimal:4',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function almacenOrigen()
    {
        return $this->belongsTo(Almacen::class, 'almacen_origen_id');
    }

    public function almacenDestino()
    {
        return $this->belongsTo(Almacen::class, 'almacen_destino_id');
    }
}
