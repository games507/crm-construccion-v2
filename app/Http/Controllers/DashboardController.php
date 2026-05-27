<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\CuentaPorCobrar;
use App\Models\CuentaPorPagar;
use App\Models\InvExistencia;
use App\Models\InvMovimiento;
use App\Models\Material;
use App\Models\Proyecto;
use App\Models\ProyectoCosto;
use App\Models\ProyectoTarea;
use App\Models\User;
use App\Support\EmpresaScope;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();

        $isSuperAdmin = false;

        if ($user) {
            if (method_exists($user, 'hasRole')) {
                $isSuperAdmin = $user->hasRole('SuperAdmin') || $user->hasRole('Super Admin');
            } elseif (isset($user->is_superadmin)) {
                $isSuperAdmin = (bool) $user->is_superadmin;
            }
        }

        $empresaId = $isSuperAdmin ? EmpresaScope::getId() : ($user->empresa_id ?? null);

        if ($isSuperAdmin && !$empresaId) {
            return view('dashboard', $this->emptyData());
        }

        if (!$isSuperAdmin && !$empresaId) {
            abort(403, 'Tu usuario no tiene empresa asignada.');
        }

        /*
        |--------------------------------------------------------------------------
        | INVENTARIO
        |--------------------------------------------------------------------------
        */
        $almacenes = Almacen::where('empresa_id', $empresaId)->count();
        $materiales = Material::where('empresa_id', $empresaId)->count();

        $existenciaCantidadColumn = Schema::hasColumn('inv_existencias', 'stock')
            ? 'stock'
            : 'cantidad';

        $stockTotal = (float) InvExistencia::where('empresa_id', $empresaId)
            ->sum($existenciaCantidadColumn);

        $valorInventario = 0;

        if (Schema::hasColumn('inv_existencias', 'costo_promedio')) {
            $valorInventario = (float) InvExistencia::where('empresa_id', $empresaId)
                ->selectRaw("COALESCE(SUM({$existenciaCantidadColumn} * costo_promedio),0) as total")
                ->value('total');
        }

        $movimientosTotal = InvMovimiento::where('empresa_id', $empresaId)->count();

        $ultimosMovs = InvMovimiento::where('empresa_id', $empresaId)
            ->with(['material', 'almacenOrigen', 'almacenDestino'])
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $topMateriales = collect();

        try {
            $topMateriales = InvExistencia::where('inv_existencias.empresa_id', $empresaId)
                ->join('materiales', 'materiales.id', '=', 'inv_existencias.material_id')
                ->selectRaw("
                    materiales.codigo,
                    materiales.descripcion,
                    SUM(inv_existencias.{$existenciaCantidadColumn}) as stock,
                    SUM(inv_existencias.{$existenciaCantidadColumn} * COALESCE(inv_existencias.costo_promedio,0)) as valor
                ")
                ->groupBy('materiales.codigo', 'materiales.descripcion')
                ->orderByDesc('valor')
                ->limit(6)
                ->get();
        } catch (\Throwable $e) {
            $topMateriales = collect();
        }

        /*
        |--------------------------------------------------------------------------
        | PROYECTOS
        |--------------------------------------------------------------------------
        */
        $proyectosBase = Proyecto::where('empresa_id', $empresaId);

        $proyectos = (clone $proyectosBase)->count();

        $proyectosActivos = (clone $proyectosBase)
            ->whereIn('estado', ['planeado', 'en_ejecucion', 'pausado'])
            ->count();

        $proyectosFinalizados = (clone $proyectosBase)
            ->where('estado', 'finalizado')
            ->count();

        $proyectosEnEjecucion = (clone $proyectosBase)
            ->where('estado', 'en_ejecucion')
            ->count();

        $presupuestoTotal = (float) (clone $proyectosBase)->sum('presupuesto');

        $avancePromedio = (float) (clone $proyectosBase)->avg('porcentaje');

        $proyectosRecientes = Proyecto::where('empresa_id', $empresaId)
            ->with('responsable')
            ->latest('id')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | TAREAS
        |--------------------------------------------------------------------------
        */
        $tareasBase = ProyectoTarea::query()
            ->join('proyectos', 'proyectos.id', '=', 'proyecto_tareas.proyecto_id')
            ->where('proyectos.empresa_id', $empresaId);

        $tareasPendientes = (clone $tareasBase)
            ->whereIn('proyecto_tareas.estado', ['pendiente', 'en_proceso', 'pausada'])
            ->count();

        $tareasFinalizadas = (clone $tareasBase)
            ->where('proyecto_tareas.estado', 'finalizada')
            ->count();

        $tareasVencidas = (clone $tareasBase)
            ->whereNotNull('proyecto_tareas.fecha_fin')
            ->whereDate('proyecto_tareas.fecha_fin', '<', now()->toDateString())
            ->whereIn('proyecto_tareas.estado', ['pendiente', 'en_proceso', 'pausada'])
            ->count();

        $tareasRecientes = ProyectoTarea::query()
            ->with(['proyecto', 'responsable'])
            ->whereHas('proyecto', function ($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            })
            ->latest('id')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | COSTOS / FINANZAS
        |--------------------------------------------------------------------------
        */
        $costosTotal = 0;

        if (Schema::hasTable('proyecto_costos')) {
            $costosTotal = (float) ProyectoCosto::query()
                ->join('proyectos', 'proyectos.id', '=', 'proyecto_costos.proyecto_id')
                ->where('proyectos.empresa_id', $empresaId)
                ->sum('proyecto_costos.monto');
        }

        $cuentasPorPagarSaldo = 0;
        $cuentasPorPagarVencidas = 0;

        if (Schema::hasTable('cuentas_por_pagar')) {
            $cuentasPorPagarSaldo = (float) CuentaPorPagar::query()
                ->join('proyectos', 'proyectos.id', '=', 'cuentas_por_pagar.proyecto_id')
                ->where('proyectos.empresa_id', $empresaId)
                ->sum('cuentas_por_pagar.saldo');

            $cuentasPorPagarVencidas = CuentaPorPagar::query()
                ->join('proyectos', 'proyectos.id', '=', 'cuentas_por_pagar.proyecto_id')
                ->where('proyectos.empresa_id', $empresaId)
                ->whereNotNull('cuentas_por_pagar.fecha_vencimiento')
                ->whereDate('cuentas_por_pagar.fecha_vencimiento', '<', now()->toDateString())
                ->whereIn('cuentas_por_pagar.estado', ['pendiente', 'parcial'])
                ->count();
        }

        $cuentasPorCobrarSaldo = 0;
        $cuentasPorCobrarVencidas = 0;

        if (Schema::hasTable('cuentas_por_cobrar')) {
            $cuentasPorCobrarSaldo = (float) CuentaPorCobrar::query()
                ->join('proyectos', 'proyectos.id', '=', 'cuentas_por_cobrar.proyecto_id')
                ->where('proyectos.empresa_id', $empresaId)
                ->sum('cuentas_por_cobrar.saldo');

            $cuentasPorCobrarVencidas = CuentaPorCobrar::query()
                ->join('proyectos', 'proyectos.id', '=', 'cuentas_por_cobrar.proyecto_id')
                ->where('proyectos.empresa_id', $empresaId)
                ->whereNotNull('cuentas_por_cobrar.fecha_vencimiento')
                ->whereDate('cuentas_por_cobrar.fecha_vencimiento', '<', now()->toDateString())
                ->whereIn('cuentas_por_cobrar.estado', ['pendiente', 'parcial'])
                ->count();
        }

        /*
        |--------------------------------------------------------------------------
        | EQUIPO ACTIVO
        |--------------------------------------------------------------------------
        */
$equipoActivo = User::query()
    ->where('empresa_id', $empresaId)
    ->whereDoesntHave('roles', function ($q) {
        $q->whereIn('name', ['SuperAdmin', 'Super Admin']);
    })
    ->latest('id')
    ->limit(4)
    ->get();

        /*
        |--------------------------------------------------------------------------
        | KPI FINAL
        |--------------------------------------------------------------------------
        */
        $kpis = [
            'almacenes'                  => $almacenes,
            'materiales'                 => $materiales,
            'stock_total'                => $stockTotal,
            'valor_inventario'           => $valorInventario,
            'movimientos_total'          => $movimientosTotal,

            'proyectos'                  => $proyectos,
            'proyectos_activos'          => $proyectosActivos,
            'proyectos_en_ejecucion'     => $proyectosEnEjecucion,
            'proyectos_finalizados'      => $proyectosFinalizados,
            'presupuesto_total'          => $presupuestoTotal,
            'avance_promedio'            => $avancePromedio,

            'tareas_pendientes'          => $tareasPendientes,
            'tareas_finalizadas'         => $tareasFinalizadas,
            'tareas_vencidas'            => $tareasVencidas,

            'costos_total'               => $costosTotal,
            'cuentas_por_pagar_saldo'    => $cuentasPorPagarSaldo,
            'cuentas_por_pagar_vencidas' => $cuentasPorPagarVencidas,
            'cuentas_por_cobrar_saldo'   => $cuentasPorCobrarSaldo,
            'cuentas_por_cobrar_vencidas'=> $cuentasPorCobrarVencidas,
        ];

        return view('dashboard', [
            'kpis'               => $kpis,
            'ultimosMovs'        => $ultimosMovs,
            'topMateriales'      => $topMateriales,
            'proyectosRecientes' => $proyectosRecientes,
            'tareasRecientes'    => $tareasRecientes,
            'equipoActivo'       => $equipoActivo,
        ]);
    }

    private function emptyData(): array
    {
        return [
            'kpis' => [
                'almacenes'                  => 0,
                'materiales'                 => 0,
                'stock_total'                => 0,
                'valor_inventario'           => 0,
                'movimientos_total'          => 0,

                'proyectos'                  => 0,
                'proyectos_activos'          => 0,
                'proyectos_en_ejecucion'     => 0,
                'proyectos_finalizados'      => 0,
                'presupuesto_total'          => 0,
                'avance_promedio'            => 0,

                'tareas_pendientes'          => 0,
                'tareas_finalizadas'         => 0,
                'tareas_vencidas'            => 0,

                'costos_total'               => 0,
                'cuentas_por_pagar_saldo'    => 0,
                'cuentas_por_pagar_vencidas' => 0,
                'cuentas_por_cobrar_saldo'   => 0,
                'cuentas_por_cobrar_vencidas'=> 0,
            ],
            'ultimosMovs'        => collect(),
            'topMateriales'      => collect(),
            'proyectosRecientes' => collect(),
            'tareasRecientes'    => collect(),
            'equipoActivo'       => collect(),
        ];
    }
}