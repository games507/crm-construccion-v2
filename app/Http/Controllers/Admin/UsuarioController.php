<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller
{
    public function index(Request $r)
    {
        $q = trim((string) $r->get('q', ''));
        $empresaId = $r->get('empresa_id');

        $empresas = Empresa::orderBy('nombre')->get();

        $usuariosQ = User::query()
            ->with(['empresa', 'roles'])
            ->orderBy('name');

        if ($q !== '') {
            $usuariosQ->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if (!empty($empresaId)) {
            $usuariosQ->where('empresa_id', (int) $empresaId);
        }

        $usuarios = $usuariosQ->paginate(15)->withQueryString();

        return view('admin.usuarios.index', compact('usuarios', 'empresas', 'q', 'empresaId'));
    }
public function create()
{
    return view('admin.usuarios.create', [
        'empresas' => Empresa::orderBy('nombre')
            ->get(['id','nombre','usuarios_limite','licencia_estado']),

        'roles' => Role::orderBy('name')
            ->get(['id','name']),

        'isSuperAdmin' => auth()->user()?->hasRole('SuperAdmin'),
    ]);
}
    public function store(Request $r)
    {
        $data = $r->validate([
            
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'empresa_id' => ['nullable', 'integer', 'exists:empresas,id'],
            'password' => ['required', 'string', 'min:8', 'max:80'],
            'role' => ['nullable', 'string', 'exists:roles,name'],

            
        ]);

        $this->validarLicenciaEmpresa($data['empresa_id'] ?? null);

        $u = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'empresa_id' => $data['empresa_id'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        if (!empty($data['role'])) {
            $u->syncRoles([$data['role']]);
        }

        return redirect()->route('admin.usuarios')->with('ok', 'Usuario creado.');
    }

    public function edit(User $user)
    {
        return view('admin.usuarios.edit', [
            'user' => $user->load('roles', 'empresa'),
            'empresas' => Empresa::orderBy('nombre')->get(['id', 'nombre', 'usuarios_limite', 'licencia_estado']),
            'roles' => Role::orderBy('name')->get(['id', 'name']),
            'roleSel' => $user->roles->first()?->name,
        ]);
    }

    public function update(Request $r, User $user)
    {
        $data = $r->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', "unique:users,email,{$user->id}"],
            'empresa_id' => ['nullable', 'integer', 'exists:empresas,id'],
            'password' => ['nullable', 'string', 'min:8', 'max:80'],
            'role' => ['nullable', 'string', 'exists:roles,name'],
        ]);

        $nuevoEmpresaId = $data['empresa_id'] ?? null;

        if ((string) ($user->empresa_id ?? '') !== (string) ($nuevoEmpresaId ?? '')) {
            $this->validarLicenciaEmpresa($nuevoEmpresaId, $user->id);
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->empresa_id = $nuevoEmpresaId;

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        $user->syncRoles(!empty($data['role']) ? [$data['role']] : []);

        return redirect()->route('admin.usuarios')->with('ok', 'Usuario actualizado.');
    }

    private function validarLicenciaEmpresa($empresaId, ?int $ignorarUserId = null): void
    {
        if (empty($empresaId)) {
            return;
        }

        $empresa = Empresa::find($empresaId);

        if (!$empresa) {
            return;
        }

        if (($empresa->licencia_estado ?? 'activa') === 'vencida') {
            throw ValidationException::withMessages([
                'empresa_id' => 'No se puede agregar usuarios: la licencia de esta empresa está vencida.',
            ]);
        }

        $limite = (int) ($empresa->usuarios_limite ?? 0);

        if ($limite <= 0) {
            return;
        }

        $usuariosActualesQ = User::where('empresa_id', $empresa->id);

        if ($ignorarUserId) {
            $usuariosActualesQ->where('id', '!=', $ignorarUserId);
        }

        $usuariosActuales = $usuariosActualesQ->count();

        if ($usuariosActuales >= $limite) {
            throw ValidationException::withMessages([
                'empresa_id' => "La empresa {$empresa->nombre} alcanzó el límite de usuarios de su licencia ({$limite}).",
            ]);
        }
    }
}