<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenCompraItem extends Model
{
    protected $table = 'orden_compra_items';

    protected $fillable = [
        'orden_compra_id',
        'material_id',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'impuesto',
        'descuento',
        'total',
    ];

    protected $casts = [
        'cantidad' => 'decimal:4',
        'precio_unitario' => 'decimal:4',
        'impuesto' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class, 'orden_compra_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    protected static function booted()
    {
        static::saving(function ($item) {
            $subtotal = ((float) $item->cantidad * (float) $item->precio_unitario);
            $item->total = ($subtotal + (float) $item->impuesto) - (float) $item->descuento;
        });
    }
}