<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Almacen;
use App\Models\InvExistencia;
use App\Models\InvMovimiento;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovimientosController extends Controller
{
    public function index(Request $r)
    {
        $empresaId = (int) (auth()->user()->empresa_id ?? 0);
        if ($empresaId <= 0) abort(403, 'Tu usuario no tiene empresa asignada.');

        $q = trim((string) $r->query('q', ''));

        $query = InvMovimiento::with(['material','almacenOrigen','almacenDestino'])
            ->where('empresa_id', $empresaId);

        // ✅ BUSCADOR (referencia, tipo, material y almacenes)
        if ($q !== '') {
            $query->where(function ($w) use ($q) {

                // referencia / tipo
                $w->where('referencia', 'like', "%{$q}%")
                  ->orWhere('tipo', 'like', "%{$q}%");

                // material: sku/codigo/descripcion
                $w->orWhereHas('material', function ($m) use ($q) {
                    $m->where('sku', 'like', "%{$q}%")
                      ->orWhere('codigo', 'like', "%{$q}%")
                      ->orWhere('descripcion', 'like', "%{$q}%");
                });

                // almacén origen: codigo/nombre
                $w->orWhereHas('almacenOrigen', function ($a) use ($q) {
                    $a->where('codigo', 'like', "%{$q}%")
                      ->orWhere('nombre', 'like', "%{$q}%");
                });

                // almacén destino: codigo/nombre
                $w->orWhereHas('almacenDestino', function ($a) use ($q) {
                    $a->where('codigo', 'like', "%{$q}%")
                      ->orWhere('nombre', 'like', "%{$q}%");
                });
            });
        }

        // ✅ paginación profesional
        $movs = $query
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('inventario.movimientos.index', compact('movs'));
    }

    public function create()
    {
        $empresaId = (int) (auth()->user()->empresa_id ?? 0);
        if ($empresaId <= 0) abort(403, 'Tu usuario no tiene empresa asignada.');

        return view('inventario.movimientos.create', [
            'materiales' => Material::where('empresa_id', $empresaId)->orderBy('descripcion')->get(),
            'almacenes'  => Almacen::where('empresa_id', $empresaId)->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $r)
    {
        $empresaId = (int) (auth()->user()->empresa_id ?? 0);
        if ($empresaId <= 0) abort(403, 'Tu usuario no tiene empresa asignada.');

        $data = $r->validate([
            'fecha' => ['required','date'],
            'tipo'  => ['required','in:entrada,salida,traslado,ajuste'],
            'material_id' => ['required','integer'],
            'almacen_origen_id' => ['nullable','integer'],
            'almacen_destino_id' => ['nullable','integer'],
            'cantidad' => ['required','numeric','min:0.0001'],
            'costo_unitario' => ['nullable','numeric','min:0'],
            'referencia' => ['nullable','string','max:80'],
        ],[
            'tipo.in' => 'El tipo seleccionado no es válido.',
            'fecha.required' => 'La fecha es obligatoria.',
            'material_id.required' => 'Debe seleccionar un material.',
            'cantidad.required' => 'La cantidad es obligatoria.',
        ]);

        // multiempresa
        $data['empresa_id'] = $empresaId;

        // Reglas por tipo
        if ($data['tipo'] === 'entrada' || $data['tipo'] === 'ajuste') {
            if (empty($data['almacen_destino_id'])) {
                return back()->withErrors(['almacen_destino_id' => 'Seleccione almacén destino.'])->withInput();
            }
            $data['almacen_origen_id'] = null;
        }

        if ($data['tipo'] === 'salida') {
            if (empty($data['almacen_origen_id'])) {
                return back()->withErrors(['almacen_origen_id' => 'Seleccione almacén origen.'])->withInput();
            }
            $data['almacen_destino_id'] = null;
        }

        if ($data['tipo'] === 'traslado') {
            if (empty($data['almacen_origen_id']) || empty($data['almacen_destino_id'])) {
                return back()->withErrors(['almacen_origen_id' => 'Seleccione almacén origen y destino.'])->withInput();
            }
            if ((int)$data['almacen_origen_id'] === (int)$data['almacen_destino_id']) {
                return back()->withErrors(['almacen_destino_id' => 'El destino no puede ser igual al origen.'])->withInput();
            }
        }

        // Validar que material y almacenes pertenezcan a la empresa (seguridad multiempresa)
        $matOk = Material::where('id', $data['material_id'])->where('empresa_id', $empresaId)->exists();
        if (!$matOk) return back()->withErrors(['material_id' => 'El material seleccionado no pertenece a tu empresa.'])->withInput();

        if (!empty($data['almacen_origen_id'])) {
            $ok = Almacen::where('id', $data['almacen_origen_id'])->where('empresa_id', $empresaId)->exists();
            if (!$ok) return back()->withErrors(['almacen_origen_id' => 'El almacén origen no pertenece a tu empresa.'])->withInput();
        }

        if (!empty($data['almacen_destino_id'])) {
            $ok = Almacen::where('id', $data['almacen_destino_id'])->where('empresa_id', $empresaId)->exists();
            if (!$ok) return back()->withErrors(['almacen_destino_id' => 'El almacén destino no pertenece a tu empresa.'])->withInput();
        }

        try {
            $self = $this;

            DB::transaction(function () use ($data, $empresaId, $self) {

                // Bloquear stock negativo en salida/traslado
                if (in_array($data['tipo'], ['salida','traslado'], true)) {
                    $cant = (float) $data['cantidad'];
                    $matId = (int) $data['material_id'];
                    $origenId = (int) ($data['almacen_origen_id'] ?? 0);

                    $ex = InvExistencia::where('empresa_id', $empresaId)
                        ->where('material_id', $matId)
                        ->where('almacen_id', $origenId)
                        ->first();

                    $stock = (float) ($ex?->stock ?? 0);

                    if ($cant > $stock + 0.00001) {
                        throw new \RuntimeException("Stock insuficiente. Disponible: " . number_format($stock, 4));
                    }
                }

                // Guardar movimiento
                $mov = InvMovimiento::create($data);

                // Aplicar existencias
                $cant = (float) $mov->cantidad;
                $matId = (int) $mov->material_id;

                if (in_array($mov->tipo, ['entrada','ajuste'], true) && $mov->almacen_destino_id) {
                    $self->sumarStock(
                        $empresaId,
                        $matId,
                        (int)$mov->almacen_destino_id,
                        $cant,
                        (float)($mov->costo_unitario ?? 0)
                    );
                }

                if ($mov->tipo === 'salida' && $mov->almacen_origen_id) {
                    $self->restarStock($empresaId, $matId, (int)$mov->almacen_origen_id, $cant);
                }

                if ($mov->tipo === 'traslado') {
                    $self->restarStock($empresaId, $matId, (int)$mov->almacen_origen_id, $cant);
                    $self->sumarStock(
                        $empresaId,
                        $matId,
                        (int)$mov->almacen_destino_id,
                        $cant,
                        (float)($mov->costo_unitario ?? 0)
                    );
                }
            });

            return redirect()->route('inventario.movimientos')->with('ok', 'Movimiento registrado correctamente.');

        } catch (\Throwable $e) {
            return back()->withErrors(['cantidad' => $e->getMessage()])->withInput();
        }
    }

    private function sumarStock(int $empresaId, int $materialId, int $almacenId, float $cantidad, float $costoUnitario): void
    {
        $ex = InvExistencia::firstOrCreate(
            ['empresa_id'=>$empresaId, 'material_id'=>$materialId, 'almacen_id'=>$almacenId],
            ['stock'=>0, 'costo_promedio'=>0]
        );

        if ($costoUnitario > 0) {
            $stockAnterior = (float)$ex->stock;
            $costoAnterior = (float)$ex->costo_promedio;

            $nuevoStock = $stockAnterior + $cantidad;
            $nuevoCostoProm = $nuevoStock > 0
                ? (($stockAnterior * $costoAnterior) + ($cantidad * $costoUnitario)) / $nuevoStock
                : 0;

            $ex->stock = $nuevoStock;
            $ex->costo_promedio = $nuevoCostoProm;
        } else {
            $ex->stock = (float)$ex->stock + $cantidad;
        }

        $ex->save();
    }

    private function restarStock(int $empresaId, int $materialId, int $almacenId, float $cantidad): void
    {
        $ex = InvExistencia::firstOrCreate(
            ['empresa_id'=>$empresaId, 'material_id'=>$materialId, 'almacen_id'=>$almacenId],
            ['stock'=>0, 'costo_promedio'=>0]
        );

        $ex->stock = (float)$ex->stock - $cantidad;
        $ex->save();
    }
}
