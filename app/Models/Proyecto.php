<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Proyecto extends Model
{
    protected $table = 'proyectos';

    // Estados recomendados
    public const ESTADO_PLANEADO   = 'planeado';
    public const ESTADO_EJECUCION  = 'en_ejecucion';
    public const ESTADO_PAUSADO    = 'pausado';
    public const ESTADO_FINALIZADO = 'finalizado';

    protected $fillable = [
        'empresa_id',
        'codigo',
        'nombre',
        'ubicacion',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'presupuesto',
        'activo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'presupuesto'  => 'decimal:2',
        'activo'       => 'boolean',
    ];

    // --------------------
    // Relaciones
    // --------------------
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function fases()
    {
        return $this->hasMany(ProyectoFase::class, 'proyecto_id')->orderBy('orden');
    }

    public function tareas()
    {
        return $this->hasMany(ProyectoTarea::class, 'proyecto_id');
    }

    public function costos()
    {
        return $this->hasMany(ProyectoCosto::class, 'proyecto_id');
    }

    public function documentos()
    {
        return $this->hasMany(ProyectoDocumento::class, 'proyecto_id');
    }

    // --------------------
    // Scopes
    // --------------------
    public function scopeActivos(Builder $q): Builder
    {
        return $q->where('activo', 1);
    }

    public function scopeEmpresa(Builder $q, int $empresaId): Builder
    {
        return $q->where('empresa_id', $empresaId);
    }

    // --------------------
    // Accesores útiles
    // --------------------
    public function getPresupuestoFormatoAttribute(): string
    {
        return number_format((float) $this->presupuesto, 2, '.', ',');
    }
}
