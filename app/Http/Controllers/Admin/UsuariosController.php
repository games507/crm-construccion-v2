<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsuariosController extends Controller
{
    /**
     * Helpers
     */
    private function authOk(): bool
    {
        return auth()->check() && auth()->user();
    }

    private function isSuperAdmin(): bool
    {
        return $this->authOk() && auth()->user()->hasRole('SuperAdmin');
    }

    private function myEmpresaId(): ?int
    {
        return $this->authOk() ? (auth()->user()->empresa_id ?? null) : null;
    }

    /**
     * ✅ Quién puede asignar roles
     * - SuperAdmin siempre
     * - Admin de empresa: si tiene permiso usuarios.editar (ajusta si usas otro)
     */
    private function canAssignRoles(): bool
    {
        if (!$this->authOk()) return false;
        if ($this->isSuperAdmin()) return true;

        return auth()->user()->can('usuarios.editar'); // o 'usuarios.asignar_roles'
    }

    /**
     * ✅ Roles visibles según el tipo de usuario
     * - SuperAdmin: todos
     * - Admin empresa: todos menos SuperAdmin
     *
     * Si quieres limitar aún más los roles del Admin, usa $allowedForAdmin.
     */
    private function rolesForCurrentUser()
    {
        $rolesQ = Role::query()->orderBy('name');

        if (!$this->isSuperAdmin()) {
            $rolesQ->where('name', '!=', 'SuperAdmin');

            // (Opcional) limitar roles disponibles para Admin empresa:
            // $allowedForAdmin = ['AdminEmpresa', 'Supervisor', 'Operador'];
            // $rolesQ->whereIn('name', $allowedForAdmin);
        }

        return $rolesQ->get(['id', 'name']);
    }

    /**
     * ✅ Empresas visibles
     * - Solo SuperAdmin puede escoger empresa
     */
    private function empresasForView()
    {
        return $this->isSuperAdmin()
            ? Empresa::orderBy('nombre')->get(['id', 'nombre'])
            : collect([]);
    }

    /**
     * ✅ Seguridad multi-empresa:
     * Admin empresa NO puede editar usuarios de otra empresa
     */
    private function enforceSameEmpresaOr403(User $user): void
    {
        if ($this->isSuperAdmin()) return;

        $myEmpresaId = (int) ($this->myEmpresaId() ?? 0);
        if ($myEmpresaId <= 0) abort(403, 'Tu usuario no tiene empresa asignada.');

        if ((int) $user->empresa_id !== $myEmpresaId) abort(403);
    }

    /**
     * HOME: listado
     */
    public function index(Request $r)
    {
        $q = trim((string) $r->get('q', ''));

        $usersQ = User::query()->orderByDesc('id');

        // Multiempresa
        if (!$this->isSuperAdmin()) {
            $usersQ->where('empresa_id', $this->myEmpresaId());
        } else {
            if ($r->filled('empresa_id')) {
                $usersQ->where('empresa_id', (int) $r->get('empresa_id'));
            }
        }

        if ($q !== '') {
            $usersQ->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $usuarios = $usersQ->paginate(15)->withQueryString();
        $empresas = $this->empresasForView();

        return view('admin.usuarios.index', compact('usuarios', 'q', 'empresas'));
    }

    /**
     * CREATE
     */
    public function create()
    {
        $isSuperAdmin   = $this->isSuperAdmin();
        $roles          = $this->rolesForCurrentUser();
        $empresas       = $this->empresasForView();
        $canAssignRoles = $this->canAssignRoles();

        return view('admin.usuarios.create', compact('roles', 'empresas', 'isSuperAdmin', 'canAssignRoles'));
    }

    public function store(Request $r)
    {
        $isSuperAdmin = $this->isSuperAdmin();

        $data = $r->validate([
            'name'       => ['required', 'string', 'max:160'],
            'email'      => ['required', 'email', 'max:190', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:6'],
            'activo'     => ['nullable'],
            'role'       => ['nullable', 'string', 'exists:roles,name'],
            'empresa_id' => ['nullable', 'integer', 'exists:empresas,id'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.unique' => 'Ese correo ya existe.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        // Empresa asignada:
        // - SuperAdmin: desde el form
        // - Admin: su empresa
        $empresaId = $isSuperAdmin ? ($data['empresa_id'] ?? null) : $this->myEmpresaId();

        $u = User::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'activo'     => (int) $r->boolean('activo', true),
            'empresa_id' => $empresaId,
        ]);

        // Rol solo si puede asignar roles
        if ($this->canAssignRoles()) {
            $roleName = $data['role'] ?? null;

            // Blindaje: solo SuperAdmin puede asignar SuperAdmin
            if (!$isSuperAdmin && $roleName === 'SuperAdmin') {
                $roleName = null;
            }

            if ($roleName) {
                $u->syncRoles([$roleName]);
            }
        }

        return redirect()->route('admin.usuarios')->with('ok', 'Usuario creado correctamente.');
    }

    /**
     * EDIT
     */
    public function edit(User $user)
    {
        $this->enforceSameEmpresaOr403($user);

        $isSuperAdmin   = $this->isSuperAdmin();
        $roles          = $this->rolesForCurrentUser();
        $empresas       = $this->empresasForView();
        $canAssignRoles = $this->canAssignRoles();

        // ✅ El blade usa roleSel
        $roleSel = $user->roles()->pluck('name')->first() ?? '';

        return view('admin.usuarios.edit', compact(
            'user',
            'roles',
            'empresas',
            'isSuperAdmin',
            'roleSel',
            'canAssignRoles'
        ));
    }

    /**
     * UPDATE
     */
    public function update(Request $r, User $user)
    {
        $this->enforceSameEmpresaOr403($user);

        $isSuperAdmin = $this->isSuperAdmin();

        $rules = [
            'name'     => ['required', 'string', 'max:160'],
            'email'    => ['required', 'email', 'max:190', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:6'],
            'activo'   => ['nullable'],
            'role'     => ['nullable', 'string', 'exists:roles,name'],
        ];

        // Solo SuperAdmin puede cambiar empresa
        if ($isSuperAdmin) {
            $rules['empresa_id'] = ['nullable', 'integer', 'exists:empresas,id'];
        }

        $data = $r->validate($rules);

        $empresaId = $isSuperAdmin ? ($data['empresa_id'] ?? null) : $this->myEmpresaId();

        $user->name       = $data['name'];
        $user->email      = $data['email'];
        $user->empresa_id = $empresaId;

        // ✅ Checkbox: si no viene, queda 0
        $user->activo = (int) $r->boolean('activo');

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        // Rol solo si puede asignar roles
        if ($this->canAssignRoles()) {
            $roleName = $data['role'] ?? null;

            // Blindaje: solo SuperAdmin puede asignar SuperAdmin
            if (!$isSuperAdmin && $roleName === 'SuperAdmin') {
                $roleName = null;
            }

            if ($roleName) {
                $user->syncRoles([$roleName]);
            } else {
                $user->syncRoles([]); // si seleccionan vacío, se queda sin rol
            }
        }

        return redirect()->route('admin.usuarios')->with('ok', 'Usuario actualizado correctamente.');
    }
}
