<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Almacen;
use App\Models\InvExistencia;
use App\Models\InvMovimiento;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// IMPORTANTE: empresa por sesión (SuperAdmin) o por usuario (empresa normal)
use App\Support\EmpresaScope;

class MovimientosController extends Controller
{
    /**
     * EMPRESA ACTUAL (FIX)
     *
     * Copia este método en otros controllers:
     * - Primero intenta EmpresaScope::getId() (cuando SuperAdmin eligió empresa)
     * - Si no hay, usa auth()->user()->empresa_id (usuarios normales)
     * - Si ninguna existe => 403 con mensaje claro
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

    public function index(Request $r)
    {
        // FIX aplicado aquí
        $empresaId = $this->empresaIdOrAbort();

        $q = trim((string) $r->query('q', ''));

        $query = InvMovimiento::with(['material','almacenOrigen','almacenDestino'])
            ->where('empresa_id', $empresaId);

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('referencia', 'like', "%{$q}%")
                  ->orWhere('tipo', 'like', "%{$q}%")
                  ->orWhereHas('material', fn($m) =>
                      $m->where('sku','like',"%{$q}%")
                        ->orWhere('codigo','like',"%{$q}%")
                        ->orWhere('descripcion','like',"%{$q}%")
                  )
                  ->orWhereHas('almacenOrigen', fn($a) =>
                      $a->where('codigo','like',"%{$q}%")
                        ->orWhere('nombre','like',"%{$q}%")
                  )
                  ->orWhereHas('almacenDestino', fn($a) =>
                      $a->where('codigo','like',"%{$q}%")
                        ->orWhere('nombre','like',"%{$q}%")
                  );
            });
        }

        $movs = $query
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('inventario.movimientos.index', compact('movs'));
    }

    public function create()
    {
        // FIX aplicado aquí
        $empresaId = $this->empresaIdOrAbort();

        return view('inventario.movimientos.create', [
            'materiales' => Material::where('empresa_id',$empresaId)->orderBy('descripcion')->get(),
            'almacenes'  => Almacen::where('empresa_id',$empresaId)->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $r)
    {
        // FIX aplicado aquí
        $empresaId = $this->empresaIdOrAbort();

        // VALIDACIÓN PROFESIONAL (cantidad ENTERA)
        $data = $r->validate([
            'fecha' => ['required','date'],
            'tipo'  => ['required','in:entrada,salida,traslado,ajuste'],
            'material_id' => ['required','integer'],
            'almacen_origen_id' => ['nullable','integer'],
            'almacen_destino_id' => ['nullable','integer'],
            'cantidad' => ['required','integer','min:1'],
            'costo_unitario' => ['nullable','numeric','min:0'],
            'referencia' => ['nullable','string','max:80'],
        ],[
            'cantidad.integer' => 'La cantidad debe ser un número entero (sin decimales).',
            'cantidad.min' => 'La cantidad debe ser mayor a cero.',
        ]);

        $data['empresa_id'] = $empresaId;

        // Reglas por tipo
        if (in_array($data['tipo'], ['entrada','ajuste'], true)) {
            if (empty($data['almacen_destino_id'])) {
                return back()->withErrors(['almacen_destino_id'=>'Seleccione almacén destino.'])->withInput();
            }
            $data['almacen_origen_id'] = null;
        }

        if ($data['tipo'] === 'salida') {
            if (empty($data['almacen_origen_id'])) {
                return back()->withErrors(['almacen_origen_id'=>'Seleccione almacén origen.'])->withInput();
            }
            $data['almacen_destino_id'] = null;
        }

        if ($data['tipo'] === 'traslado') {
            if (empty($data['almacen_origen_id']) || empty($data['almacen_destino_id'])) {
                return back()->withErrors(['almacen_origen_id'=>'Seleccione almacén origen y destino.'])->withInput();
            }
            if ((int)$data['almacen_origen_id'] === (int)$data['almacen_destino_id']) {
                return back()->withErrors(['almacen_destino_id'=>'El destino no puede ser igual al origen.'])->withInput();
            }
        }

        // Seguridad multiempresa (la empresa ya viene resuelta por Scope/User)
        Material::where('empresa_id',$empresaId)->where('id',$data['material_id'])->firstOrFail();
        if (!empty($data['almacen_origen_id'])) {
            Almacen::where('empresa_id',$empresaId)->where('id',$data['almacen_origen_id'])->firstOrFail();
        }
        if (!empty($data['almacen_destino_id'])) {
            Almacen::where('empresa_id',$empresaId)->where('id',$data['almacen_destino_id'])->firstOrFail();
        }

        try {
            DB::transaction(function () use ($data, $empresaId) {

                $cant = (int) $data['cantidad'];

                // Bloquear stock negativo
                if (in_array($data['tipo'], ['salida','traslado'], true)) {
                    $ex = InvExistencia::where('empresa_id',$empresaId)
                        ->where('material_id',$data['material_id'])
                        ->where('almacen_id',$data['almacen_origen_id'])
                        ->first();

                    $stock = (int) ($ex?->stock ?? 0);

                    if ($cant > $stock) {
                        throw new \RuntimeException("Stock insuficiente. Disponible: {$stock}");
                    }
                }

                // Guardar movimiento (cantidad ENTERA)
                $data['cantidad'] = $cant;
                $mov = InvMovimiento::create($data);

                if (in_array($mov->tipo,['entrada','ajuste'],true)) {
                    $this->sumarStock(
                        $empresaId,
                        (int)$mov->material_id,
                        (int)$mov->almacen_destino_id,
                        $cant,
                        (float)($mov->costo_unitario ?? 0)
                    );
                }

                if ($mov->tipo === 'salida') {
                    $this->restarStock(
                        $empresaId,
                        (int)$mov->material_id,
                        (int)$mov->almacen_origen_id,
                        $cant
                    );
                }

                if ($mov->tipo === 'traslado') {
                    $this->restarStock(
                        $empresaId,
                        (int)$mov->material_id,
                        (int)$mov->almacen_origen_id,
                        $cant
                    );
                    $this->sumarStock(
                        $empresaId,
                        (int)$mov->material_id,
                        (int)$mov->almacen_destino_id,
                        $cant,
                        (float)($mov->costo_unitario ?? 0)
                    );
                }
            });

            return redirect()->route('inventario.movimientos')
                ->with('ok','Movimiento registrado correctamente.');

        } catch (\Throwable $e) {
            return back()->withErrors(['cantidad'=>$e->getMessage()])->withInput();
        }
    }

    private function sumarStock(int $empresaId, int $materialId, int $almacenId, int $cantidad, float $costoUnitario): void
    {
        $ex = InvExistencia::firstOrCreate(
            ['empresa_id'=>$empresaId,'material_id'=>$materialId,'almacen_id'=>$almacenId],
            ['stock'=>0,'costo_promedio'=>0]
        );

        if ($costoUnitario > 0) {
            $stockAnt = (int)$ex->stock;
            $costoAnt = (float)$ex->costo_promedio;

            $nuevoStock = $stockAnt + $cantidad;
            $nuevoCosto = $nuevoStock > 0
                ? (($stockAnt * $costoAnt) + ($cantidad * $costoUnitario)) / $nuevoStock
                : 0;

            $ex->stock = $nuevoStock;
            $ex->costo_promedio = round($nuevoCosto, 4);
        } else {
            $ex->stock = (int)$ex->stock + $cantidad;
        }

        $ex->save();
    }

    private function restarStock(int $empresaId, int $materialId, int $almacenId, int $cantidad): void
    {
        $ex = InvExistencia::firstOrCreate(
            ['empresa_id'=>$empresaId,'material_id'=>$materialId,'almacen_id'=>$almacenId],
            ['stock'=>0,'costo_promedio'=>0]
        );

        $ex->stock = (int)$ex->stock - $cantidad;
        $ex->save();
    }
}
