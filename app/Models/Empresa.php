<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresas';

    protected $fillable = [
        'nombre',
        'ruc',
        'dv',
        'contacto',
        'telefono',
        'email',
        'direccion',
        'logo_path',
        'settings',
        'activa',
        'activo',
        'admin_user_id',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'activo' => 'boolean',
        'settings' => 'array',
    ];

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class);
    }

    public function adminUser()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }
}
