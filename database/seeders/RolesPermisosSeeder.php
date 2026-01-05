<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesPermisosSeeder extends Seeder
{
    public function run(): void
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $permisos = [
            // Admin
            'admin.ver',

            // Configuración
            'usuarios.ver','usuarios.crear','usuarios.editar',
            'roles.ver','roles.crear','roles.editar',
            'permisos.ver',
            'empresas.ver','empresas.crear','empresas.editar',
            'proyectos.ver','proyectos.crear','proyectos.editar',

            // Inventario
            'inventario.ver','inventario.crear','inventario.editar','inventario.eliminar',
            'kardex.ver',
            'materiales.ver','materiales.crear','materiales.editar','materiales.eliminar',
            'almacenes.ver','almacenes.crear','almacenes.editar','almacenes.eliminar',

            // (preparados)
            'compras.ver','compras.crear','compras.editar','compras.eliminar',
            'pedidos.ver','pedidos.crear','pedidos.editar','pedidos.eliminar',
            'finanzas.ver','finanzas.crear','finanzas.editar','finanzas.eliminar',
            'cotizaciones.ver','cotizaciones.crear','cotizaciones.editar','cotizaciones.eliminar',
            'facturas.ver','facturas.crear','facturas.editar','facturas.eliminar',
        ];

        foreach ($permisos as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $supervisor = Role::firstOrCreate(['name' => 'Supervisor']);
        $operador = Role::firstOrCreate(['name' => 'Operador']);

        $admin->syncPermissions(Permission::all());

        $supervisor->syncPermissions([
            'admin.ver',
            'usuarios.ver','roles.ver','permisos.ver',
            'empresas.ver','proyectos.ver',
            'inventario.ver','inventario.crear','inventario.editar',
            'kardex.ver',
            'materiales.ver','almacenes.ver',
        ]);

        $operador->syncPermissions([
            'inventario.ver','inventario.crear',
            'kardex.ver',
            'materiales.ver','almacenes.ver',
        ]);
    }
}
