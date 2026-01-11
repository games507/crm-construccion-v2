<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InvExistencia;
use App\Models\Almacen;
use App\Support\EmpresaScope; // (Super Admin contexto)

class InventarioController extends Controller
{
    /**
     * PATRÓN ÚNICO PARA EMPRESA (CÓPIALO EN LOS DEMÁS)
     *
     * 1) Si hay contexto seleccionado (EmpresaScope::getId) -> úsalo (Super Admin)
     * 2) Si no hay contexto -> usa empresa_id del usuario (usuario normal)
     * 3) Si ambos son 0 -> 403 (mensaje claro)
     */
    private function empresaIdOrAbort(): int
    {
        $user = auth()->user();

        $scopeEmpresaId = (int) EmpresaScope::getId();        // super admin cuando elige empresa
        $userEmpresaId  = (int) ($user->empresa_id ?? 0);     // usuario normal

        $empresaId = $scopeEmpresaId > 0 ? $scopeEmpresaId : $userEmpresaId;

        if ($empresaId <= 0) {
            abort(403, 'Seleccione una empresa para continuar.');
        }

        return $empresaId;
    }

    public function existencias(Request $r)
    {
        // Antes: (int) auth()->user()->empresa_id + abort 403
        // Ahora: soporta Super Admin por contexto y usuario normal por empresa_id
        $empresaId = $this->empresaIdOrAbort();

        $almacenId = (int) $r->get('almacen_id', 0);
        $q = trim((string) $r->get('q', ''));

        $query = InvExistencia::query()
            ->with([
                'material:id,codigo,sku,descripcion,unidad,unidad_id',
                'material.unidadRef:id,codigo,descripcion',
                'almacen:id,codigo,nombre',
            ])
            ->where('empresa_id', $empresaId);

        if ($almacenId > 0) {
            $query->where('almacen_id', $almacenId);
        }

        if ($q !== '') {
            $query->whereHas('material', function ($mq) use ($q) {
                $mq->where('descripcion', 'like', "%{$q}%")
                   ->orWhere('codigo', 'like', "%{$q}%")
                   ->orWhere('sku', 'like', "%{$q}%");
            });
        }

        $existencias = $query
            ->orderBy('almacen_id')
            ->orderBy('material_id')
            ->paginate(20)
            ->withQueryString();

        $almacenes = Almacen::where('empresa_id', $empresaId)
            ->orderBy('nombre')
            ->get(['id','codigo','nombre']);

        return view('inventario.existencias.index', compact('existencias','almacenes','almacenId','q'));
    }
}
