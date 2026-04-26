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
}