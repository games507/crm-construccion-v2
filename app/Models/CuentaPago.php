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
        'user_id', // 🔥 NUEVO
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha' => 'datetime', // 🔥 CAMBIO CLAVE
    ];

    public function cuenta()
    {
        return $this->belongsTo(CuentaPorPagar::class, 'cuenta_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}