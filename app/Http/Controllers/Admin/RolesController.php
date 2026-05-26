<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    private array $rolesProtegidos = [
        'SuperAdmin',
    ];

    private array $permisosProtegidos = [
        'admin.',
        'empresas.',
        'roles.',
        'permisos.',
    ];

    private function isSuperAdmin(): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        if (method_exists($user, 'hasRole') && $user->hasRole('SuperAdmin')) {
            return true;
        }

        return (bool) ($user->is_superadmin ?? false);
    }

    private function esRolProtegido(Role $role): bool
    {
        return in_array($role->name, $this->rolesProtegidos, true);
    }

    private function filtrarPermisosPermitidos()
    {
        $permisos = Permission::orderBy('name')->get();

        if ($this->isSuperAdmin()) {
            return $permisos;
        }

        return $permisos->reject(function ($permiso) {
            foreach ($this->permisosProtegidos as $prefijo) {
                if (str_starts_with($permiso->name, $prefijo)) {
                    return true;
                }
            }

            return false;
        })->values();
    }

    private function limpiarPermisosSolicitados(array $permissions): array
    {
        if ($this->isSuperAdmin()) {
            return $permissions;
        }

        return collect($permissions)
            ->reject(function ($permiso) {
                foreach ($this->permisosProtegidos as $prefijo) {
                    if (str_starts_with($permiso, $prefijo)) {
                        return true;
                    }
                }

                return false;
            })
            ->values()
            ->toArray();
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $rolesQ = Role::withCount('permissions')
            ->orderBy('name');

        if (!$this->isSuperAdmin()) {
            $rolesQ->whereNotIn('name', $this->rolesProtegidos);
        }

        if ($q !== '') {
            $rolesQ->where('name', 'like', "%{$q}%");
        }

        $roles = $rolesQ->paginate(15)->withQueryString();

        return view('admin.roles.index', compact('roles', 'q'));
    }

    public function create()
    {
        $permisos = $this->filtrarPermisosPermitidos();

        $grupos = $permisos->groupBy(function ($p) {
            $parts = explode('.', (string) $p->name);
            return $parts[0] ?? 'otros';
        });

        return view('admin.roles.create', compact('permisos', 'grupos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:80', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        if (!$this->isSuperAdmin() && in_array($data['name'], $this->rolesProtegidos, true)) {
            abort(403, 'No puedes crear roles protegidos del sistema.');
        }

        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);

        $permissions = $this->limpiarPermisosSolicitados($data['permissions'] ?? []);

        $role->syncPermissions($permissions);

        return redirect()
            ->route('admin.roles')
            ->with('ok', 'Rol creado.');
    }

    public function edit(Role $role)
    {
        if (!$this->isSuperAdmin() && $this->esRolProtegido($role)) {
            abort(403, 'No puedes editar este rol protegido.');
        }

        $permisos = $this->filtrarPermisosPermitidos();

        $grupos = $permisos->groupBy(function ($p) {
            $parts = explode('.', (string) $p->name);
            return $parts[0] ?? 'otros';
        });

        $selected = $role->permissions()
            ->pluck('name')
            ->toArray();

        $rolePerms = $selected;

        return view('admin.roles.edit', compact(
            'role',
            'permisos',
            'grupos',
            'selected',
            'rolePerms'
        ));
    }

    public function update(Request $request, Role $role)
    {
        if (!$this->isSuperAdmin() && $this->esRolProtegido($role)) {
            abort(403, 'No puedes actualizar este rol protegido.');
        }

        $data = $request->validate([
            'name'        => [
                'required',
                'string',
                'max:80',
                Rule::unique('roles', 'name')->ignore($role->id),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        if (!$this->isSuperAdmin() && in_array($data['name'], $this->rolesProtegidos, true)) {
            abort(403, 'No puedes usar nombres de roles protegidos.');
        }

        $role->update([
            'name' => $data['name'],
        ]);

        $permissions = $this->limpiarPermisosSolicitados($data['permissions'] ?? []);

        $role->syncPermissions($permissions);

        return redirect()
            ->route('admin.roles')
            ->with('ok', 'Rol actualizado.');
    }

    public function destroy(Role $role)
    {
        if (!$this->isSuperAdmin() && $this->esRolProtegido($role)) {
            abort(403, 'No puedes eliminar este rol protegido.');
        }

        if ($this->esRolProtegido($role)) {
            return back()->withErrors([
                'role' => 'El rol SuperAdmin no se puede eliminar.',
            ]);
        }

        $role->delete();

        return redirect()
            ->route('admin.roles')
            ->with('ok', 'Rol eliminado.');
    }
}