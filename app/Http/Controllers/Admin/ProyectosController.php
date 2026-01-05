<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Proyecto;
use Illuminate\Http\Request;

class ProyectosController extends Controller
{
    public function index(Request $r)
    {
        $q = trim((string)$r->get('q',''));
        $empresaId = $r->get('empresa_id');
        $estado = trim((string)$r->get('estado',''));
        $soloActivos = $r->get('solo_activos'); // "1" o null

        $userEmpresaId = (int)(auth()->user()->empresa_id ?? 0);
        $isAdminEmpresas = auth()->user()->can('empresas.ver');

        $empresas = Empresa::orderBy('nombre')->get(['id','nombre']);

        $qProy = Proyecto::query()
            ->with('empresa')
            ->orderByDesc('id');

        // Multiempresa:
        // - Si NO es admin de empresas, solo puede ver su empresa.
        if (!$isAdminEmpresas) {
            if ($userEmpresaId <= 0) {
                $proyectos = Proyecto::whereRaw('1=0')->paginate(15);
                return view('admin.proyectos.index', compact('proyectos','empresas','q','empresaId','estado','soloActivos'))
                    ->withErrors(['empresa_id' => 'Tu usuario no tiene empresa asignada.']);
            }
            $qProy->where('empresa_id', $userEmpresaId);
        } else {
            // Admin: puede filtrar por empresa
            if (!empty($empresaId)) {
                $qProy->where('empresa_id', (int)$empresaId);
            }
        }

        if ($q !== '') {
            $qProy->where(function($qq) use ($q){
                $qq->where('codigo','like',"%{$q}%")
                   ->orWhere('nombre','like',"%{$q}%")
                   ->orWhere('ubicacion','like',"%{$q}%")
                   ->orWhere('estado','like',"%{$q}%");
            });
        }

        if ($estado !== '') {
            $qProy->where('estado', $estado);
        }

        if ($soloActivos) {
            $qProy->where('activo', 1);
        }

        $proyectos = $qProy->paginate(15)->withQueryString();

        return view('admin.proyectos.index', compact('proyectos','empresas','q','empresaId','estado','soloActivos'));
    }

    public function create()
    {
        return view('admin.proyectos.create', [
            'empresas' => Empresa::orderBy('nombre')->get(['id','nombre']),
        ]);
    }

    public function store(Request $r)
    {
        $userEmpresaId = (int)(auth()->user()->empresa_id ?? 0);

        $data = $r->validate([
            'empresa_id'   => ['nullable','integer','exists:empresas,id'],
            'codigo'       => ['nullable','string','max:40'],
            'nombre'       => ['required','string','max:160'],
            'ubicacion'    => ['nullable','string','max:220'],
            'fecha_inicio' => ['nullable','date'],
            'fecha_fin'    => ['nullable','date','after_or_equal:fecha_inicio'],
            'estado'       => ['required','string','max:30'],
            'presupuesto'  => ['nullable','numeric','min:0'],
            'activo'       => ['nullable'],
        ], [
            'estado.required' => 'El campo estado es obligatorio.',
        ]);

        if (!auth()->user()->can('empresas.ver')) {
            if ($userEmpresaId <= 0) {
                return back()->withErrors(['empresa_id' => 'Tu usuario no tiene empresa asignada.'])->withInput();
            }
            $data['empresa_id'] = $userEmpresaId;
        } else {
            if (empty($data['empresa_id'])) {
                if ($userEmpresaId <= 0) {
                    return back()->withErrors(['empresa_id' => 'Selecciona una empresa.'])->withInput();
                }
                $data['empresa_id'] = $userEmpresaId;
            }
        }

        $data['activo'] = $r->boolean('activo');
        $data['presupuesto'] = $data['presupuesto'] ?? 0;

        Proyecto::create($data);

        return redirect()->route('admin.proyectos')->with('ok','Proyecto creado.');
    }

    public function edit(Proyecto $proyecto)
    {
        // Multiempresa: un usuario normal no debe editar proyectos de otra empresa
        if (!auth()->user()->can('empresas.ver')) {
            $userEmpresaId = (int)(auth()->user()->empresa_id ?? 0);
            if ($proyecto->empresa_id !== $userEmpresaId) {
                abort(403, 'No tienes permisos para editar este proyecto.');
            }
        }

        return view('admin.proyectos.edit', [
            'proyecto' => $proyecto->load('empresa'),
            'empresas' => Empresa::orderBy('nombre')->get(['id','nombre']),
        ]);
    }

    public function update(Request $r, Proyecto $proyecto)
    {
        if (!auth()->user()->can('empresas.ver')) {
            $userEmpresaId = (int)(auth()->user()->empresa_id ?? 0);
            if ($proyecto->empresa_id !== $userEmpresaId) {
                abort(403, 'No tienes permisos para editar este proyecto.');
            }
        }

        $userEmpresaId = (int)(auth()->user()->empresa_id ?? 0);

        $data = $r->validate([
            'empresa_id'   => ['nullable','integer','exists:empresas,id'],
            'codigo'       => ['nullable','string','max:40'],
            'nombre'       => ['required','string','max:160'],
            'ubicacion'    => ['nullable','string','max:220'],
            'fecha_inicio' => ['nullable','date'],
            'fecha_fin'    => ['nullable','date','after_or_equal:fecha_inicio'],
            'estado'       => ['required','string','max:30'],
            'presupuesto'  => ['nullable','numeric','min:0'],
            'activo'       => ['nullable'],
        ], [
            'estado.required' => 'El campo estado es obligatorio.',
        ]);

        if (!auth()->user()->can('empresas.ver')) {
            if ($userEmpresaId <= 0) {
                return back()->withErrors(['empresa_id' => 'Tu usuario no tiene empresa asignada.'])->withInput();
            }
            $data['empresa_id'] = $userEmpresaId;
        } else {
            if (empty($data['empresa_id'])) {
                $data['empresa_id'] = $proyecto->empresa_id; // mantiene
            }
        }

        $proyecto->fill($data);
        $proyecto->activo = $r->boolean('activo');
        if ($proyecto->presupuesto === null) $proyecto->presupuesto = 0;
        $proyecto->save();

        return redirect()->route('admin.proyectos')->with('ok','Proyecto actualizado.');
    }
}
