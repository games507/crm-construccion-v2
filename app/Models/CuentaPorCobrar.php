<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaPorCobrar extends Model
{
    protected $table = 'cuentas_por_cobrar';

    protected $fillable = [
        'proyecto_id',
        'cliente',
        'descripcion',
        'monto_total',
        'monto_cobrado',
        'saldo',
        'fecha',
        'fecha_vencimiento',
        'estado',
        'user_id',
    ];

    protected $casts = [
        'monto_total' => 'decimal:2',
        'monto_cobrado' => 'decimal:2',
        'saldo' => 'decimal:2',
        'fecha' => 'date',
        'fecha_vencimiento' => 'date',
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function cobros()
    {
        return $this->hasMany(CuentaCobro::class, 'cuenta_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}