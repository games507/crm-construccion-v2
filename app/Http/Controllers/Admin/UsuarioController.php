<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller
{
public function index(\Illuminate\Http\Request $r)
{
    $q = trim((string)$r->get('q',''));
    $empresaId = $r->get('empresa_id');

    $empresas = \App\Models\Empresa::orderBy('nombre')->get();

    $usuariosQ = \App\Models\User::query()
      ->with(['empresa','roles'])

        ->orderBy('name');

    if ($q !== '') {
        $usuariosQ->where(function($qq) use ($q){
            $qq->where('name','like',"%{$q}%")
               ->orWhere('email','like',"%{$q}%");
        });
    }

    if (!empty($empresaId)) {
        $usuariosQ->where('empresa_id', (int)$empresaId);
    }

    $usuarios = $usuariosQ->paginate(15)->withQueryString();

    return view('admin.usuarios.index', compact('usuarios','empresas','q','empresaId'));
}


    public function create()
    {
        return view('admin.usuarios.create', [
            'empresas' => Empresa::orderBy('nombre')->get(['id','nombre']),
            'roles' => Role::orderBy('name')->get(['id','name']),
        ]);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name' => ['required','string','max:120'],
            'email' => ['required','email','max:150','unique:users,email'],
            'empresa_id' => ['nullable','integer','exists:empresas,id'],
            'password' => ['required','string','min:8','max:80'],
            'role' => ['nullable','string','exists:roles,name'],
        ]);

        $u = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'empresa_id' => $data['empresa_id'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        if (!empty($data['role'])) {
            $u->syncRoles([$data['role']]);
        }

        return redirect()->route('admin.usuarios')->with('ok','Usuario creado.');
    }

    public function edit(User $user)
    {
        return view('admin.usuarios.edit', [
            'user' => $user->load('roles','empresa'),
            'empresas' => Empresa::orderBy('nombre')->get(['id','nombre']),
            'roles' => Role::orderBy('name')->get(['id','name']),
            'roleSel' => $user->roles->first()?->name,
        ]);
    }

    public function update(Request $r, User $user)
    {
        $data = $r->validate([
            'name' => ['required','string','max:120'],
            'email' => ['required','email','max:150',"unique:users,email,{$user->id}"],
            'empresa_id' => ['nullable','integer','exists:empresas,id'],
            'password' => ['nullable','string','min:8','max:80'],
            'role' => ['nullable','string','exists:roles,name'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->empresa_id = $data['empresa_id'] ?? null;

        if (!empty($data['password'])) {
           $user->password = \Illuminate\Support\Facades\Hash::make($data['password']);
        }

        $user->save();
        
 // siempre aplica rol (si viene vacío, lo deja sin rol)
    $user->syncRoles(!empty($data['role']) ? [$data['role']] : []);

    return redirect()->route('admin.usuarios')->with('ok','Usuario actualizado.');


    }
}
