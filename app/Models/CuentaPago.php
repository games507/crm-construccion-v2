<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaPago extends Model
{
    protected $table = 'cuenta_pagos';

    protected $fillable = [
        'cuenta_id',
        'monto',
        'fecha',
        'observacion',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha' => 'date',
    ];

    public function cuenta()
    {
        return $this->belongsTo(CuentaPorPagar::class, 'cuenta_id');
    }
}