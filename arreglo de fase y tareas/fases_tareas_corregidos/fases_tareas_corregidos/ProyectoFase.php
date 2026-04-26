<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProyectoFase extends Model
{
    protected $fillable = [
        'proyecto_id',
        'nombre',
        'orden',
        'porcentaje',
    ];

    /**
     * Relación con proyecto
     */
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    /**
     * Relación con tareas (CLAVE 🔥)
     */
    public function tareas()
    {
        return $this->hasMany(ProyectoTarea::class, 'fase_id');
    }

    /**
     * Avance calculado de la fase (opcional pero pro)
     */
    public function getAvanceAttribute(): float
    {
        $total = $this->tareas()->count();

        if ($total === 0) {
            return 0;
        }

        $sum = (float) $this->tareas()->sum('porcentaje');

        return round($sum / $total, 2);
    }
}