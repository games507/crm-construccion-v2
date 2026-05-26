<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaCobro extends Model
{
    protected $table = 'cuenta_cobros';

    protected $fillable = [
        'cuenta_id',
        'user_id',
        'monto',
        'fecha',
        'observacion',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha' => 'datetime',
    ];

    public function cuenta()
    {
        return $this->belongsTo(CuentaPorCobrar::class, 'cuenta_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}