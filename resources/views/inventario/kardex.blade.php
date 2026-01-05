@extends('layouts.base')
@section('title','Kárdex')

@section('content')
<div class="card" style="max-width: 1100px; margin: 0 auto;">

  <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
    <div>
      <h2 style="margin:0 0 6px 0;">Kárdex</h2>
      <div style="color:#64748b;font-size:13px;">
        Historial por <b>material</b> y <b>almacén</b>, con saldo acumulado.
      </div>
    </div>
    <a class="btn btn-outline" href="{{ route('inventario.movimientos') }}">Ver movimientos</a>
  </div>

  <form method="GET" action="{{ route('inventario.kardex.ver') }}" style="margin-top:14px;">
    <div style="display:grid;grid-template-columns:repeat(12,1fr);gap:14px;">

      {{-- Material --}}
      <div style="grid-column: span 8;">
        <div class="field">
          <div class="label">Material</div>
          <div class="select-wrap has-icon">
            <div class="select-icon">M</div>
            <select name="material_id" required>
              @foreach($materiales as $m)
                <option value="{{ $m->id }}" @selected(($materialSel ?? '')==$m->id)>
                  {{ $m->sku }} — {{ $m->descripcion }}
                </option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      {{-- Almacén --}}
      <div style="grid-column: span 4;">
        <div class="field">
          <div class="label">Almacén</div>
          <div class="select-wrap has-icon">
            <div class="select-icon">A</div>
            <select name="almacen_id" required>
              @foreach($almacenes as $a)
                <option value="{{ $a->id }}" @selected(($almacenSel ?? '')==$a->id)>
                  {{ $a->nombre }}
                </option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

    </div>

    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:12px;flex-wrap:wrap;">
      <button class="btn" type="submit">Ver Kárdex</button>
    </div>
  </form>

  @if($totales)
    <div style="display:grid;grid-template-columns:repeat(12,1fr);gap:14px;margin-top:16px;">
      <div style="grid-column: span 4;">
        <div class="kpi">
          <div class="label">Entradas</div>
          <div class="value">{{ number_format((float)$totales['entradas'],4) }}</div>
          <div class="hint">Total recibido en el almacén</div>
        </div>
      </div>

      <div style="grid-column: span 4;">
        <div class="kpi">
          <div class="label">Salidas</div>
          <div class="value">{{ number_format((float)$totales['salidas'],4) }}</div>
          <div class="hint">Total despachado del almacén</div>
        </div>
      </div>

      <div style="grid-column: span 4;">
        <div class="kpi">
          <div class="label">Saldo</div>
          <div class="value">{{ number_format((float)$totales['saldo'],4) }}</div>
          <div class="hint">Existencia resultante</div>
        </div>
      </div>
    </div>
  @endif

  <div style="margin-top:16px;overflow:auto;border:1px solid rgba(15,23,42,.08);border-radius:14px;">
    <table width="100%" cellpadding="10" style="border-collapse:collapse;min-width:860px;">
      <thead>
        <tr style="background:rgba(2,6,23,.03);text-align:left;">
          <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);">Fecha</th>
          <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);">Tipo</th>
          <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);">Entrada</th>
          <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);">Salida</th>
          <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);">Saldo</th>
          <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);">Referencia</th>
        </tr>
      </thead>

      <tbody>
        @forelse($rows as $r)
          @php
            $isIn = (float)$r['entrada'] > 0;
            $isOut = (float)$r['salida'] > 0;
          @endphp

          <tr style="border-bottom:1px solid rgba(15,23,42,.06);">
            <td style="padding:12px;white-space:nowrap;">{{ $r['fecha'] }}</td>
            <td style="padding:12px;">
              <span style="
                display:inline-flex;align-items:center;gap:8px;
                padding:6px 10px;border-radius:999px;
                border:1px solid rgba(15,23,42,.10);
                background: {{ $isIn ? 'rgba(34,197,94,.10)' : ($isOut ? 'rgba(239,68,68,.10)' : 'rgba(99,102,241,.10)') }};
                color:#0f172a;
                font-weight:700;font-size:12px;
              ">
                {{ strtoupper($r['tipo']) }}
              </span>
            </td>

            <td style="padding:12px;">
              {{ $r['entrada'] ? number_format((float)$r['entrada'],4) : '—' }}
            </td>

            <td style="padding:12px;">
              {{ $r['salida'] ? number_format((float)$r['salida'],4) : '—' }}
            </td>

            <td style="padding:12px;font-weight:800;">
              {{ number_format((float)$r['saldo'],4) }}
            </td>

            <td style="padding:12px;color:#64748b;">
              {{ $r['ref'] ?: '—' }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" style="padding:14px;color:#64748b;">
              Selecciona material y almacén para ver movimientos.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

</div>
@endsection
