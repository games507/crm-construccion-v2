<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\InvExistencia;
use App\Models\InvMovimiento;
use App\Models\Material;

use App\Support\EmpresaScope; // ✅ contexto de empresa (SuperAdmin)

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();

        // =========================
        // Empresa efectiva (multiempresa)
        // - SuperAdmin => contexto (session)
        // - Admin Empresa => su empresa_id
        // =========================
        $isSuperAdmin = false;
        if ($user) {
            if (method_exists($user, 'hasRole')) {
                $isSuperAdmin = $user->hasRole('SuperAdmin');
            } elseif (isset($user->is_superadmin)) {
                $isSuperAdmin = (bool) $user->is_superadmin;
            }
        }

        $empresaId = $isSuperAdmin ? EmpresaScope::getId() : ($user->empresa_id ?? null);

        // ✅ Si SuperAdmin no eligió empresa, no mostramos data mezclada
        if ($isSuperAdmin && !$empresaId) {
            $kpis = [
                'almacenes'         => 0,
                'materiales'        => 0,
                'stock_total'       => 0,
                'valor_inventario'  => 0,
                'proyectos'         => 0,
                'movimientos_total' => 0,
            ];

            return view('dashboard', [
                'kpis'         => $kpis,
                'ultimosMovs'  => collect(),
                'topMateriales'=> collect(),
            ]);
        }

        // ✅ Si NO es superadmin y no tiene empresa, lo paramos
        if (!$isSuperAdmin && !$empresaId) {
            abort(403, 'Tu usuario no tiene empresa asignada.');
        }

        // =========================
        // KPIs (FILTRADOS POR EMPRESA)
        // =========================
        $almacenes  = Almacen::where('empresa_id', $empresaId)->count();
        $materiales = Material::where('empresa_id', $empresaId)->count();

        $stockTotal = (float) InvExistencia::where('empresa_id', $empresaId)->sum('stock');

        // Valor inventario = sum(stock * costo_promedio)
        $valorInventario = (float) InvExistencia::where('empresa_id', $empresaId)
            ->selectRaw('COALESCE(SUM(stock * costo_promedio),0) as v')
            ->value('v');

        // Total REAL de movimientos
        $movimientosTotal = InvMovimiento::where('empresa_id', $empresaId)->count();

        // Últimos 3 movimientos
        $ultimosMovs = InvMovimiento::where('empresa_id', $empresaId)
            ->with(['material', 'almacenOrigen', 'almacenDestino'])
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        // Top materiales (valor)
        $topMateriales = InvExistencia::where('inv_existencias.empresa_id', $empresaId)
            ->join('materiales', function ($j) use ($empresaId) {
                $j->on('materiales.id', '=', 'inv_existencias.material_id')
                  // ✅ blindaje por si materiales también es multiempresa
                  ->where('materiales.empresa_id', '=', $empresaId);
            })
            ->selectRaw('
                materiales.sku,
                materiales.descripcion,
                SUM(inv_existencias.stock) as stock,
                SUM(inv_existencias.stock * inv_existencias.costo_promedio) as valor
            ')
            ->groupBy('materiales.sku', 'materiales.descripcion')
            ->orderByDesc('valor')
            ->limit(6)
            ->get();

        // Proyectos (si tienes modelo)
        $proyectos = 0;
        try {
            if (class_exists(\App\Models\Proyecto::class)) {
                $proyectos = \App\Models\Proyecto::where('empresa_id', $empresaId)->count();
            } elseif (class_exists(\App\Models\Project::class)) {
                $proyectos = \App\Models\Project::where('empresa_id', $empresaId)->count();
            }
        } catch (\Throwable $e) {
            $proyectos = 0;
        }

        $kpis = [
            'almacenes'         => $almacenes,
            'materiales'        => $materiales,
            'stock_total'       => $stockTotal,
            'valor_inventario'  => $valorInventario,
            'proyectos'         => $proyectos,
            'movimientos_total' => $movimientosTotal,
        ];

        return view('dashboard', compact('kpis', 'ultimosMovs', 'topMateriales'));
    }
}
