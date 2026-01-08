@extends('layouts.base')
@section('title','Tu Tablero')

@section('content')
@php
  $k = $kpis ?? [];

  // Empresa (para mostrar en el header)
  $empresaNombre = (string) (auth()->user()?->empresa?->nombre ?? auth()->user()?->empresa?->razon_social ?? '—');

  // Total REAL de movimientos (si el controller lo manda, usamos ese)
  $totalMovs = (int)($k['movimientos_total'] ?? 0);

  // Por si NO lo mandan (fallback): contar lo que venga en $ultimosMovs (ojo: puede ser limitado)
  if ($totalMovs <= 0) {
    $totalMovs = isset($ultimosMovs) ? (is_countable($ultimosMovs) ? count($ultimosMovs) : 0) : 0;
  }

  // KPI Proyectos (debe venir del controller)
  $proyectosCount = (int)($k['proyectos'] ?? 0);

  // Valor inventario movido al panel Top materiales
  $valorInventario = (float)($k['valor_inventario'] ?? 0);
@endphp

<style>
  .dash-wrap{max-width:1200px;margin:0 auto;padding:14px}
  .dash-head{
    display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;
    background:#fff;border-radius:16px;padding:16px;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
    border:1px solid rgba(15,23,42,.06);
  }
  .dash-title{margin:0;font-size:20px;font-weight:900;color:#0f172a}
  .dash-sub{margin-top:6px;font-size:13px;color:#64748b}
  .dash-sub strong{color:#0f172a}

  .dash-actions{display:flex;gap:10px;flex-wrap:wrap}
  .a-btn{
    display:inline-flex;align-items:center;gap:10px;
    height:42px;padding:0 14px;border-radius:14px;
    font-weight:800;font-size:13px;text-decoration:none;
    border:1px solid rgba(15,23,42,.10);
    background:#fff;color:#0f172a;
    box-shadow:0 10px 24px rgba(2,6,23,.06);
    white-space:nowrap;
  }
  .a-btn:hover{transform:translateY(-1px)}
  .a-ico{width:18px;height:18px;display:inline-block}

  /* botones pastel suave */
  .a-btn.soft{ border-color: rgba(15,23,42,.10); }
  .a-btn.soft.m1{ background: rgba(99,102,241,.10); color:#1e1b4b; }
  .a-btn.soft.m2{ background: rgba(34,197,94,.10);  color:#064e3b; }
  .a-btn.soft.m3{ background: rgba(245,158,11,.10); color:#78350f; }
  .a-btn.soft.m4{ background: rgba(14,165,233,.10); color:#0c4a6e; }

  .grid12{display:grid;grid-template-columns:repeat(12,1fr);gap:14px;margin-top:14px}
  .span3{grid-column:span 3}
  .span8{grid-column:span 8}
  .span4{grid-column:span 4}
  @media (max-width: 1024px){
    .span3{grid-column:span 6}
    .span8{grid-column:span 12}
    .span4{grid-column:span 12}
  }
  @media (max-width: 560px){
    .span3{grid-column:span 12}
  }

  .tile{
    background:#fff;border-radius:16px;padding:16px;
    box-shadow:0 18px 40px rgba(2,6,23,.10);
    border:1px solid rgba(15,23,42,.06);
    display:flex;align-items:center;justify-content:space-between;gap:10px;
    min-height:92px;
    overflow:hidden;
  }
  .tile-left{display:flex;align-items:center;gap:12px;min-width:0}
  .tile-ico{
    width:44px;height:44px;border-radius:14px;
    display:flex;align-items:center;justify-content:center;
    border:1px solid rgba(15,23,42,.08);
    flex:0 0 auto;
  }
  .tile-val{
    font-size:26px;font-weight:950;color:#0f172a;line-height:1;
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
    max-width:56%; text-align:right;
  }
  @media (max-width: 560px){
    .tile-val{max-width:62%; font-size:22px;}
  }
  .tile-meta{margin-top:6px;font-size:12px;color:#64748b;font-weight:700}
  .tile-name{display:none}

  .p1{background:rgba(99,102,241,.12)}
  .p2{background:rgba(34,197,94,.12)}
  .p3{background:rgba(14,165,233,.12)}
  .p4{background:rgba(245,158,11,.12)}

  .cardx{
    background:#fff;border-radius:16px;padding:16px;
    box-shadow:0 18px 40px rgba(2,6,23,.10);
    border:1px solid rgba(15,23,42,.06);
  }
  .muted{color:#64748b;font-size:12px}

  .list{margin-top:12px;display:flex;flex-direction:column;gap:10px}
  .item{
    border:1px solid rgba(15,23,42,.08);
    border-radius:14px;padding:12px;
    background:rgba(2,6,23,.015);
  }
  .row{display:flex;justify-content:space-between;gap:10px}
  .top-total{
    margin-top:10px;
    padding:12px;
    border-radius:14px;
    border:1px solid rgba(15,23,42,.08);
    background:rgba(99,102,241,.06);
    display:flex;justify-content:space-between;align-items:center;gap:10px;
  }
  .top-total .label{font-weight:900;color:#0f172a}
  .top-total .amount{font-weight:950;color:#0f172a}

  /* Cajón Movimientos con botón */
  .mov-tile{
    background:#fff;border-radius:16px;padding:14px 16px;
    box-shadow:0 18px 40px rgba(2,6,23,.10);
    border:1px solid rgba(15,23,42,.06);
    display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;
  }
  .mov-left{display:flex;align-items:center;gap:12px;min-width:0}
  .mov-title{font-weight:950;color:#0f172a}
  .mov-total{font-size:22px;font-weight:950;color:#0f172a;white-space:nowrap}
</style>

<div class="dash-wrap">

  {{-- HEADER --}}
  <div class="dash-head">
    <div>
      <h2 class="dash-title">Tu Tablero</h2>
      <div class="dash-sub">
        <strong>{{ $empresaNombre }}</strong>
      </div>
    </div>

    {{-- MENÚ CON ICONOS (sin botón Nuevo) --}}
    <div class="dash-actions">

      {{-- Material --}}
      <a class="a-btn soft m1" href="{{ route('inventario.materiales') }}" title="Materiales">
        <span class="a-ico">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2 2 7l10 5 10-5-10-5Z"/>
            <path d="M2 17l10 5 10-5"/>
            <path d="M2 12l10 5 10-5"/>
          </svg>
        </span>
        Material
      </a>

      {{-- Almacén --}}
      <a class="a-btn soft m2" href="{{ route('inventario.almacenes') }}" title="Almacenes">
        <span class="a-ico">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 21V8l9-4 9 4v13"/>
            <path d="M3 10h18"/>
            <path d="M9 21v-8h6v8"/>
          </svg>
        </span>
        Almacén
      </a>

      {{-- Proyecto (ruta real: admin.proyectos) --}}
      <a class="a-btn soft m3" href="{{ route('admin.proyectos') }}" title="Proyectos">
        <span class="a-ico">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 7a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z"/>
          </svg>
        </span>
        Proyecto
      </a>

      {{-- Kardex --}}
      <a class="a-btn soft m4" href="{{ route('inventario.kardex') }}" title="Kardex">
        <span class="a-ico">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M8 6h13M8 12h13M8 18h13"/>
            <path d="M3 6h.01M3 12h.01M3 18h.01"/>
          </svg>
        </span>
        Kardex
      </a>

    </div>
  </div>

  {{-- KPIs --}}
  <div class="grid12">

    <div class="span3">
      <div class="tile" title="Almacenes">
        <div class="tile-left">
          <div class="tile-ico p1" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 21V8l9-4 9 4v13"/>
              <path d="M9 21v-8h6v8"/>
              <path d="M3 10h18"/>
            </svg>
          </div>
          <div><div class="tile-name">Almacenes</div><div class="tile-meta">Activos</div></div>
        </div>
        <div class="tile-val">{{ (int)($k['almacenes'] ?? 0) }}</div>
      </div>
    </div>

    <div class="span3">
      <div class="tile" title="Materiales">
        <div class="tile-left">
          <div class="tile-ico p2" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 2 2 7l10 5 10-5-10-5Z"/>
              <path d="M2 17l10 5 10-5"/>
              <path d="M2 12l10 5 10-5"/>
            </svg>
          </div>
          <div><div class="tile-name">Materiales</div><div class="tile-meta">Catálogo</div></div>
        </div>
        <div class="tile-val">{{ (int)($k['materiales'] ?? 0) }}</div>
      </div>
    </div>

    <div class="span3">
      <div class="tile" title="Stock total">
        <div class="tile-left">
          <div class="tile-ico p3" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 3v18h18"/>
              <path d="M7 14v4"/>
              <path d="M12 10v8"/>
              <path d="M17 6v12"/>
            </svg>
          </div>
          <div><div class="tile-name">Stock total</div><div class="tile-meta">Unidades</div></div>
        </div>
        <div class="tile-val">{{ number_format((float)($k['stock_total'] ?? 0), 0) }}</div>
      </div>
    </div>

    <div class="span3">
      <div class="tile" title="Proyectos">
        <div class="tile-left">
          <div class="tile-ico p4" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 7a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z"/>
            </svg>
          </div>
          <div><div class="tile-name">Proyectos</div><div class="tile-meta">Registrados</div></div>
        </div>
        <div class="tile-val">{{ number_format($proyectosCount,0) }}</div>
      </div>
    </div>

  </div>

  {{-- Paneles --}}
  <div class="grid12">

    {{-- Movimientos --}}
    <div class="span8">
      <div class="mov-tile">
        <div class="mov-left">
          <div class="tile-ico p3" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M8 6h13M8 12h13M8 18h13"/>
              <path d="M3 6h.01M3 12h.01M3 18h.01"/>
            </svg>
          </div>
          <div><div class="mov-title">Movimientos</div></div>
        </div>

        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
          <div class="mov-total">Total: {{ $totalMovs }}</div>
          <a class="a-btn" href="{{ route('inventario.movimientos') }}" title="Ver todos los movimientos">
            <span class="a-ico">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M8 6h13M8 12h13M8 18h13"/>
                <path d="M3 6h.01M3 12h.01M3 18h.01"/>
              </svg>
            </span>
            Ver todo
          </a>
        </div>
      </div>
    </div>

    {{-- Top materiales --}}
    <div class="span4">
      <div class="cardx">
        <div style="font-weight:950;color:#0f172a;">Top materiales (valor)</div>
        <div class="muted" style="margin-top:4px;">Stock × costo promedio</div>

        <div class="top-total" title="Valor total del inventario">
          <div class="label">Total inventario</div>
          <div class="amount">${{ number_format($valorInventario, 2) }}</div>
        </div>

        <div class="list">
          @forelse($topMateriales as $t)
            <div class="item">
              <div class="row">
                <div>
                  <div style="font-weight:950;">{{ $t->sku }}</div>
                  <div class="muted">{{ $t->descripcion }}</div>
                </div>
                <div style="text-align:right;">
                  <div style="font-weight:950;">${{ number_format((float)$t->valor, 2) }}</div>
                  <div class="muted">{{ number_format((float)$t->stock,4) }}</div>
                </div>
              </div>
            </div>
          @empty
            <div class="muted">Aún no hay existencias valoradas.</div>
          @endforelse
        </div>
      </div>
    </div>

  </div>

</div>
@endsection
