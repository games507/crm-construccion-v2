@extends('layouts.base')
@section('title','Dashboard')

@section('content')
<div style="max-width:1200px;margin:0 auto;">

  <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
    <div>
      <h2 style="margin:0 0 6px 0;">Dashboard</h2>
      <div style="color:#64748b;font-size:13px;">
        Resumen ejecutivo del inventario y operaciones.
      </div>
    </div>

    <div style="display:flex;gap:10px;flex-wrap:wrap;">
      <a class="btn btn-outline" href="{{ route('inventario.existencias') }}">Existencias</a>
      <a class="btn btn-outline" href="{{ route('inventario.movimientos') }}">Movimientos</a>
      <a class="btn" href="{{ route('inventario.movimientos.create') }}">+ Nuevo movimiento</a>
    </div>
  </div>

  {{-- KPIs --}}
  <div style="display:grid;grid-template-columns:repeat(12,1fr);gap:14px;margin-top:16px;">

    <div style="grid-column: span 3;">
      <div class="card" style="padding:16px;border-radius:16px;box-shadow:0 18px 40px rgba(2,6,23,.10);">
        <div style="font-size:12px;color:#64748b;">Almacenes</div>
        <div style="font-size:26px;font-weight:900;margin-top:4px;">{{ (int)($kpis['almacenes'] ?? 0) }}</div>
        <div style="font-size:12px;color:#94a3b8;margin-top:6px;">Activos</div>
      </div>
    </div>

    <div style="grid-column: span 3;">
      <div class="card" style="padding:16px;border-radius:16px;box-shadow:0 18px 40px rgba(2,6,23,.10);">
        <div style="font-size:12px;color:#64748b;">Materiales</div>
        <div style="font-size:26px;font-weight:900;margin-top:4px;">{{ (int)($kpis['materiales'] ?? 0) }}</div>
        <div style="font-size:12px;color:#94a3b8;margin-top:6px;">Catálogo</div>
      </div>
    </div>

    <div style="grid-column: span 3;">
      <div class="card" style="padding:16px;border-radius:16px;box-shadow:0 18px 40px rgba(2,6,23,.10);">
        <div style="font-size:12px;color:#64748b;">Stock total</div>
        <div style="font-size:26px;font-weight:900;margin-top:4px;">
          <!-- {{ number_format((float) $kpis['stock_total'], 0) }} -->
          {{ number_format($kpis['stock_total'], 0) }}

        </div>
        <div style="font-size:12px;color:#94a3b8;margin-top:6px;">Unidades</div>
      </div>
    </div>

    <div style="grid-column: span 3;">
      <div class="card" style="padding:16px;border-radius:16px;box-shadow:0 18px 40px rgba(2,6,23,.10);">
        <div style="font-size:12px;color:#64748b;">Valor inventario</div>
        <div style="font-size:26px;font-weight:900;margin-top:4px;">
          ${{ number_format((float)($kpis['valor_inventario'] ?? 0), 2) }}
        </div>
        <div style="font-size:12px;color:#94a3b8;margin-top:6px;">Costo promedio</div>
      </div>
    </div>

  </div>

  {{-- Paneles --}}
  <div style="display:grid;grid-template-columns:repeat(12,1fr);gap:14px;margin-top:16px;">

    {{-- Últimos movimientos --}}
    <div style="grid-column: span 8;">
      <div class="card" style="padding:16px;border-radius:16px;box-shadow:0 18px 40px rgba(2,6,23,.10);">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
          <div style="font-weight:900;">Últimos movimientos</div>
          <a class="btn btn-outline" href="{{ route('inventario.movimientos') }}">Ver todo</a>
        </div>

        <div style="margin-top:12px;overflow:auto;border:1px solid rgba(15,23,42,.08);border-radius:14px;">
          <table width="100%" style="border-collapse:collapse;min-width:760px;">
            <thead>
              <tr style="background:rgba(2,6,23,.03);text-align:left;">
                <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);">Fecha</th>
                <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);">Tipo</th>
                <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);">Material</th>
                <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);">Cantidad</th>
                <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);">Origen</th>
                <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);">Destino</th>
              </tr>
            </thead>
            <tbody>
              @forelse($ultimosMovs as $m)
                <tr style="border-bottom:1px solid rgba(15,23,42,.06);">
                  <td style="padding:12px;white-space:nowrap;">{{ $m->fecha }}</td>
                  <td style="padding:12px;">
                    <span style="
                      display:inline-flex;align-items:center;gap:8px;
                      padding:6px 10px;border-radius:999px;
                      border:1px solid rgba(15,23,42,.10);
                      background: {{ $m->tipo==='entrada' ? 'rgba(34,197,94,.10)' : ($m->tipo==='salida' ? 'rgba(239,68,68,.10)' : 'rgba(99,102,241,.10)') }};
                      font-weight:800;font-size:12px;
                    ">
                      {{ strtoupper($m->tipo) }}
                    </span>
                  </td>
                  <td style="padding:12px;">
                    <div style="font-weight:800;">{{ $m->material?->sku }}</div>
                    <div style="color:#64748b;font-size:12px;">{{ $m->material?->descripcion }}</div>
                  </td>
                  <td style="padding:12px;font-weight:900;text-align:right;">
                    {{ number_format ($m->cantidad,0) }}
                  </td>
                  <td style="padding:12px;color:#64748b;">{{ $m->almacenOrigen?->nombre ?? '—' }}</td>
                  <td style="padding:12px;color:#64748b;">{{ $m->almacenDestino?->nombre ?? '—' }}</td>
                </tr>
              @empty
                <tr><td colspan="6" style="padding:14px;color:#64748b;">No hay movimientos aún.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- Top materiales por valor --}}
    <div style="grid-column: span 4;">
      <div class="card" style="padding:16px;border-radius:16px;box-shadow:0 18px 40px rgba(2,6,23,.10);">
        <div style="font-weight:900;">Top materiales (valor)</div>
        <div style="color:#64748b;font-size:12px;margin-top:4px;">Stock × costo promedio</div>

        <div style="margin-top:12px;display:flex;flex-direction:column;gap:10px;">
          @forelse($topMateriales as $t)
            <div style="border:1px solid rgba(15,23,42,.08);border-radius:14px;padding:12px;">
              <div style="display:flex;justify-content:space-between;gap:10px;">
                <div>
                  <div style="font-weight:900;">{{ $t->sku }}</div>
                  <div style="font-size:12px;color:#64748b;">{{ $t->descripcion }}</div>
                </div>
                <div style="text-align:right;">
                  <div style="font-weight:900;">${{ number_format((float)$t->valor, 2) }}</div>
                  <div style="font-size:12px;color:#64748b;">{{ number_format((float)$t->stock,4) }}</div>
                </div>
              </div>
            </div>
          @empty
            <div style="color:#64748b;">Aún no hay existencias valoradas.</div>
          @endforelse
        </div>
      </div>
    </div>

  </div>

</div>
@endsection
