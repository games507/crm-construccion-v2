<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Spatie
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * Mass assignable
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'empresa_id',
        'activo',
        'is_superadmin',
    ];

    /**
     * Hidden for serialization
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'activo'            => 'boolean',
        'is_superadmin'     => 'boolean',
    ];

    /**
     * Relaciones
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Helpers
     */
    public function isSuperAdmin(): bool
    {
        if ($this->is_superadmin) {
            return true;
        }

        if (method_exists($this, 'hasRole')) {
            return $this->hasRole('SuperAdmin');
        }

        return false;
    }
}
