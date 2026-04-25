<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaPorPagar extends Model
{
    protected $table = 'cuentas_por_pagar';

    protected $fillable = [
        'proyecto_id',
        'proveedor',
        'descripcion',
        'monto_total',
        'monto_pagado',
        'saldo',
        'fecha',
        'fecha_vencimiento',
        'estado',
        'origen_tipo',
        'origen_id',
    ];

    protected $casts = [
        'monto_total'       => 'decimal:2',
        'monto_pagado'      => 'decimal:2',
        'saldo'             => 'decimal:2',
        'fecha'             => 'date',
        'fecha_vencimiento' => 'date',
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function pagos()
    {
        return $this->hasMany(CuentaPago::class, 'cuenta_id')
            ->latest('fecha')
            ->latest('id');
    }
}