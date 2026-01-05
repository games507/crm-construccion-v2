<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('permissions')->orderBy('name')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permisos = Permission::orderBy('name')->get();
        return view('admin.roles.create', compact('permisos'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name' => ['required','string','max:80','unique:roles,name'],
            'permissions' => ['array']
        ]);

        $role = Role::create(['name'=>$data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('admin.roles')->with('ok','Rol creado.');
    }

    public function edit(Role $role)
    {
        $permisos = Permission::orderBy('name')->get();
        $selected = $role->permissions()->pluck('name')->toArray();
        return view('admin.roles.edit', compact('role','permisos','selected'));
    }

    public function update(Request $r, Role $role)
    {
        $data = $r->validate([
            'name' => ['required','string','max:80','unique:roles,name,'.$role->id],
            'permissions' => ['array']
        ]);

        $role->update(['name'=>$data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('admin.roles')->with('ok','Rol actualizado.');
    }
}
