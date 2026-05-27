@extends('layouts.base')
@section('title','Movimientos')

@section('content')
@php
  $icon = function($name){

    if($name==='plus'){
      return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none">
        <path d="M12 5v14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>';
    }

    if($name==='search'){
      return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none">
        <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
      </svg>';
    }

    if($name==='swap'){
      return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none">
        <path d="M16 3l4 4-4 4" stroke="currentColor" stroke-width="2"/>
        <path d="M20 7H10a4 4 0 0 0-4 4" stroke="currentColor" stroke-width="2"/>
        <path d="M8 21l-4-4 4-4" stroke="currentColor" stroke-width="2"/>
        <path d="M4 17h10a4 4 0 0 0 4-4" stroke="currentColor" stroke-width="2"/>
      </svg>';
    }

    return '';
  };

  $tipoBadge = function($tipo){
    return match($tipo){
      'entrada' => 'badge-green',
      'salida' => 'badge-red',
      'traslado' => 'badge-blue',
      'ajuste' => 'badge-yellow',
      default => 'badge-slate',
    };
  };

  $q = (string) request('q', '');

  $totalMovs = $movs->total();
  $entradas = $movs->where('tipo','entrada')->count();
  $salidas = $movs->where('tipo','salida')->count();
@endphp

<style>

.vs-wrap{
  max-width:1450px;
  margin:0 auto;
  padding:18px;
}

.vs-head{
  display:flex;
  justify-content:space-between;
  align-items:flex-start;
  gap:16px;
  flex-wrap:wrap;
  margin-bottom:18px;
}

.vs-title{
  font-size:30px;
  font-weight:950;
  color:#0f172a;
}

.vs-sub{
  margin-top:6px;
  font-size:13px;
  color:#64748b;
  font-weight:700;
}

.btn{
  height:44px;
  border:none;
  border-radius:16px;
  padding:0 18px;
  display:inline-flex;
  align-items:center;
  gap:10px;
  font-weight:900;
  text-decoration:none;
  transition:.2s ease;
}

.btn:hover{
  transform:translateY(-2px);
}

.btn-primary{
  background:linear-gradient(135deg,#0f172a,#0b4f7d);
  color:white;
  box-shadow:0 12px 30px rgba(15,23,42,.15);
}

.btn-light{
  background:#f1f5f9;
  color:#334155;
  border:1px solid #e2e8f0;
}

.panel{
  background:white;
  border-radius:28px;
  border:1px solid #e2e8f0;
  box-shadow:0 18px 50px rgba(15,23,42,.07);
  overflow:hidden;
}

.panel-head{
  padding:18px;
  border-bottom:1px solid #e2e8f0;
}

.panel-title{
  font-size:16px;
  font-weight:950;
  color:#0f172a;
}

.panel-sub{
  margin-top:4px;
  font-size:12px;
  color:#64748b;
  font-weight:700;
}

.panel-body{
  padding:18px;
}

.kpi-grid{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:14px;
  margin-bottom:18px;
}

@media(max-width:900px){
  .kpi-grid{
    grid-template-columns:1fr;
  }
}

.kpi{
  background:white;
  border-radius:24px;
  border:1px solid #e2e8f0;
  padding:18px;
  box-shadow:0 14px 40px rgba(15,23,42,.06);
}

.kpi-label{
  font-size:11px;
  text-transform:uppercase;
  letter-spacing:.08em;
  color:#64748b;
  font-weight:950;
}

.kpi-value{
  margin-top:10px;
  font-size:28px;
  font-weight:950;
}

.green{color:#047857}
.red{color:#b91c1c}
.blue{color:#1d4ed8}

.search-wrap{
  display:flex;
  gap:10px;
  flex-wrap:wrap;
  align-items:center;
}

.search-box{
  position:relative;
}

.search-box span{
  position:absolute;
  left:12px;
  top:50%;
  transform:translateY(-50%);
  color:#94a3b8;
}

.search-input{
  height:44px;
  width:340px;
  max-width:80vw;
  border-radius:16px;
  border:1px solid #dbe2ea;
  padding-left:42px;
  padding-right:14px;
  font-weight:700;
}

.search-input:focus{
  outline:none;
  border-color:#38bdf8;
  box-shadow:0 0 0 4px rgba(14,165,233,.12);
}

.table-wrap{
  overflow:auto;
  max-height:650px;
}

.table-wrap::-webkit-scrollbar{
  width:8px;
  height:8px;
}

.table-wrap::-webkit-scrollbar-thumb{
  background:#cbd5e1;
  border-radius:999px;
}

table{
  width:100%;
  min-width:1400px;
  border-collapse:collapse;
}

thead{
  position:sticky;
  top:0;
  z-index:10;
  background:#f8fafc;
}

th{
  padding:14px;
  text-align:left;
  font-size:11px;
  text-transform:uppercase;
  letter-spacing:.08em;
  color:#64748b;
  font-weight:950;
  white-space:nowrap;
}

td{
  padding:14px;
  border-top:1px solid #edf2f7;
  vertical-align:middle;
}

tr:hover{
  background:#fafcff;
}

.badge{
  display:inline-flex;
  align-items:center;
  padding:7px 12px;
  border-radius:999px;
  font-size:11px;
  font-weight:950;
  border:1px solid transparent;
}

.badge-green{
  background:#dcfce7;
  color:#166534;
  border-color:#bbf7d0;
}

.badge-red{
  background:#fee2e2;
  color:#991b1b;
  border-color:#fecaca;
}

.badge-blue{
  background:#dbeafe;
  color:#1d4ed8;
  border-color:#bfdbfe;
}

.badge-yellow{
  background:#fef3c7;
  color:#92400e;
  border-color:#fde68a;
}

.badge-slate{
  background:#e2e8f0;
  color:#334155;
  border-color:#cbd5e1;
}

.pagination-wrap{
  padding:18px;
  border-top:1px solid #e2e8f0;
  background:#fff;
}

</style>

<div class="vs-wrap">

  <div class="vs-head">

    <div>
      <div class="vs-title">
        Movimientos de Inventario
      </div>

      <div class="vs-sub">
        Entradas, salidas, traslados y ajustes del inventario.
      </div>
    </div>

    @can('inventario.crear')
      <a href="{{ route('inventario.movimientos.create') }}"
         class="btn btn-primary">
        {!! $icon('plus') !!}
        Nuevo movimiento
      </a>
    @endcan

  </div>

  <div class="kpi-grid">

    <div class="kpi">
      <div class="kpi-label">Total movimientos</div>
      <div class="kpi-value blue">
        {{ number_format($totalMovs) }}
      </div>
    </div>

    <div class="kpi">
      <div class="kpi-label">Entradas</div>
      <div class="kpi-value green">
        {{ number_format($entradas) }}
      </div>
    </div>

    <div class="kpi">
      <div class="kpi-label">Salidas</div>
      <div class="kpi-value red">
        {{ number_format($salidas) }}
      </div>
    </div>

  </div>

  <div class="panel">

    <div class="panel-head">

      <div class="flex items-center justify-between gap-4 flex-wrap">

        <div>
          <div class="panel-title">
            Historial de movimientos
          </div>

          <div class="panel-sub">
            Registro completo del inventario.
          </div>
        </div>

        <form method="GET"
              action="{{ route('inventario.movimientos') }}"
              class="search-wrap">

          <div class="search-box">

            <span>
              {!! $icon('search') !!}
            </span>

            <input
              type="text"
              name="q"
              value="{{ $q }}"
              placeholder="Buscar material, referencia o tipo..."
              class="search-input"
            >

          </div>

          <button type="submit"
                  class="btn btn-light">
            Buscar
          </button>

          @if($q !== '')
            <a href="{{ route('inventario.movimientos') }}"
               class="btn btn-light">
              Limpiar
            </a>
          @endif

        </form>

      </div>

    </div>

    <div class="table-wrap">

      <table>

        <thead>
          <tr>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Material</th>
            <th>Origen</th>
            <th>Destino</th>
            <th class="text-right">Cantidad</th>
            <th class="text-right">Costo U.</th>
            <th>Referencia</th>
          </tr>
        </thead>

        <tbody>

          @forelse($movs as $m)

            @php
              $mat = $m->material;
              $origen = $m->almacenOrigen;
              $destino = $m->almacenDestino;
            @endphp

            <tr>

              <td class="font-bold whitespace-nowrap">
                {{ \Carbon\Carbon::parse($m->fecha)->format('Y-m-d') }}
              </td>

              <td>
                <span class="badge {{ $tipoBadge($m->tipo) }}">
                  {{ strtoupper($m->tipo) }}
                </span>
              </td>

              <td>

                <div class="font-black text-slate-900">
                  {{ $mat->codigo ?? $mat->sku ?? '—' }}
                </div>

                <div class="text-xs text-slate-500 font-semibold mt-1">
                  {{ $mat->descripcion ?? '—' }}
                </div>

              </td>

              <td>

                @if($origen)
                  <div class="font-black text-slate-900">
                    {{ $origen->codigo }}
                  </div>

                  <div class="text-xs text-slate-500 font-semibold mt-1">
                    {{ $origen->nombre }}
                  </div>
                @else
                  —
                @endif

              </td>

              <td>

                @if($destino)
                  <div class="font-black text-slate-900">
                    {{ $destino->codigo }}
                  </div>

                  <div class="text-xs text-slate-500 font-semibold mt-1">
                    {{ $destino->nombre }}
                  </div>
                @else
                  —
                @endif

              </td>

              <td class="text-right font-black">
                {{ number_format((float)$m->cantidad,0) }}
              </td>

              <td class="text-right font-black">
                ${{ number_format((float)($m->costo_unitario ?? 0),2) }}
              </td>

              <td class="font-semibold text-slate-600">
                {{ $m->referencia ?: '—' }}
              </td>

            </tr>

          @empty

            <tr>
              <td colspan="8" class="text-center py-14 text-slate-500 font-bold">
                No hay movimientos registrados.
              </td>
            </tr>

          @endforelse

        </tbody>

      </table>

    </div>

    <div class="pagination-wrap">
      {{ $movs->links() }}
    </div>

  </div>

</div>
@endsection