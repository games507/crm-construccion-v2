<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'empresas';

    protected $fillable = [
        'nombre',
        'ruc',
        'dv',
        'contacto',
        'telefono',
        'email',
        'direccion',
        'activa',
        'activo',
        'logo_path',
        'admin_user_id',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'activo' => 'boolean',
    ];

    /**
     * Usuario administrador asignado
     */
    public function adminUser()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    /**
     * Usuarios de la empresa
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
