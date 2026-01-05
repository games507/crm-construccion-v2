<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermisosController extends Controller
{
    public function index(Request $r)
    {
        $q = trim((string) $r->get('q', ''));

        $permisosQ = Permission::query()->orderBy('name');

        if ($q !== '') {
            $permisosQ->where('name', 'like', "%{$q}%");
        }

        $permisos = $permisosQ->paginate(20)->withQueryString();

        return view('admin.permisos.index', compact('permisos', 'q'));
    }

    public function create()
    {
        return view('admin.permisos.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name' => ['required', 'string', 'max:120', 'unique:permissions,name'],
        ], [
            'name.required' => 'El nombre del permiso es obligatorio.',
            'name.unique'   => 'Ese permiso ya existe.',
        ]);

        $name = strtolower(trim($data['name']));

        Permission::create([
            'name'       => $name,
            'guard_name' => 'web',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.permisos')->with('ok', 'Permiso creado.');
    }

    public function edit(Permission $permission)
    {
        return view('admin.permisos.edit', [
            'permiso' => $permission,
        ]);
    }

    public function update(Request $r, Permission $permission)
    {
        $data = $r->validate([
            'name' => ['required', 'string', 'max:120', 'unique:permissions,name,' . $permission->id],
        ], [
            'name.required' => 'El nombre del permiso es obligatorio.',
            'name.unique'   => 'Ese permiso ya existe.',
        ]);

        $permission->name = strtolower(trim($data['name']));
        $permission->save();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.permisos')->with('ok', 'Permiso actualizado.');
    }

    // ELIMINAR
    public function destroy(Permission $permission)
    {
        // Opcional: evita borrar permisos "críticos"
        $protegidos = ['admin.ver'];
        if (in_array($permission->name, $protegidos, true)) {
            return redirect()->route('admin.permisos')
                ->withErrors(['No puedes eliminar este permiso del sistema.']);
        }

        $permission->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.permisos')->with('ok', 'Permiso eliminado.');
    }
}
