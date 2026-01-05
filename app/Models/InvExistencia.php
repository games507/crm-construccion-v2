<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvExistencia extends Model
{
    protected $table = 'inv_existencias';

    protected $fillable = [
        'empresa_id',
        'almacen_id',
        'material_id',
        'cantidad',
    ];

    protected $casts = [
        'cantidad' => 'decimal:4',
    ];

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'almacen_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
