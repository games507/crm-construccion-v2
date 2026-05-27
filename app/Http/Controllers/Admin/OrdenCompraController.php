<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Almacen;
use App\Models\CuentaPorPagar;
use App\Models\Empresa;
use App\Models\InvExistencia;
use App\Models\InvMovimiento;
use App\Models\Material;
use App\Models\OrdenCompra;
use App\Models\OrdenCompraItem;
use App\Models\Proveedor;
use App\Models\Proyecto;
use App\Models\ProyectoCosto;
use App\Support\EmpresaScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdenCompraController extends Controller
{
    private function empresaIdActual(): ?int
    {
        $empresaId = EmpresaScope::getId();

        if (!$empresaId || (int) $empresaId === 0) {
            $empresaId = auth()->user()->empresa_id ?? null;
        }

        if (!$empresaId || (int) $empresaId === 0) {
            $empresaId = Empresa::query()->orderBy('id')->value('id');
        }

        return $empresaId ? (int) $empresaId : null;
    }

    public function index(Request $request)
    {
        $empresaId = $this->empresaIdActual();

        $q = trim($request->q ?? '');
        $estado = trim($request->estado ?? '');

        $ordenes = OrdenCompra::query()
            ->with(['proveedor', 'proyecto'])
            ->where('empresa_id', $empresaId)
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('numero', 'like', "%{$q}%")
                        ->orWhereHas('proveedor', fn($p) => $p->where('nombre', 'like', "%{$q}%"))
                        ->orWhereHas('proyecto', fn($p) => $p->where('nombre', 'like', "%{$q}%"));
                });
            })
            ->when($estado, fn($query) => $query->where('estado', $estado))
            ->latest('id')
            ->paginate(15);

        return view('admin.ordenes_compra.index', compact('ordenes', 'q', 'estado'));
    }

    public function create()
    {
        $empresaId = $this->empresaIdActual();

        $proveedores = Proveedor::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $proyectos = Proyecto::where('empresa_id', $empresaId)
            ->orderByDesc('id')
            ->get();

        $materiales = Material::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->orderBy('descripcion')
            ->get();

        $almacenes = Almacen::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $numero = $this->generarNumero($empresaId);

        return view('admin.ordenes_compra.create', compact(
            'proveedores',
            'proyectos',
            'materiales',
            'almacenes',
            'numero'
        ));
    }

    public function store(Request $request)
    {
        $empresaId = $this->empresaIdActual();

        $data = $request->validate([
            'proveedor_id' => ['required', 'exists:proveedores,id'],
            'proyecto_id' => ['nullable', 'exists:proyectos,id'],
            'almacen_destino_id' => ['nullable', 'exists:almacenes,id'],
            'numero' => ['required', 'max:40'],
            'fecha' => ['required', 'date'],
            'fecha_entrega' => ['nullable', 'date'],
            'estado' => ['required', 'in:borrador,solicitada,aprobada,recibida,parcial,cancelada'],
            'observacion' => ['nullable', 'max:5000'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.material_id' => ['nullable', 'exists:materiales,id'],
            'items.*.descripcion' => ['required', 'max:255'],
            'items.*.cantidad' => ['required', 'numeric', 'min:0.0001'],
            'items.*.precio_unitario' => ['required', 'numeric', 'min:0'],
            'items.*.impuesto' => ['nullable', 'numeric', 'min:0'],
            'items.*.descuento' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($data, $empresaId) {
            $orden = OrdenCompra::create([
                'empresa_id' => $empresaId,
                'proveedor_id' => $data['proveedor_id'],
                'proyecto_id' => $data['proyecto_id'] ?? null,
                'almacen_destino_id' => $data['almacen_destino_id'] ?? null,
                'numero' => $data['numero'],
                'fecha' => $data['fecha'],
                'fecha_entrega' => $data['fecha_entrega'] ?? null,
                'estado' => $data['estado'],
                'observacion' => $data['observacion'] ?? null,
                'creado_por' => auth()->id(),
            ]);

            foreach ($data['items'] as $item) {
                $cantidad = (float) $item['cantidad'];
                $precio = (float) $item['precio_unitario'];
                $impuesto = (float) ($item['impuesto'] ?? 0);
                $descuento = (float) ($item['descuento'] ?? 0);

                OrdenCompraItem::create([
                    'orden_compra_id' => $orden->id,
                    'material_id' => $item['material_id'] ?? null,
                    'descripcion' => $item['descripcion'],
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio,
                    'impuesto' => $impuesto,
                    'descuento' => $descuento,
                    'total' => (($cantidad * $precio) + $impuesto) - $descuento,
                ]);
            }

            $orden->recalcularTotales();
        });

        return redirect()
            ->route('admin.ordenes_compra.index')
            ->with('ok', 'Orden de compra creada correctamente.');
    }

    public function show(OrdenCompra $ordenCompra)
    {
        $ordenCompra->load([
            'proveedor',
            'proyecto',
            'items.material',
            'creadoPor',
            'aprobadoPor',
        ]);

        return view('admin.ordenes_compra.show', compact('ordenCompra'));
    }

    public function aprobar(OrdenCompra $ordenCompra)
    {
        if ($ordenCompra->estado === 'cancelada') {
            return back()->withErrors(['orden' => 'No puedes aprobar una orden cancelada.']);
        }

        $ordenCompra->update([
            'estado' => 'aprobada',
            'aprobado_por' => auth()->id(),
            'aprobado_en' => now(),
        ]);

        return back()->with('ok', 'Orden aprobada correctamente.');
    }

    public function cancelar(OrdenCompra $ordenCompra)
    {
        if ($ordenCompra->estado === 'recibida') {
            return back()->withErrors(['orden' => 'No puedes cancelar una orden que ya fue recibida.']);
        }

        $ordenCompra->update(['estado' => 'cancelada']);

        return back()->with('ok', 'Orden cancelada correctamente.');
    }

    public function recibir(OrdenCompra $ordenCompra)
    {
        if ($ordenCompra->estado === 'cancelada') {
            return back()->withErrors(['orden' => 'No puedes recibir una orden cancelada.']);
        }

        if ($ordenCompra->estado === 'recibida') {
            return back()->with('ok', 'Esta orden ya fue recibida.');
        }

        if (!$ordenCompra->almacen_destino_id) {
            return back()->withErrors([
                'almacen' => 'Esta orden no tiene almacén destino. Debes seleccionar un almacén antes de recibir.',
            ]);
        }

        DB::transaction(function () use ($ordenCompra) {
            $ordenCompra->load(['items.material', 'proveedor', 'proyecto']);

            $ordenCompra->update([
                'estado' => 'recibida',
            ]);

            foreach ($ordenCompra->items as $item) {
                if (!$item->material_id) {
                    continue;
                }

                $cantidadEntrada = (float) $item->cantidad;
                $costoUnitario = (float) $item->precio_unitario;

                $existencia = InvExistencia::firstOrCreate(
                    [
                        'empresa_id' => $ordenCompra->empresa_id,
                        'material_id' => $item->material_id,
                        'almacen_id' => $ordenCompra->almacen_destino_id,
                    ],
                    [
                        'cantidad' => 0,
                        'stock' => 0,
                        'costo_promedio' => 0,
                    ]
                );

                $stockAnterior = (float) $existencia->stock;
                $costoAnterior = (float) $existencia->costo_promedio;

                $stockNuevo = $stockAnterior + $cantidadEntrada;

                $valorAnterior = $stockAnterior * $costoAnterior;
                $valorEntrada = $cantidadEntrada * $costoUnitario;

                $costoPromedioNuevo = $stockNuevo > 0
                    ? (($valorAnterior + $valorEntrada) / $stockNuevo)
                    : $costoUnitario;

                $existencia->update([
                    'cantidad' => $stockNuevo,
                    'stock' => $stockNuevo,
                    'costo_promedio' => $costoPromedioNuevo,
                ]);

                InvMovimiento::create([
                    'empresa_id' => $ordenCompra->empresa_id,
                    'fecha' => $ordenCompra->fecha,
                    'tipo' => 'entrada',
                    'material_id' => $item->material_id,
                    'almacen_origen_id' => null,
                    'almacen_destino_id' => $ordenCompra->almacen_destino_id,
                    'cantidad' => $cantidadEntrada,
                    'costo_unitario' => $costoUnitario,
                    'referencia' => $ordenCompra->numero,
                    'meta' => json_encode([
                        'origen' => 'orden_compra',
                        'orden_compra_id' => $ordenCompra->id,
                        'orden_compra_item_id' => $item->id,
                        'proveedor' => $ordenCompra->proveedor->nombre ?? null,
                        'descripcion' => $item->descripcion,
                    ], JSON_UNESCAPED_UNICODE),
                ]);
            }

            CuentaPorPagar::updateOrCreate(
                [
                    'proyecto_id' => $ordenCompra->proyecto_id,
                    'proveedor' => $ordenCompra->proveedor->nombre ?? 'Proveedor',
                    'descripcion' => 'Orden de compra ' . $ordenCompra->numero,
                ],
                [
                    'monto_total' => $ordenCompra->total,
                    'monto_pagado' => 0,
                    'saldo' => $ordenCompra->total,
                    'fecha' => $ordenCompra->fecha,
                    'fecha_vencimiento' => $ordenCompra->fecha_entrega,
                    'estado' => 'pendiente',
                ]
            );

            if ($ordenCompra->proyecto_id) {
       ProyectoCosto::updateOrCreate(
    [
        'proyecto_id' => $ordenCompra->proyecto_id,
        'descripcion' => 'Orden de compra ' . $ordenCompra->numero,
    ],
    [
        'fecha' => $ordenCompra->fecha,
        'tipo' => 'materiales',
        'categoria' => 'Compras',
        'proveedor' => $ordenCompra->proveedor->nombre ?? null,
        'monto' => $ordenCompra->total,
        'estado' => 'pendiente',
    ]
);
            }
        });

        return redirect()
            ->route('admin.ordenes_compra.show', $ordenCompra)
            ->with('ok', 'Orden recibida. Se actualizó inventario, cuenta por pagar y costo del proyecto.');
    }

    private function generarNumero(int $empresaId): string
    {
        $year = now()->format('Y');

        $ultimo = OrdenCompra::where('empresa_id', $empresaId)
            ->where('numero', 'like', "OC-{$year}-%")
            ->orderByDesc('id')
            ->value('numero');

        $next = 1;

        if ($ultimo && preg_match('/OC-' . $year . '-(\d+)/', $ultimo, $m)) {
            $next = ((int) $m[1]) + 1;
        }

        return 'OC-' . $year . '-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}