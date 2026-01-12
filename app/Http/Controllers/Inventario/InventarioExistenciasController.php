<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Support\EmpresaScope;

class InventarioExistenciasController extends Controller
{
    /**
     * EMPRESA ACTUAL (FIX)
     *
     * 1) Si existe EmpresaScope (super admin eligió empresa) => usamos esa
     * 2) Si no, caemos al empresa_id del usuario (usuarios normales)
     * 3) Si ninguna existe => 403 con mensaje claro
     */
    private function empresaIdOrAbort(): int
    {
        $user = auth()->user();

        // 1) empresa elegida por Super Admin (guardada en sesión)
        $scopeEmpresaId = (int) EmpresaScope::getId();

        // 2) fallback: empresa asignada al usuario normal
        $userEmpresaId = (int) ($user->empresa_id ?? 0);

        // 3) resolver empresa final
        $empresaId = $scopeEmpresaId > 0 ? $scopeEmpresaId : $userEmpresaId;

        // 4) si no hay empresa, bloquear con mensaje correcto
        if ($empresaId <= 0) {
            abort(403, 'Seleccione una empresa para continuar.');
        }

        return $empresaId;
    }

    /**
     * API para React (Existencias)
     *
     * Query params:
     * - q: busca por codigo / sku / descripcion
     * - almacen_id: filtra por almacén
     * - per_page: tamaño de página (default 50, max 200)
     */
    public function api(Request $request)
    {
        $empresaId = $this->empresaIdOrAbort();

        $q = trim((string) $request->query('q', ''));
        $almacenId = (int) $request->query('almacen_id', 0);

        $perPage = (int) $request->query('per_page', 50);
        if ($perPage <= 0) $perPage = 50;
        if ($perPage > 200) $perPage = 200;

        $query = DB::table('existencias as e')
            ->where('e.empresa_id', $empresaId)

            ->join('materiales as m', function ($join) use ($empresaId) {
                $join->on('m.id', '=', 'e.material_id')
                     ->where('m.empresa_id', '=', $empresaId);
            })

            ->join('almacenes as a', function ($join) use ($empresaId) {
                $join->on('a.id', '=', 'e.almacen_id')
                     ->where('a.empresa_id', '=', $empresaId);
            })

            ->when($almacenId > 0, function ($qq) use ($almacenId) {
                $qq->where('e.almacen_id', $almacenId);
            })

            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('m.codigo', 'like', "%{$q}%")
                      ->orWhere('m.sku', 'like', "%{$q}%")
                      ->orWhere('m.descripcion', 'like', "%{$q}%");
                });
            })

            ->select([
                'e.id',
                'm.id as material_id',
                'a.id as almacen_id',

                DB::raw('COALESCE(m.codigo, "") as material'),
                DB::raw('COALESCE(m.codigo, "") as codigo'),
                DB::raw('COALESCE(m.sku, "") as sku'),

                DB::raw('"" as categoria'),

                DB::raw('COALESCE(a.codigo, "") as almacen_codigo'),
                DB::raw('COALESCE(a.nombre, "") as almacen'),

                DB::raw('COALESCE(e.stock, 0) as existencia'),
                DB::raw('0 as minimo'),

                DB::raw('COALESCE(m.descripcion, "") as descripcion'),
                DB::raw('COALESCE(m.unidad, "") as unidad'),
                DB::raw('COALESCE(e.costo_promedio, 0) as costo_promedio'),
                DB::raw('COALESCE(m.costo_estandar, 0) as costo_estandar'),
                DB::raw('COALESCE(m.activo, 1) as activo'),
            ])
            ->orderBy('m.codigo')
            ->orderBy('a.nombre');

        $page = $query->paginate($perPage)->withQueryString();

        return response()->json([
            'data' => $page->items(),
            'meta' => [
                'current_page' => $page->currentPage(),
                'per_page'     => $page->perPage(),
                'total'        => $page->total(),
                'last_page'    => $page->lastPage(),
            ],
        ]);
    }
}
