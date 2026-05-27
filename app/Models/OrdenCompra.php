<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrdenCompra extends Model
{
    protected $table = 'ordenes_compra';

    protected $fillable = [
        'empresa_id',
        'proyecto_id',
        'proveedor_id',
        'almacen_destino_id',
        'numero',
        'fecha',
        'fecha_entrega',
        'estado',
        'subtotal',
        'impuesto',
        'descuento',
        'total',
        'observacion',
        'creado_por',
        'aprobado_por',
        'aprobado_en',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_entrega' => 'date',
        'aprobado_en' => 'datetime',
        'subtotal' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function almacenDestino()
    {
        return $this->belongsTo(Almacen::class, 'almacen_destino_id');
    }

    public function items()
    {
        return $this->hasMany(OrdenCompraItem::class, 'orden_compra_id');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    public function recalcularTotales(): void
    {
        $subtotal = $this->items()->sum(DB::raw('(cantidad * precio_unitario)'));
        $impuesto = $this->items()->sum('impuesto');
        $descuento = $this->items()->sum('descuento');

        $this->subtotal = $subtotal;
        $this->impuesto = $impuesto;
        $this->descuento = $descuento;
        $this->total = ($subtotal + $impuesto) - $descuento;
        $this->save();
    }
}