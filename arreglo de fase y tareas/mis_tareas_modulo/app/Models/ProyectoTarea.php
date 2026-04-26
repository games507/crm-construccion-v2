<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProyectoTarea extends Model
{
    protected $table = 'proyecto_tareas';

    public const ESTADO_PENDIENTE   = 'pendiente';
    public const ESTADO_EN_PROCESO  = 'en_proceso';
    public const ESTADO_FINALIZADA  = 'finalizada';
    public const ESTADO_PAUSADA     = 'pausada';

    protected $fillable = [
        'proyecto_id',
        'fase_id',
        'responsable_id',
        'nombre',
        'descripcion',
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'porcentaje',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'porcentaje'   => 'decimal:2',
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function fase()
    {
        return $this->belongsTo(ProyectoFase::class, 'fase_id');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function getEstaVencidaAttribute(): bool
    {
        return $this->fecha_fin
            && $this->fecha_fin->lt(now()->startOfDay())
            && $this->estado !== self::ESTADO_FINALIZADA;
    }

    public function getEstadoLabelAttribute(): string
    {
        return match ($this->estado) {
            self::ESTADO_PENDIENTE => 'Pendiente',
            self::ESTADO_EN_PROCESO => 'En proceso',
            self::ESTADO_FINALIZADA => 'Finalizada',
            self::ESTADO_PAUSADA => 'Pausada',
            default => ucfirst((string) $this->estado),
        };
    }
}