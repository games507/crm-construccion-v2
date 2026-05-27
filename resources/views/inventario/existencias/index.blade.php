@extends('layouts.base')
@section('title','Existencias')

@section('content')
@php
  $icon = function($name){
    if($name==='plus') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 5v14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='search') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='x') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='box') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" stroke="currentColor" stroke-width="2"/><path d="M3.3 7L12 12l8.7-5" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M12 22V12" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='warehouse') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M22 8.35V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8.35A2 2 0 0 1 3.26 6.5l8-3.2a2 2 0 0 1 1.48 0l8 3.2A2 2 0 0 1 22 8.35Z" stroke="currentColor" stroke-width="2"/><path d="M6 18h12M6 14h12M6 10h12" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='money') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="3" y="6" width="18" height="12" rx="2" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="2"/></svg>';
    return '';
  };

  $almacenSel = (int)($almacenId ?? 0);
  $qVal = trim((string)($q ?? ''));

  $totalRegistros = 0;
  if (is_object($existencias) && method_exists($existencias, 'total')) {
    $totalRegistros = (int) $existencias->total();
  } elseif (is_iterable($existencias)) {
    $totalRegistros = (int) count($existencias);
  }

  $stockPagina = 0;
  $valorPagina = 0;

  foreach($existencias as $e){
    $stockPagina += (float)($e->stock ?? 0);
    $valorPagina += ((float)($e->stock ?? 0) * (float)($e->costo_promedio ?? 0));
  }
@endphp

<style>
.vs-wrap{max-width:1450px;margin:0 auto;padding:18px}
.vs-head{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:18px}
.vs-title{font-size:30px;font-weight:950;color:#0f172a;line-height:1}
.vs-sub{margin-top:7px;font-size:13px;color:#64748b;font-weight:700}
.vs-actions{display:flex;gap:10px;flex-wrap:wrap}

.btn{
  height:44px;border:none;border-radius:16px;padding:0 18px;
  display:inline-flex;align-items:center;justify-content:center;gap:10px;
  font-weight:900;text-decoration:none;cursor:pointer;transition:.2s ease;
}
.btn:hover{transform:translateY(-2px)}
.btn-primary{background:linear-gradient(135deg,#0f172a,#0b4f7d);color:white;box-shadow:0 12px 30px rgba(15,23,42,.15)}
.btn-primary:hover{color:white}
.btn-light{background:#f1f5f9;color:#334155;border:1px solid #e2e8f0}
.btn-light:hover{color:#334155;background:#e2e8f0}

.kpi-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:18px}
@media(max-width:900px){.kpi-grid{grid-template-columns:1fr}}
.kpi{
  background:white;border-radius:24px;border:1px solid #e2e8f0;padding:18px;
  box-shadow:0 14px 40px rgba(15,23,42,.06);
}
.kpi-top{display:flex;align-items:center;justify-content:space-between;gap:12px}
.kpi-label{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#64748b;font-weight:950}
.kpi-value{margin-top:9px;font-size:28px;line-height:1;font-weight:950;color:#0f172a}
.ico{
  width:46px;height:46px;border-radius:18px;display:flex;align-items:center;justify-content:center;
  background:#dbeafe;color:#1d4ed8;
}
.ico-green{background:#dcfce7;color:#047857}
.ico-indigo{background:#e0e7ff;color:#4338ca}

.panel{
  background:white;border-radius:28px;border:1px solid #e2e8f0;
  box-shadow:0 18px 50px rgba(15,23,42,.07);
  overflow:hidden;margin-bottom:18px;
}
.panel-head{padding:18px;border-bottom:1px solid #e2e8f0}
.panel-title{font-weight:950;color:#0f172a;font-size:16px}
.panel-sub{font-size:12px;color:#64748b;margin-top:4px;font-weight:700}
.panel-body{padding:18px}

.filter-grid{display:grid;grid-template-columns:repeat(12,1fr);gap:14px}
.f4{grid-column:span 4}.f6{grid-column:span 6}.f2{grid-column:span 2}
@media(max-width:1000px){.f4,.f6,.f2{grid-column:span 12}}

.field label{display:block;margin-bottom:7px;font-size:12px;font-weight:900;color:#334155;text-transform:uppercase;letter-spacing:.06em}
.field input,.field select{
  width:100%;height:46px;border:1px solid #dbe2ea;border-radius:16px;
  padding:0 14px;font-weight:750;outline:none;background:white;color:#0f172a;
}
.field input{padding-left:42px}
.field input:focus,.field select:focus{border-color:#38bdf8;box-shadow:0 0 0 4px rgba(14,165,233,.12)}
.search-box{position:relative}
.search-box span{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8}

.alert-ok{margin-bottom:18px;border-radius:18px;border:1px solid #bbf7d0;background:#dcfce7;color:#166534;padding:14px 16px;font-weight:900}
.alert-error{margin-bottom:18px;border-radius:18px;border:1px solid #fecaca;background:#fee2e2;color:#991b1b;padding:14px 16px;font-weight:900}

.table-wrap{overflow:auto;max-height:650px}
.table-wrap::-webkit-scrollbar{width:8px;height:8px}
.table-wrap::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:999px}
table{width:100%;min-width:1180px;border-collapse:collapse}
thead{background:#f8fafc;position:sticky;top:0;z-index:10}
th{padding:15px 14px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#64748b;font-weight:950;white-space:nowrap}
td{padding:14px;border-top:1px solid #edf2f7;vertical-align:middle;font-size:13px}
tr:hover{background:#fafcff}
.strong{font-weight:950;color:#0f172a}
.muted{color:#64748b;font-size:12px;font-weight:700}
.num{text-align:right;font-weight:950;white-space:nowrap}
.money{color:#4f46e5}
.stock-badge{
  display:inline-flex;align-items:center;justify-content:center;
  padding:7px 12px;border-radius:999px;font-size:11px;font-weight:950;border:1px solid #bbf7d0;
  background:#dcfce7;color:#166534;
}
.pagination-wrap{padding:18px;border-top:1px solid #e2e8f0;background:#fff}
.empty{padding:42px;text-align:center;color:#64748b;font-weight:800}
</style>

<div class="vs-wrap">

  <div class="vs-head">
    <div>
      <div class="vs-title">Existencias</div>
      <div class="vs-sub">Consulta de stock por almacén, material, unidad y costo promedio.</div>
    </div>

    <div class="vs-actions">
      @can('inventario.crear')
        <a href="{{ route('inventario.movimientos.create') }}" class="btn btn-primary">
          {!! $icon('plus') !!}
          Agregar movimiento
        </a>
      @endcan
    </div>
  </div>

  @if (session('ok'))
    <div class="alert-ok">{{ session('ok') }}</div>
  @endif

  @if ($errors->any())
    <div class="alert-error">
      @foreach($errors->all() as $e)
        <div>{{ $e }}</div>
      @endforeach
    </div>
  @endif

  <div class="kpi-grid">
    <div class="kpi">
      <div class="kpi-top">
        <div>
          <div class="kpi-label">Registros</div>
          <div class="kpi-value">{{ number_format($totalRegistros) }}</div>
        </div>
        <div class="ico">{!! $icon('box') !!}</div>
      </div>
    </div>

    <div class="kpi">
      <div class="kpi-top">
        <div>
          <div class="kpi-label">Stock visible</div>
          <div class="kpi-value">{{ number_format($stockPagina, 2) }}</div>
        </div>
        <div class="ico ico-green">{!! $icon('warehouse') !!}</div>
      </div>
    </div>

    <div class="kpi">
      <div class="kpi-top">
        <div>
          <div class="kpi-label">Valor visible</div>
          <div class="kpi-value">${{ number_format($valorPagina, 2) }}</div>
        </div>
        <div class="ico ico-indigo">{!! $icon('money') !!}</div>
      </div>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head">
      <div>
        <div class="panel-title">Filtros de consulta</div>
        <div class="panel-sub">Filtra por almacén o busca por código, SKU o descripción.</div>
      </div>
    </div>

    <div class="panel-body">
      <form method="GET" action="{{ route('inventario.existencias') }}">
        <div class="filter-grid">
          <div class="field f4">
            <label>Almacén</label>
            <select name="almacen_id">
              <option value="0">Todos los almacenes</option>
              @foreach($almacenes as $a)
                <option value="{{ $a->id }}" @selected((int)$a->id === $almacenSel)>
                  {{ $a->codigo }} — {{ $a->nombre }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="field f6">
            <label>Buscar</label>
            <div class="search-box">
              <span>{!! $icon('search') !!}</span>
              <input
                name="q"
                value="{{ $qVal }}"
                placeholder="Buscar por código, SKU o descripción..."
              >
            </div>
          </div>

          <div class="field f2" style="display:flex;align-items:end;gap:10px">
            <button type="submit" class="btn btn-primary" style="width:100%">
              Buscar
            </button>
          </div>
        </div>

        @if($almacenSel > 0 || $qVal !== '')
          <div style="margin-top:14px;display:flex;justify-content:flex-end">
            <a href="{{ route('inventario.existencias') }}" class="btn btn-light">
              {!! $icon('x') !!}
              Limpiar filtros
            </a>
          </div>
        @endif
      </form>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head">
      <div>
        <div class="panel-title">Detalle de existencias</div>
        <div class="panel-sub">Listado paginado de materiales disponibles por almacén.</div>
      </div>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Almacén</th>
            <th>Material</th>
            <th>Unidad</th>
            <th class="num">Cantidad</th>
            <th class="num">Costo prom.</th>
            <th class="num">Valor</th>
          </tr>
        </thead>

        <tbody>
          @forelse($existencias as $e)
            @php
              $a = $e->almacen;
              $m = $e->material;

              $matCode = $m->codigo ?? $m->sku ?? '—';
              $matDesc = $m->descripcion ?? '—';

              $unidadTxt = '';
              if(isset($m->unidadRef) && $m->unidadRef){
                $unidadTxt = trim(($m->unidadRef->codigo ?? '') . ' - ' . ($m->unidadRef->descripcion ?? ''), ' -');
              }
              if($unidadTxt === ''){
                $unidadTxt = $m->unidad ?? '—';
              }

              $stock = (float)($e->stock ?? 0);
              $costo = (float)($e->costo_promedio ?? 0);
              $valor = $stock * $costo;
            @endphp

            <tr>
              <td>
                <div class="strong">{{ $a->codigo ?? '—' }}</div>
                <div class="muted">{{ $a->nombre ?? '—' }}</div>
              </td>

              <td>
                <div class="strong">{{ $matCode }}</div>
                <div class="muted">{{ $matDesc }}</div>
              </td>

              <td class="muted">{{ $unidadTxt }}</td>

              <td class="num">
                <span class="stock-badge">{{ number_format($stock, 2) }}</span>
              </td>

              <td class="num">${{ number_format($costo, 2) }}</td>

              <td class="num money">${{ number_format($valor, 2) }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="6">
                <div class="empty">No hay existencias para los filtros seleccionados.</div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="pagination-wrap">
      @if(is_object($existencias) && method_exists($existencias, 'links'))
        {{ $existencias->links() }}
      @endif
    </div>
  </div>

</div>
@endsection