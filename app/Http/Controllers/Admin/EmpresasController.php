<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Role;

class EmpresasController extends Controller
{
    /**
     * ✅ SuperAdmin = rol Spatie "SuperAdmin" O columna is_superadmin = 1
     */
    private function isSuperAdmin(): bool
    {
        $u = auth()->user();
        if (!$u) return false;

        // Spatie role
        if (method_exists($u, 'hasRole') && $u->hasRole('SuperAdmin')) {
            return true;
        }

        // columna booleana
        return (bool) ($u->is_superadmin ?? false);
    }

    /**
     * ✅ Si no es SuperAdmin, redirige a su empresa
     */
    private function redirectIfNotSuperAdmin()
    {
        if (!$this->isSuperAdmin()) {
            return redirect()->route('admin.mi_empresa.edit');
        }
        return null;
    }

    public function index(Request $r)
    {
        if ($resp = $this->redirectIfNotSuperAdmin()) return $resp;

        $q = trim((string) $r->get('q', ''));

        $empresasQ = Empresa::query()
            ->with('adminUser')
            ->orderBy('nombre');

        if ($q !== '') {
            $empresasQ->where(function ($qq) use ($q) {
                $qq->where('nombre', 'like', "%{$q}%")
                    ->orWhere('ruc', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('telefono', 'like', "%{$q}%");
            });
        }

        $empresas = $empresasQ->paginate(15)->withQueryString();

        return view('admin.empresas.index', compact('empresas', 'q'));
    }

    public function create()
    {
        if ($resp = $this->redirectIfNotSuperAdmin()) return $resp;

        $usuarios = User::orderBy('name')->get(['id', 'name', 'email', 'empresa_id']);

        return view('admin.empresas.create', compact('usuarios'));
    }

    public function store(Request $r)
    {
        if ($resp = $this->redirectIfNotSuperAdmin()) return $resp;

        $data = $r->validate([
            'nombre'        => ['required', 'string', 'max:160'],
            'ruc'           => ['nullable', 'string', 'max:80'],
            'dv'            => ['nullable', 'string', 'max:10'],
            'contacto'      => ['nullable', 'string', 'max:160'],
            'telefono'      => ['nullable', 'string', 'max:60'],
            'email'         => ['nullable', 'email', 'max:160'],
            'direccion'     => ['nullable', 'string', 'max:220'],
            'activa'        => ['nullable'],
            'activo'        => ['nullable'],
            'admin_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'logo'          => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ]);

        $data['activa'] = $r->boolean('activa');
        $data['activo'] = $r->boolean('activo', true);

        // Upload logo
        if ($r->hasFile('logo')) {
            $data['logo_path'] = $r->file('logo')->store('empresas', 'public');
        }

        $empresa = Empresa::create($data);

        // Si asignó admin_user_id: amarrar empresa al usuario
        if (!empty($data['admin_user_id'])) {
            $this->assignAdminUserToEmpresa((int) $data['admin_user_id'], $empresa);
        }

        // Limpia cache de permisos/roles
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.empresas')->with('ok', 'Empresa creada.');
    }

    public function edit(Empresa $empresa)
    {
        if ($resp = $this->redirectIfNotSuperAdmin()) return $resp;

        $usuarios = User::orderBy('name')->get(['id', 'name', 'email', 'empresa_id']);

        return view('admin.empresas.edit', compact('empresa', 'usuarios'));
    }

    public function update(Request $r, Empresa $empresa)
    {
        if ($resp = $this->redirectIfNotSuperAdmin()) return $resp;

        $data = $r->validate([
            'nombre'        => ['required', 'string', 'max:160'],
            'ruc'           => ['nullable', 'string', 'max:80'],
            'dv'            => ['nullable', 'string', 'max:10'],
            'contacto'      => ['nullable', 'string', 'max:160'],
            'telefono'      => ['nullable', 'string', 'max:60'],
            'email'         => ['nullable', 'email', 'max:160'],
            'direccion'     => ['nullable', 'string', 'max:220'],
            'activa'        => ['nullable'],
            'activo'        => ['nullable'],
            'admin_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'logo'          => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'remove_logo'   => ['nullable'],
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
            $this->assignAdminUserToEmpresa((int) $data['admin_user_id'], $empresa);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.empresas')->with('ok', 'Empresa actualizada.');
    }

    public function destroy(Empresa $empresa)
    {
        if (!$this->isSuperAdmin()) {
            abort(403, 'Solo SuperAdmin puede eliminar empresas.');
        }

        if ($empresa->logo_path) {
            Storage::disk('public')->delete($empresa->logo_path);
        }

        $empresa->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.empresas')->with('ok', 'Empresa eliminada.');
    }

    /**
     * ✅ Helper: asigna admin_user_id a una empresa y le pone empresa_id al usuario
     * Además (opcional): asigna rol AdminEmpresa si existe
     */
    private function assignAdminUserToEmpresa(int $adminUserId, Empresa $empresa): void
    {
        $u = User::find($adminUserId);
        if (!$u) return;

        // Asignar empresa al usuario seleccionado
        $u->empresa_id = $empresa->id;
        $u->save();

        // (Opcional recomendado) guardar admin_user_id en empresa si tu tabla lo tiene
        if (isset($empresa->admin_user_id)) {
            $empresa->admin_user_id = $u->id;
            $empresa->save();
        }

        // (Opcional) asignar rol admin empresa automáticamente
        if (method_exists($u, 'syncRoles')) {
            if (Role::where('name', 'AdminEmpresa')->exists()) {
                // Blindaje: NO tocar SuperAdmin si por algún motivo el usuario lo tiene
                if (method_exists($u, 'hasRole') && $u->hasRole('SuperAdmin')) {
                    return;
                }
                $u->syncRoles(['AdminEmpresa']);
            }
        }
    }
}
