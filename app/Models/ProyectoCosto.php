<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProyectoCosto extends Model
{
    protected $table = 'proyecto_costos';

    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_PARCIAL   = 'parcial';
    public const ESTADO_PAGADO    = 'pagado';

    protected $fillable = [
        'proyecto_id',
        'tipo',
        'categoria',
        'descripcion',
        'monto',
        'fecha',
        'proveedor',
        'requiere_pago',
        'estado_pago',
    ];

    protected $casts = [
        'monto'         => 'decimal:2',
        'fecha'         => 'date',
        'requiere_pago' => 'boolean',
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public static function tipos(): array
    {
        return [
            'materiales'           => 'Materiales',
            'mano_obra'            => 'Mano de obra',
            'subcontratos'         => 'Subcontratos',
            'alquiler_maquinaria'  => 'Alquiler de maquinaria',
            'combustible'          => 'Combustible',
            'mantenimiento'        => 'Mantenimiento',
            'administrativo'       => 'Administrativo',
            'otros'                => 'Otros',
        ];
    }

    public static function estadosPago(): array
    {
        return [
            self::ESTADO_PENDIENTE => 'Pendiente',
            self::ESTADO_PARCIAL   => 'Parcial',
            self::ESTADO_PAGADO    => 'Pagado',
        ];
    }

    public function getMontoFormatoAttribute(): string
    {
        return number_format((float) $this->monto, 2, '.', ',');
    }
}