<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MiEmpresaController extends Controller
{
    public function edit()
    {
        $user = auth()->user();

        if (!$user || !$user->empresa_id) {
            abort(403, 'Tu usuario no tiene empresa asignada.');
        }

        // Mejor: traer directo por PK
        $empresa = Empresa::findOrFail((int)$user->empresa_id);

        return view('admin.mi_empresa.edit', compact('empresa'));
    }

    public function update(Request $r)
    {
        $user = auth()->user();

        if (!$user || !$user->empresa_id) {
            abort(403, 'Tu usuario no tiene empresa asignada.');
        }

        $empresa = Empresa::findOrFail((int)$user->empresa_id);

        $data = $r->validate([
            'nombre'    => ['required','string','max:160'],
            'ruc'       => ['nullable','string','max:80'],
            'dv'        => ['nullable','string','max:10'],
            'contacto'  => ['nullable','string','max:160'],
            'telefono'  => ['nullable','string','max:60'],
            'email'     => ['nullable','email','max:160'],     // OJO: tu columna es email (no "correo")
            'direccion' => ['nullable','string','max:220'],

            // logo
            'logo' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
        ], [
            'logo.image' => 'El logo debe ser una imagen.',
            'logo.mimes' => 'Logo permitido: JPG, PNG o WEBP.',
            'logo.max'   => 'El logo no debe exceder 2MB.',
        ]);

        // Mejora: guarda el logo en carpeta por empresa y con nombre fijo (evita acumulación)
        if ($r->hasFile('logo')) {
            $file = $r->file('logo');

            // Borra el anterior si existe
            if (!empty($empresa->logo_path) && Storage::disk('public')->exists($empresa->logo_path)) {
                Storage::disk('public')->delete($empresa->logo_path);
            }

            // Nombre fijo: logo + extensión real (logo.png / logo.webp ...)
            $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
            $path = $file->storeAs("empresas/{$empresa->id}", "logo.{$ext}", "public");

            $data['logo_path'] = $path;
        }

        $empresa->update($data);

        return redirect()
            ->route('admin.mi_empresa.edit')
            ->with('ok', 'Mi Empresa actualizada correctamente.');
    }
}
