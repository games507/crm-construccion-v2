<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Limpia cache de Spatie
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Crea rol SuperAdmin (guard web)
        $role = Role::firstOrCreate(
            ['name' => 'SuperAdmin', 'guard_name' => 'web'],
            ['name' => 'SuperAdmin', 'guard_name' => 'web']
        );

        // Asigna al primer usuario del sistema
        $u = User::orderBy('id')->first();
        if ($u && method_exists($u, 'assignRole')) {
            if (!$u->hasRole('SuperAdmin')) {
                $u->assignRole('SuperAdmin');
            }
        }
    }
}
