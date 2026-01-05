<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesController extends Controller
{
    public function index(Request $r)
    {
        $q = trim((string)$r->get('q', ''));

        $rolesQ = Role::query()->orderBy('name');

        if ($q !== '') {
            $rolesQ->where('name', 'like', "%{$q}%");
        }

        $roles = $rolesQ->paginate(15)->withQueryString();

        return view('admin.roles.index', compact('roles', 'q'));
    }

    /** Construye grupos para la vista */
    private function buildGrupos()
    {
        // devuelve Collection agrupada por prefijo: usuarios.*, roles.*, inventario.*, etc.
        return Permission::orderBy('name')
            ->get()
            ->groupBy(function ($p) {
                $name = (string) $p->name;
                $parts = explode('.', $name);
                return $parts[0] ?? 'otros';
            });
    }

    public function create()
    {
        $grupos = $this->buildGrupos();

        return view('admin.roles.create', [
            'grupos' => $grupos,
            'rolePerms' => [],
        ]);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name' => ['required', 'string', 'max:80', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ], [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.unique'   => 'Ya existe un rol con ese nombre.',
        ]);

        $role = Role::create(['name' => $data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles')->with('ok', 'Rol creado correctamente.');
    }

    public function edit(Role $role)
    {
        $grupos = $this->buildGrupos();
        $rolePerms = $role->permissions()->pluck('name')->toArray();

        return view('admin.roles.edit', [
            'role' => $role,
            'grupos' => $grupos,
            'rolePerms' => $rolePerms,
        ]);
    }

    public function update(Request $r, Role $role)
    {
        $data = $r->validate([
            'name' => ['required', 'string', 'max:80', 'unique:roles,name,' . $role->id],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ], [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.unique'   => 'Ya existe un rol con ese nombre.',
        ]);

        $role->name = $data['name'];
        $role->save();

        $role->syncPermissions($data['permissions'] ?? []);

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles')->with('ok', 'Rol actualizado correctamente.');
    }

    /**
     * ELIMINAR ROL
     * Requiere permiso: roles.eliminar
     * Ruta: admin.roles.destroy (DELETE admin/roles/{role})
     */
    public function destroy(Role $role)
    {
        // (Opcional) Bloquear eliminación del Admin
        if (strtolower($role->name) === 'admin') {
            return redirect()->route('admin.roles')
                ->withErrors(['name' => 'No se puede eliminar el rol Admin.']);
        }

        // limpiar permisos/relaciones
        $role->syncPermissions([]);
        $role->delete();

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles')->with('ok', 'Rol eliminado.');
    }
}
