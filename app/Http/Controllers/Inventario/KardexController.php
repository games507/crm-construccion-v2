<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Almacen;
use App\Models\InvMovimiento;
use App\Models\Material;
use Illuminate\Http\Request;

// ✅ Import del scope (super admin selecciona empresa)
use App\Support\EmpresaScope;

class KardexController extends Controller
{
    /**
     * ✅ EMPRESA ACTUAL (PATRÓN PARA COPIAR)
     * - SuperAdmin: EmpresaScope::getId() (sesión)
     * - Usuario normal: auth()->user()->empresa_id
     * - Si no hay empresa -> abort 403
     */
    private function empresaIdOrAbort(): int
    {
        $user = auth()->user();

        $scopeEmpresaId = (int) EmpresaScope::getId();          // super admin (sesión)
        $userEmpresaId  = (int) ($user->empresa_id ?? 0);       // usuario normal

        $empresaId = $scopeEmpresaId > 0 ? $scopeEmpresaId : $userEmpresaId;

        if ($empresaId <= 0) {
            abort(403, 'Seleccione una empresa para continuar.');
        }

        return $empresaId;
    }

    /**
     * Muestra formulario (material + almacén).
     */
    public function index(Request $req)
    {
        // ✅ FIX: ya NO usamos solo auth()->user()->empresa_id
        // porque SuperAdmin no tiene empresa asignada (0) y trabaja con EmpresaScope.
        $empresaId = $this->empresaIdOrAbort();

        $materiales = Material::where('empresa_id', $empresaId)
            ->orderBy('descripcion')
            ->get();

        $almacenes = Almacen::where('empresa_id', $empresaId)
            ->orderBy('nombre')
            ->get();

        return view('inventario.kardex.index', [
            'materiales'  => $materiales,
            'almacenes'   => $almacenes,
            'materialSel' => $req->query('material_id'),
            'almacenSel'  => $req->query('almacen_id'),
            'rows'        => [],
            'totales'     => null,
        ]);
    }

    /**
     * Genera el kardex.
     */
    public function kardexVer(Request $req)
    {
        // ✅ FIX: empresa por scope/usuario
        $empresaId = $this->empresaIdOrAbort();

        // Validación base
        $data = $req->validate([
            'material_id' => ['required', 'integer'],
            'almacen_id'  => ['required', 'integer'],
        ]);

        $materialId = (int) $data['material_id'];
        $almacenId  = (int) $data['almacen_id'];

        /**
         * ✅ Seguridad multiempresa:
         * - Material SOLO de esta empresa
         * - Almacén SOLO de esta empresa
         */
        $material = Material::where('empresa_id', $empresaId)
            ->where('id', $materialId)
            ->firstOrFail();

        Almacen::where('empresa_id', $empresaId)
            ->where('id', $almacenId)
            ->firstOrFail();

        /**
         * Movimientos SOLO de esta empresa + material + almacén involucrado
         */
        $movs = InvMovimiento::where('empresa_id', $empresaId)
            ->where('material_id', $material->id)
            ->where(function ($q) use ($almacenId) {
                $q->where('almacen_origen_id', $almacenId)
                  ->orWhere('almacen_destino_id', $almacenId);
            })
            ->orderBy('fecha')
            ->orderBy('id')
            ->get();

        $rows = [];

        // Entradas/Salidas enteras
        $saldo    = 0.0;  // saldo con decimales (2)
        $entradas = 0;    // entero
        $salidas  = 0;    // entero

        foreach ($movs as $m) {
            $entra = ((int) $m->almacen_destino_id === $almacenId);
            $sale  = ((int) $m->almacen_origen_id === $almacenId);

            // cantidad como ENTERO (sin decimales)
            $cant = (int) round((float) $m->cantidad, 0);

            $entrada = $entra ? $cant : 0;
            $salida  = $sale  ? $cant : 0;

            // saldo en 2 decimales (aunque entrada/salida sea entero)
            $saldo = round($saldo + $entrada - $salida, 2);

            $entradas += $entrada;
            $salidas  += $salida;

            $rows[] = [
                'fecha'   => (string) $m->fecha,
                'tipo'    => (string) $m->tipo,
                'entrada' => $entrada,
                'salida'  => $salida,
                'saldo'   => $saldo,
                'ref'     => (string) ($m->referencia ?? ''),
            ];
        }

        $totales = [
            'entradas' => (int) $entradas,
            'salidas'  => (int) $salidas,
            'saldo'    => round((float) $saldo, 2),
            'material' => $material->descripcion,
        ];

        return view('inventario.kardex.index', [
            'materiales'  => Material::where('empresa_id', $empresaId)->orderBy('descripcion')->get(),
            'almacenes'   => Almacen::where('empresa_id', $empresaId)->orderBy('nombre')->get(),
            'materialSel' => $material->id,
            'almacenSel'  => $almacenId,
            'rows'        => $rows,
            'totales'     => $totales,
        ]);
    }
}
