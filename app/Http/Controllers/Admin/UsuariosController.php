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
    private function isSuperAdmin(): bool
    {
        return auth()->check() && auth()->user()->hasRole('SuperAdmin');
    }

    private function myEmpresaId(): ?int
    {
        return auth()->check() ? (auth()->user()->empresa_id ?? null) : null;
    }

    public function index(Request $r)
    {
        $q = trim((string)$r->get('q',''));

        $usersQ = User::query()->orderByDesc('id');

        // Multiempresa: SuperAdmin ve todo, Admin ve solo su empresa
        if (!$this->isSuperAdmin()) {
            $usersQ->where('empresa_id', $this->myEmpresaId());
        } else {
            if ($r->filled('empresa_id')) {
                $usersQ->where('empresa_id', (int)$r->get('empresa_id'));
            }
        }

        if ($q !== '') {
            $usersQ->where(function($qq) use ($q){
                $qq->where('name','like',"%{$q}%")
                   ->orWhere('email','like',"%{$q}%");
            });
        }

        $usuarios = $usersQ->paginate(15)->withQueryString();

        $empresas = $this->isSuperAdmin()
            ? Empresa::orderBy('nombre')->get(['id','nombre'])
            : collect([]);

        return view('admin.usuarios.index', compact('usuarios','q','empresas'));
    }

    public function create()
    {
        $isSuperAdmin = $this->isSuperAdmin();

        // Roles: si no es SuperAdmin, NO mostrar SuperAdmin
        $rolesQ = Role::query()->orderBy('name');
        if (!$isSuperAdmin) {
            $rolesQ->where('name', '!=', 'SuperAdmin');
        }
        $roles = $rolesQ->get(['id','name']);

        $empresas = $isSuperAdmin
            ? Empresa::orderBy('nombre')->get(['id','nombre'])
            : collect([]);

        return view('admin.usuarios.create', compact('roles','empresas','isSuperAdmin'));
    }

    public function store(Request $r)
    {
        $isSuperAdmin = $this->isSuperAdmin();

        $data = $r->validate([
            'name'       => ['required','string','max:160'],
            'email'      => ['required','email','max:190','unique:users,email'],
            'password'   => ['required','string','min:6'],
            'activo'     => ['nullable'],
            'role'       => ['nullable','string','exists:roles,name'],

            // empresa_id solo si es superadmin
            'empresa_id' => ['nullable','integer','exists:empresas,id'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.unique' => 'Ese correo ya existe.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        // Empresa asignada:
        // - SuperAdmin: la del form
        // - Admin: su misma empresa
        $empresaId = $isSuperAdmin ? ($data['empresa_id'] ?? null) : $this->myEmpresaId();

        // Blindaje: si no es superadmin, jamás permitir asignar rol SuperAdmin
        $roleName = $data['role'] ?? null;
        if (!$isSuperAdmin && $roleName === 'SuperAdmin') {
            $roleName = null;
        }

        $u = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'activo' => $r->boolean('activo', true),
            'empresa_id' => $empresaId,
        ]);

        if ($roleName) {
            $u->syncRoles([$roleName]);
        }

        return redirect()->route('admin.usuarios')->with('ok','Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        $isSuperAdmin = $this->isSuperAdmin();

        // Multiempresa: Admin no puede editar usuarios de otra empresa
        if (!$isSuperAdmin && (int)$user->empresa_id !== (int)$this->myEmpresaId()) {
            abort(403);
        }

        $rolesQ = Role::query()->orderBy('name');
        if (!$isSuperAdmin) {
            $rolesQ->where('name', '!=', 'SuperAdmin');
        }
        $roles = $rolesQ->get(['id','name']);

        $empresas = $isSuperAdmin
            ? Empresa::orderBy('nombre')->get(['id','nombre'])
            : collect([]);

        $userRole = $user->roles()->pluck('name')->first();

        return view('admin.usuarios.edit', compact('user','roles','empresas','isSuperAdmin','userRole'));
    }

    public function update(Request $r, User $user)
    {
        $isSuperAdmin = $this->isSuperAdmin();

        if (!$isSuperAdmin && (int)$user->empresa_id !== (int)$this->myEmpresaId()) {
            abort(403);
        }

        $data = $r->validate([
            'name'       => ['required','string','max:160'],
            'email'      => ['required','email','max:190','unique:users,email,'.$user->id],
            'password'   => ['nullable','string','min:6'],
            'activo'     => ['nullable'],
            'role'       => ['nullable','string','exists:roles,name'],
            'empresa_id' => ['nullable','integer','exists:empresas,id'],
        ]);

        $empresaId = $isSuperAdmin ? ($data['empresa_id'] ?? null) : $this->myEmpresaId();

        $roleName = $data['role'] ?? null;
        if (!$isSuperAdmin && $roleName === 'SuperAdmin') {
            $roleName = null;
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->activo = $r->boolean('activo', true);
        $user->empresa_id = $empresaId;

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        if ($roleName) {
            $user->syncRoles([$roleName]);
        } else {
            if (!$isSuperAdmin) {
                // Admin: si intentan borrar rol, lo dejamos sin cambios o lo dejamos vacío (tu decides)
                // Aquí lo dejamos vacío:
                $user->syncRoles([]);
            } else {
                $user->syncRoles([]);
            }
        }

        return redirect()->route('admin.usuarios')->with('ok','Usuario actualizado correctamente.');
    }
}
