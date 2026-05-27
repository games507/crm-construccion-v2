@extends('layouts.base')
@section('title','Órdenes de compra')

@section('content')
@php
  $money = fn($v) => '$' . number_format((float)$v, 2);

  $estadoClass = function($estado) {
    return match($estado) {
      'borrador' => 'b-slate',
      'solicitada' => 'b-blue',
      'aprobada' => 'b-green',
      'recibida' => 'b-cyan',
      'parcial' => 'b-yellow',
      'cancelada' => 'b-red',
      default => 'b-slate',
    };
  };
@endphp

<style>
  .vs-wrap{max-width:1400px;margin:0 auto;padding:18px}
  .vs-head{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:18px}
  .vs-title{font-size:28px;font-weight:950;color:#0f172a;line-height:1}
  .vs-sub{margin-top:6px;font-size:13px;color:#64748b;font-weight:700}
  .vs-btn{height:44px;border:none;border-radius:16px;padding:0 18px;display:inline-flex;align-items:center;gap:10px;background:linear-gradient(135deg,#0f172a,#0b4f7d);color:white;font-weight:900;text-decoration:none;box-shadow:0 12px 30px rgba(15,23,42,.15);transition:.2s ease}
  .vs-btn:hover{transform:translateY(-2px);color:white}
  .panel{background:white;border-radius:28px;border:1px solid #e2e8f0;box-shadow:0 18px 50px rgba(15,23,42,.07);overflow:hidden}
  .toolbar{padding:18px;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;gap:14px;flex-wrap:wrap}
  .search,.select{height:46px;border-radius:16px;border:1px solid #dbe2ea;padding:0 16px;font-weight:700;outline:none;background:white}
  .search{width:320px;max-width:100%}
  .filters{display:flex;gap:10px;flex-wrap:wrap}
  .table-wrap{overflow:auto}
  table{width:100%;border-collapse:collapse}
  thead{background:#f8fafc}
  th{padding:16px;text-align:left;font-size:12px;text-transform:uppercase;letter-spacing:.08em;color:#64748b;font-weight:900;white-space:nowrap}
  td{padding:16px;border-top:1px solid #edf2f7;vertical-align:middle}
  tr:hover{background:#fafcff}
  .name{font-weight:950;color:#0f172a}
  .muted{color:#64748b;font-size:12px;font-weight:700;margin-top:4px}
  .badge{display:inline-flex;padding:6px 12px;border-radius:999px;font-size:11px;font-weight:950}
  .b-green{background:#dcfce7;color:#166534}
  .b-red{background:#fee2e2;color:#991b1b}
  .b-blue{background:#dbeafe;color:#1d4ed8}
  .b-yellow{background:#fef3c7;color:#92400e}
  .b-slate{background:#e2e8f0;color:#334155}
  .b-cyan{background:#cffafe;color:#155e75}
  .actions{display:flex;gap:8px;justify-content:flex-end}
  .btn-icon{width:38px;height:38px;border:none;border-radius:14px;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:.2s ease;text-decoration:none}
  .btn-icon:hover{transform:translateY(-2px)}
  .btn-view{background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe}
  .empty{padding:40px;text-align:center;color:#64748b;font-weight:800}
  .ok{margin-bottom:14px;padding:14px 18px;border-radius:16px;background:#dcfce7;color:#166534;font-weight:900;border:1px solid #bbf7d0}
</style>

<div class="vs-wrap">

  @if(session('ok'))
    <div class="ok">{{ session('ok') }}</div>
  @endif

  <div class="vs-head">
    <div>
      <div class="vs-title">Órdenes de compra</div>
      <div class="vs-sub">Control de compras, proveedores, proyectos y materiales.</div>
    </div>

    <a href="{{ route('admin.ordenes_compra.create') }}" class="vs-btn">
      Nueva orden
    </a>
  </div>

  <div class="panel">
    <div class="toolbar">
      <form class="filters" method="GET">
        <input type="text" name="q" value="{{ $q }}" class="search" placeholder="Buscar orden, proveedor o proyecto...">

        <select name="estado" class="select" onchange="this.form.submit()">
          <option value="">Todos los estados</option>
          @foreach(['borrador','solicitada','aprobada','recibida','parcial','cancelada'] as $e)
            <option value="{{ $e }}" @selected($estado === $e)>
              {{ ucfirst($e) }}
            </option>
          @endforeach
        </select>

        <button class="vs-btn" style="height:46px">Filtrar</button>
      </form>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Orden</th>
            <th>Proveedor</th>
            <th>Proyecto</th>
            <th>Fecha</th>
            <th>Estado</th>
            <th>Total</th>
            <th width="90"></th>
          </tr>
        </thead>

        <tbody>
          @forelse($ordenes as $oc)
            <tr>
              <td>
                <div class="name">{{ $oc->numero }}</div>
                <div class="muted">Creada {{ $oc->created_at?->format('d/m/Y') }}</div>
              </td>

              <td>
                <div class="name">{{ $oc->proveedor->nombre ?? '—' }}</div>
                <div class="muted">{{ $oc->proveedor->ruc ?? 'Sin RUC' }}</div>
              </td>

              <td>
                <div class="name">{{ $oc->proyecto->nombre ?? 'Sin proyecto' }}</div>
                <div class="muted">{{ $oc->proyecto->codigo ?? '—' }}</div>
              </td>

              <td>
                <div class="name">{{ $oc->fecha?->format('d/m/Y') }}</div>
                <div class="muted">
                  Entrega: {{ $oc->fecha_entrega ? $oc->fecha_entrega->format('d/m/Y') : '—' }}
                </div>
              </td>

              <td>
                <span class="badge {{ $estadoClass($oc->estado) }}">
                  {{ ucfirst($oc->estado) }}
                </span>
              </td>

              <td>
                <div class="name">{{ $money($oc->total) }}</div>
              </td>

              <td>
                <div class="actions">
                  <a href="{{ route('admin.ordenes_compra.show', $oc) }}" class="btn-icon btn-view" title="Ver detalle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Z"/>
                      <circle cx="12" cy="12" r="3"/>
                    </svg>
                  </a>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7">
                <div class="empty">No hay órdenes de compra registradas.</div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div style="padding:18px">
      {{ $ordenes->links() }}
    </div>
  </div>
</div>
@endsection