<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Proyecto extends Model
{
    protected $table = 'proyectos';

    public const ESTADO_PLANEADO   = 'planeado';
    public const ESTADO_EJECUCION  = 'en_ejecucion';
    public const ESTADO_PAUSADO    = 'pausado';
    public const ESTADO_FINALIZADO = 'finalizado';

    protected $fillable = [
        'empresa_id',
        'responsable_id',
        'codigo',
        'nombre',
        'descripcion',
        'ubicacion',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'presupuesto',
        'porcentaje',
        'activo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'presupuesto'  => 'decimal:2',
        'porcentaje'   => 'decimal:2',
        'activo'       => 'boolean',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
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

    public function scopeActivos(Builder $q): Builder
    {
        return $q->where('activo', 1);
    }

    public function scopeEmpresa(Builder $q, int $empresaId): Builder
    {
        return $q->where('empresa_id', $empresaId);
    }

    public function getPresupuestoFormatoAttribute(): string
    {
        return number_format((float) $this->presupuesto, 2, '.', ',');
    }

    /**
     * Avance calculado desde tareas
     * Si ya guardas porcentaje en la tabla proyectos,
     * puedes usar este accesor como respaldo o referencia.
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

    public static function estados(): array
    {
        return [
            self::ESTADO_PLANEADO   => 'Planeado',
            self::ESTADO_EJECUCION  => 'En ejecución',
            self::ESTADO_PAUSADO    => 'Pausado',
            self::ESTADO_FINALIZADO => 'Finalizado',
        ];
    }
}