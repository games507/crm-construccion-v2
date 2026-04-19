<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\InvExistencia;
use App\Models\InvMovimiento;
use App\Models\Material;
use Illuminate\Support\Facades\Schema;
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
                'almacenes'            => 0,
                'materiales'           => 0,
                'stock_total'          => 0,
                'valor_inventario'     => 0,
                'proyectos'            => 0,
                'proyectos_activos'    => 0,
                'proyectos_finalizados'=> 0,
                'tareas_pendientes'    => 0,
                'tareas_vencidas'      => 0,
                'movimientos_total'    => 0,
            ];

            return view('dashboard', [
                'kpis'              => $kpis,
                'ultimosMovs'       => collect(),
                'topMateriales'     => collect(),
                'proyectosRecientes'=> collect(),
            ]);
        }

        // ✅ Si NO es superadmin y no tiene empresa, lo paramos
        if (!$isSuperAdmin && !$empresaId) {
            abort(403, 'Tu usuario no tiene empresa asignada.');
        }

        // =========================
        // KPIs INVENTARIO (FILTRADOS POR EMPRESA)
        // =========================
        $almacenes = Almacen::where('empresa_id', $empresaId)->count();

        $materiales = Material::where('empresa_id', $empresaId)->count();

        $stockTotal = (float) InvExistencia::where('empresa_id', $empresaId)->sum('stock');

        // Valor inventario = sum(stock * costo_promedio)
        $valorInventario = (float) InvExistencia::where('empresa_id', $empresaId)
            ->selectRaw('COALESCE(SUM(stock * costo_promedio),0) as v')
            ->value('v');

        $movimientosTotal = InvMovimiento::where('empresa_id', $empresaId)->count();

        $ultimosMovs = InvMovimiento::where('empresa_id', $empresaId)
            ->with(['material', 'almacenOrigen', 'almacenDestino'])
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        $topMateriales = InvExistencia::where('inv_existencias.empresa_id', $empresaId)
            ->join('materiales', function ($j) use ($empresaId) {
                $j->on('materiales.id', '=', 'inv_existencias.material_id')
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

        // =========================
        // KPIs PROYECTOS / TAREAS
        // =========================
        $proyectos = 0;
        $proyectosActivos = 0;
        $proyectosFinalizados = 0;
        $tareasPendientes = 0;
        $tareasVencidas = 0;
        $proyectosRecientes = collect();

        try {
            if (class_exists(\App\Models\Proyecto::class)) {
                $proyectoModel = \App\Models\Proyecto::query()
                    ->where('empresa_id', $empresaId);

                $proyectos = (clone $proyectoModel)->count();

                if (Schema::hasColumn('proyectos', 'estado')) {
                    $proyectosActivos = (clone $proyectoModel)
                        ->whereIn('estado', ['planeado', 'en_ejecucion', 'pausado'])
                        ->count();

                    $proyectosFinalizados = (clone $proyectoModel)
                        ->where('estado', 'finalizado')
                        ->count();
                }

                $proyectosRecientes = \App\Models\Proyecto::query()
                    ->where('empresa_id', $empresaId)
                    ->latest('id')
                    ->limit(5)
                    ->get();
            } elseif (class_exists(\App\Models\Project::class)) {
                $proyectos = \App\Models\Project::where('empresa_id', $empresaId)->count();
            }
        } catch (\Throwable $e) {
            $proyectos = 0;
            $proyectosActivos = 0;
            $proyectosFinalizados = 0;
            $proyectosRecientes = collect();
        }

        try {
            if (class_exists(\App\Models\ProyectoTarea::class)) {
                $tareaModel = \App\Models\ProyectoTarea::query()
                    ->where('proyecto_tareas.empresa_id', $empresaId);

                // Si tu tabla proyecto_tareas no tiene empresa_id,
                // entonces hacemos filtro por proyecto
                if (!Schema::hasColumn('proyecto_tareas', 'empresa_id')) {
                    $tareaModel = \App\Models\ProyectoTarea::query()
                        ->join('proyectos', 'proyectos.id', '=', 'proyecto_tareas.proyecto_id')
                        ->where('proyectos.empresa_id', $empresaId);
                }

                if (Schema::hasColumn('proyecto_tareas', 'estado')) {
                    $tareasPendientes = (clone $tareaModel)
                        ->whereIn('proyecto_tareas.estado', ['pendiente', 'en_proceso', 'pausada'])
                        ->count();
                }

                if (Schema::hasColumn('proyecto_tareas', 'fecha_fin')) {
                    $tareasVencidas = (clone $tareaModel)
                        ->whereNotNull('proyecto_tareas.fecha_fin')
                        ->whereDate('proyecto_tareas.fecha_fin', '<', now()->toDateString())
                        ->whereIn('proyecto_tareas.estado', ['pendiente', 'en_proceso', 'pausada'])
                        ->count();
                }
            }
        } catch (\Throwable $e) {
            $tareasPendientes = 0;
            $tareasVencidas = 0;
        }

        // =========================
        // KPIs FINAL
        // =========================
        $kpis = [
            'almacenes'             => $almacenes,
            'materiales'            => $materiales,
            'stock_total'           => $stockTotal,
            'valor_inventario'      => $valorInventario,
            'proyectos'             => $proyectos,
            'proyectos_activos'     => $proyectosActivos,
            'proyectos_finalizados' => $proyectosFinalizados,
            'tareas_pendientes'     => $tareasPendientes,
            'tareas_vencidas'       => $tareasVencidas,
            'movimientos_total'     => $movimientosTotal,
        ];

        return view('dashboard', [
            'kpis'               => $kpis,
            'ultimosMovs'        => $ultimosMovs,
            'topMateriales'      => $topMateriales,
            'proyectosRecientes' => $proyectosRecientes,
        ]);
    }
}