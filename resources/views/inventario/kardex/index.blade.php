@extends('layouts.base')
@section('title','Kárdex PRO')

@section('content')
@php
$desde = $desde ?? null;
$hasta = $hasta ?? null;
$materialSel = $materialSel ?? null;
$almacenSel = $almacenSel ?? null;
  $kIcon = function(string $type): string {
    return match($type) {
      'entrada'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5"/><path d="m5 12 7-7 7 7"/></svg>',
      'salida'   => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14"/><path d="m19 12-7 7-7-7"/></svg>',
      'traslado' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3l4 4-4 4"/><path d="M3 7h18"/><path d="M7 21l-4-4 4-4"/><path d="M21 17H3"/></svg>',
      'ajuste'   => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14"/><path d="M5 12h14"/></svg>',
      default    => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>',
    };
  };

  $pillClass = function(string $type): string {
    return match($type) {
      'entrada'  => 'badge-green',
      'salida'   => 'badge-red',
      'traslado' => 'badge-blue',
      'ajuste'   => 'badge-purple',
      default    => 'badge-slate',
    };
  };
@endphp

<style>
  .vs-wrap{max-width:1450px;margin:0 auto;padding:18px}
  .vs-head{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:18px}
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

  .panel{
    background:white;border-radius:28px;border:1px solid #e2e8f0;
    box-shadow:0 18px 50px rgba(15,23,42,.07);
    overflow:hidden;margin-bottom:18px;
  }
  .panel-head{padding:18px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
  .panel-title{font-weight:950;color:#0f172a;font-size:16px}
  .panel-sub{font-size:12px;color:#64748b;margin-top:4px;font-weight:700}
  .panel-body{padding:18px}

  .grid{display:grid;grid-template-columns:repeat(12,1fr);gap:14px}
  .span-2{grid-column:span 2}.span-3{grid-column:span 3}.span-5{grid-column:span 5}
  @media(max-width:1000px){.span-2,.span-3,.span-5{grid-column:span 6}}
  @media(max-width:650px){.vs-wrap{padding:12px}.span-2,.span-3,.span-5{grid-column:span 12}}

  .field label{display:block;margin-bottom:7px;font-size:12px;font-weight:900;color:#334155;text-transform:uppercase;letter-spacing:.06em}
  .field input,.field select{
    width:100%;height:46px;border:1px solid #dbe2ea;border-radius:16px;
    padding:0 14px;font-weight:750;outline:none;background:white;color:#0f172a;
  }
  .field input:focus,.field select:focus{border-color:#38bdf8;box-shadow:0 0 0 4px rgba(14,165,233,.12)}

  .kpi-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:18px}
  @media(max-width:1100px){.kpi-grid{grid-template-columns:repeat(2,1fr)}}
  @media(max-width:650px){.kpi-grid{grid-template-columns:1fr}}
  .kpi{
    background:white;border-radius:24px;border:1px solid #e2e8f0;padding:18px;
    box-shadow:0 14px 40px rgba(15,23,42,.06);
  }
  .kpi-label{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#64748b;font-weight:950}
  .kpi-value{margin-top:9px;font-size:26px;line-height:1;font-weight:950;color:#0f172a}
  .kpi-green{color:#047857}.kpi-red{color:#b91c1c}.kpi-blue{color:#1d4ed8}.kpi-indigo{color:#4f46e5}

  .table-wrap{overflow:auto;max-height:620px}
  .table-wrap::-webkit-scrollbar{width:8px;height:8px}
  .table-wrap::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:999px}
  table{width:100%;min-width:1320px;border-collapse:collapse}
  thead{background:#f8fafc;position:sticky;top:0;z-index:10}
  th{padding:15px 14px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#64748b;font-weight:950;white-space:nowrap}
  td{padding:14px;border-top:1px solid #edf2f7;vertical-align:middle;font-size:13px}
  tr:hover{background:#fafcff}

  .strong{font-weight:950;color:#0f172a}
  .muted{color:#64748b;font-size:12px;font-weight:700}
  .num{font-weight:900;white-space:nowrap}
  .green{color:#047857}.red{color:#b91c1c}.indigo{color:#4f46e5}

  .badge{
    display:inline-flex;align-items:center;gap:7px;padding:7px 12px;border-radius:999px;
    font-size:11px;font-weight:950;border:1px solid transparent;white-space:nowrap;
  }
  .badge-green{background:#dcfce7;color:#166534;border-color:#bbf7d0}
  .badge-red{background:#fee2e2;color:#991b1b;border-color:#fecaca}
  .badge-blue{background:#dbeafe;color:#1d4ed8;border-color:#bfdbfe}
  .badge-purple{background:#ede9fe;color:#6d28d9;border-color:#ddd6fe}
  .badge-slate{background:#e2e8f0;color:#334155;border-color:#cbd5e1}

  .empty{padding:42px;text-align:center;color:#64748b;font-weight:800}
</style>

<div class="vs-wrap">

  <div class="vs-head">
    <div>
      <div class="vs-title">Kárdex PRO</div>
      <div class="vs-sub">Historial valorizado por material, almacén, fechas, entradas, salidas y saldo acumulado.</div>
    </div>

<div class="vs-actions">

  <a href="{{ route('inventario.movimientos') }}" class="btn btn-light">
    {!! $kIcon('traslado') !!}
    Movimientos
  </a>

  @if($materialSel && $almacenSel)

    <a
      href="{{ route('inventario.kardex.pdf', [
        'material_id' => $materialSel,
        'almacen_id'  => $almacenSel,
        'desde'       => $desde,
        'hasta'       => $hasta,
      ]) }}"
      target="_blank"
      class="btn btn-primary"
    >
      <svg width="18" height="18" viewBox="0 0 24 24"
           fill="none" stroke="currentColor" stroke-width="2">
        <path d="M12 17V3"/>
        <path d="m6 11 6 6 6-6"/>
        <path d="M19 21H5"/>
      </svg>

      Exportar PDF
    </a>

  @endif

</div>
  </div>

  <div class="panel">
    <div class="panel-head">
      <div>
        <div class="panel-title">Filtros de consulta</div>
        <div class="panel-sub">Selecciona el material, almacén y rango de fechas.</div>
      </div>
    </div>

    <div class="panel-body">
      <form method="GET" action="{{ route('inventario.kardex.ver') }}">
        <div class="grid">
          <div class="field span-5">
            <label>Material</label>
            <select name="material_id" required>
              @foreach($materiales as $m)
                <option value="{{ $m->id }}" @selected(($materialSel ?? '')==$m->id)>
                  {{ $m->sku }} — {{ $m->descripcion }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="field span-3">
            <label>Almacén</label>
            <select name="almacen_id" required>
              @foreach($almacenes as $a)
                <option value="{{ $a->id }}" @selected(($almacenSel ?? '')==$a->id)>
                  {{ $a->nombre }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="field span-2">
            <label>Desde</label>
            <input type="date" name="desde" value="{{ $desde ?? '' }}">
          </div>

          <div class="field span-2">
            <label>Hasta</label>
            <input type="date" name="hasta" value="{{ $hasta ?? '' }}">
          </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:10px;flex-wrap:wrap;margin-top:18px">
          <a href="{{ route('inventario.kardex') }}" class="btn btn-light">Limpiar</a>
          <button class="btn btn-primary" type="submit">
            Consultar Kárdex
          </button>
        </div>
      </form>
    </div>
  </div>

  @if($totales)
    <div class="kpi-grid">
      <div class="kpi">
        <div class="kpi-label">Entradas</div>
        <div class="kpi-value kpi-green">{{ number_format((float)$totales['entradas_cantidad'], 2) }}</div>
      </div>

      <div class="kpi">
        <div class="kpi-label">Salidas</div>
        <div class="kpi-value kpi-red">{{ number_format((float)$totales['salidas_cantidad'], 2) }}</div>
      </div>

      <div class="kpi">
        <div class="kpi-label">Saldo</div>
        <div class="kpi-value">{{ number_format((float)$totales['saldo_cantidad'], 2) }}</div>
      </div>

      <div class="kpi">
        <div class="kpi-label">Valor entradas</div>
        <div class="kpi-value kpi-green">${{ number_format((float)$totales['entradas_valor'], 2) }}</div>
      </div>

      <div class="kpi">
        <div class="kpi-label">Saldo valorizado</div>
        <div class="kpi-value kpi-indigo">${{ number_format((float)$totales['saldo_valor'], 2) }}</div>
      </div>
    </div>
  @endif

  <div class="panel">
    <div class="panel-head">
      <div>
        <div class="panel-title">Detalle de movimientos</div>
        <div class="panel-sub">
          @if($totales)
            {{ $totales['material'] ?? 'Material' }} · {{ $totales['almacen'] ?? 'Almacén' }}
          @else
            Selecciona filtros para visualizar el historial.
          @endif
        </div>
      </div>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Entrada</th>
            <th>Salida</th>
            <th>Saldo</th>
            <th>Referencia</th>
            <th>Detalle</th>
            <th>Costo U.</th>
            <th>Valor</th>
            <th>Saldo $</th>
          </tr>
        </thead>

        <tbody>
          @forelse($rows as $r)
            @php
              $tipo = (string)($r['tipo'] ?? '');
              $pill = $pillClass($tipo);
              $valorMov = (float)($r['entrada_valor'] ?: $r['salida_valor']);
            @endphp

            <tr>
              <td class="num">{{ $r['fecha'] }}</td>

              <td>
                <span class="badge {{ $pill }}">
                  {!! $kIcon($tipo) !!}
                  {{ strtoupper($tipo ?: 'MOV') }}
                </span>
              </td>

              <td class="num green">
                {{ $r['entrada_cantidad'] ? number_format((float)$r['entrada_cantidad'], 2) : '—' }}
              </td>

              <td class="num red">
                {{ $r['salida_cantidad'] ? number_format((float)$r['salida_cantidad'], 2) : '—' }}
              </td>

              <td class="num">{{ number_format((float)$r['saldo_cantidad'], 2) }}</td>

              <td class="muted">{{ $r['referencia'] ?: '—' }}</td>

              <td>
                <div class="strong">{{ $r['detalle'] ?: 'Movimiento de inventario' }}</div>
                <div class="muted">
                  {{ $r['almacen_origen'] ?: '—' }} → {{ $r['almacen_destino'] ?: '—' }}
                </div>
              </td>

              <td class="num">${{ number_format((float)$r['costo_unitario'], 2) }}</td>

              <td class="num">${{ number_format($valorMov, 2) }}</td>

              <td class="num indigo">${{ number_format((float)$r['saldo_valor'], 2) }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="10">
                <div class="empty">Selecciona material y almacén para visualizar el Kárdex.</div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection