<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmpresasController extends Controller
{
    private function isSuperAdmin(): bool
    {
        return auth()->check() && auth()->user()->hasRole('SuperAdmin');
        // Alternativa si lo manejas por permiso:
        // return auth()->check() && auth()->user()->can('empresas.todas');
    }

    public function index(Request $r)
    {
        if (!$this->isSuperAdmin()) {
            // Usuario normal: NO debe ver listado de empresas
            return redirect()->route('admin.mi_empresa.edit');
        }

        $q = trim((string)$r->get('q',''));

        $empresasQ = Empresa::query()
            ->with('adminUser')
            ->orderBy('nombre');

        if ($q !== '') {
            $empresasQ->where(function($qq) use ($q){
                $qq->where('nombre','like',"%{$q}%")
                   ->orWhere('ruc','like',"%{$q}%")
                   ->orWhere('email','like',"%{$q}%")
                   ->orWhere('telefono','like',"%{$q}%");
            });
        }

        $empresas = $empresasQ->paginate(15)->withQueryString();

        return view('admin.empresas.index', compact('empresas','q'));
    }

    public function create()
    {
        if (!$this->isSuperAdmin()) {
            return redirect()->route('admin.mi_empresa.edit');
        }

        // Usuarios candidatos a ser admin de empresa:
        // puedes filtrar a los que NO tengan empresa asignada, o a todos.
        $usuarios = User::orderBy('name')->get(['id','name','email','empresa_id']);

        return view('admin.empresas.create', compact('usuarios'));
    }

    public function store(Request $r)
    {
        if (!$this->isSuperAdmin()) {
            return redirect()->route('admin.mi_empresa.edit');
        }

        $data = $r->validate([
            'nombre'    => ['required','string','max:160'],
            'ruc'       => ['nullable','string','max:80'],
            'dv'        => ['nullable','string','max:10'],
            'contacto'  => ['nullable','string','max:160'],
            'telefono'  => ['nullable','string','max:60'],
            'email'     => ['nullable','email','max:160'],
            'direccion' => ['nullable','string','max:220'],
            'activa'    => ['nullable'],
            'activo'    => ['nullable'],
            'admin_user_id' => ['nullable','integer','exists:users,id'],
            'logo'      => ['nullable','image','mimes:png,jpg,jpeg,webp','max:2048'],
        ]);

        $data['activa'] = $r->boolean('activa');
        $data['activo'] = $r->boolean('activo', true);

        // Upload logo
        if ($r->hasFile('logo')) {
            $path = $r->file('logo')->store('empresas', 'public');
            $data['logo_path'] = $path;
        }

        $empresa = Empresa::create($data);

        // Si asignó admin_user_id: le amarramos la empresa al usuario
        if (!empty($data['admin_user_id'])) {
            $u = User::find((int)$data['admin_user_id']);
            if ($u) {
                $u->empresa_id = $empresa->id;
                $u->save();

                // opcional: asignar rol admin empresa automáticamente
                if (method_exists($u, 'assignRole') && !$u->hasRole('Admin')) {
                    // Ajusta el nombre del rol según tu sistema:
                    if (\Spatie\Permission\Models\Role::where('name','AdminEmpresa')->exists()) {
                        $u->syncRoles(['AdminEmpresa']);
                    }
                }
            }
        }

        return redirect()->route('admin.empresas')->with('ok','Empresa creada.');
    }

    public function edit(Empresa $empresa)
    {
        if (!$this->isSuperAdmin()) {
            return redirect()->route('admin.mi_empresa.edit');
        }

        $usuarios = User::orderBy('name')->get(['id','name','email','empresa_id']);

        return view('admin.empresas.edit', compact('empresa','usuarios'));
    }

    public function update(Request $r, Empresa $empresa)
    {
        if (!$this->isSuperAdmin()) {
            return redirect()->route('admin.mi_empresa.edit');
        }

        $data = $r->validate([
            'nombre'    => ['required','string','max:160'],
            'ruc'       => ['nullable','string','max:80'],
            'dv'        => ['nullable','string','max:10'],
            'contacto'  => ['nullable','string','max:160'],
            'telefono'  => ['nullable','string','max:60'],
            'email'     => ['nullable','email','max:160'],
            'direccion' => ['nullable','string','max:220'],
            'activa'    => ['nullable'],
            'activo'    => ['nullable'],
            'admin_user_id' => ['nullable','integer','exists:users,id'],
            'logo'      => ['nullable','image','mimes:png,jpg,jpeg,webp','max:2048'],
            'remove_logo' => ['nullable'],
        ]);

        $data['activa'] = $r->boolean('activa');
        $data['activo'] = $r->boolean('activo', true);

        // Quitar logo
        if ($r->boolean('remove_logo') && $empresa->logo_path) {
            Storage::disk('public')->delete($empresa->logo_path);
            $data['logo_path'] = null;
        }

        // Reemplazar logo
        if ($r->hasFile('logo')) {
            if ($empresa->logo_path) {
                Storage::disk('public')->delete($empresa->logo_path);
            }
            $data['logo_path'] = $r->file('logo')->store('empresas', 'public');
        }

        $empresa->update($data);

        // Re-amarrar admin si aplica
        if (!empty($data['admin_user_id'])) {
            $u = User::find((int)$data['admin_user_id']);
            if ($u) {
                $u->empresa_id = $empresa->id;
                $u->save();
            }
        }

        return redirect()->route('admin.empresas')->with('ok','Empresa actualizada.');
    }

    public function destroy(Empresa $empresa)
    {
        if (!$this->isSuperAdmin()) {
            abort(403);
        }

        if ($empresa->logo_path) {
            Storage::disk('public')->delete($empresa->logo_path);
        }

        $empresa->delete();

        return redirect()->route('admin.empresas')->with('ok','Empresa eliminada.');
    }
}
