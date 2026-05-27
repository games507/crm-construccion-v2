@extends('layouts.base')
@section('title','Materiales')

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

  if($name==='edit'){
    return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none">
      <path d="M12 20h9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"/>
    </svg>';
  }

  if($name==='pdf'){
    return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none">
      <path d="M12 17V3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      <path d="m6 11 6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      <path d="M19 21H5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    </svg>';
  }

  if($name==='box'){
    return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none">
      <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"
            stroke="currentColor"
            stroke-width="2"/>
      <path d="M3.3 7L12 12l8.7-5"
            stroke="currentColor"
            stroke-width="2"
            stroke-linejoin="round"/>
      <path d="M12 22V12"
            stroke="currentColor"
            stroke-width="2"/>
    </svg>';
  }

  if($name==='money'){
    return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none">
      <rect x="3" y="6" width="18" height="12" rx="2"
            stroke="currentColor"
            stroke-width="2"/>
      <circle cx="12" cy="12" r="2.5"
              stroke="currentColor"
              stroke-width="2"/>
    </svg>';
  }

  return '';
};

$qVal = trim((string)($q ?? ''));

$total = $items->total();
$activos = $items->where('activo',1)->count();
$valor = $items->sum('costo_estandar');

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
.indigo{color:#4338ca}

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
  min-width:1200px;
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
}

.badge-ok{
  background:#dcfce7;
  color:#166534;
}

.badge-off{
  background:#fee2e2;
  color:#991b1b;
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
        Materiales
      </div>

      <div class="vs-sub">
        Catálogo general de materiales y costos.
      </div>
    </div>

    <div class="flex gap-2 flex-wrap">

      <a href="{{ route('inventario.materiales.pdf', ['q'=>$qVal]) }}"
         target="_blank"
         class="btn btn-light">

        {!! $icon('pdf') !!}

        PDF
      </a>

      @can('materiales.crear')

        <a href="{{ route('inventario.materiales.create') }}"
           class="btn btn-primary">

          {!! $icon('plus') !!}

          Nuevo material
        </a>

      @endcan

    </div>

  </div>

  <div class="kpi-grid">

    <div class="kpi">
      <div class="kpi-label">
        Total materiales
      </div>

      <div class="kpi-value">
        {{ number_format($total) }}
      </div>
    </div>

    <div class="kpi">
      <div class="kpi-label">
        Materiales activos
      </div>

      <div class="kpi-value green">
        {{ number_format($activos) }}
      </div>
    </div>

    <div class="kpi">
      <div class="kpi-label">
        Costo visible
      </div>

      <div class="kpi-value indigo">
        ${{ number_format($valor,2) }}
      </div>
    </div>

  </div>

  <div class="panel">

    <div class="panel-head">

      <div class="flex items-center justify-between gap-4 flex-wrap">

        <div>

          <div class="panel-title">
            Listado de materiales
          </div>

          <div class="panel-sub">
            Busca por código, SKU o descripción.
          </div>

        </div>

        <form method="GET"
              action="{{ route('inventario.materiales') }}"
              class="search-wrap">

          <div class="search-box">

            <span>
              {!! $icon('search') !!}
            </span>

            <input
              type="text"
              name="q"
              value="{{ $qVal }}"
              placeholder="Buscar material..."
              class="search-input"
            >

          </div>

          <button type="submit"
                  class="btn btn-primary">
            Buscar
          </button>

          @if($qVal !== '')

            <a href="{{ route('inventario.materiales') }}"
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
            <th>Código</th>
            <th>SKU</th>
            <th>Descripción</th>
            <th>Unidad</th>
            <th class="text-right">Costo estándar</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>

        </thead>

        <tbody>

          @forelse($items as $m)

            <tr>

              <td class="font-black text-slate-900">
                {{ $m->codigo ?: '—' }}
              </td>

              <td class="font-semibold text-slate-600">
                {{ $m->sku ?: '—' }}
              </td>

              <td>

                <div class="font-black text-slate-900">
                  {{ $m->descripcion ?: '—' }}
                </div>

              </td>

              <td class="font-semibold text-slate-600">
                {{ $m->unidad ?: '—' }}
              </td>

              <td class="text-right font-black text-indigo-700">
                ${{ number_format((float)($m->costo_estandar ?? 0),2) }}
              </td>

              <td>

                @if($m->activo)

                  <span class="badge badge-ok">
                    ACTIVO
                  </span>

                @else

                  <span class="badge badge-off">
                    INACTIVO
                  </span>

                @endif

              </td>

              <td>

                @can('materiales.editar')

                  <a href="{{ route('inventario.materiales.edit', $m) }}"
                     class="btn btn-light"
                     style="height:38px;padding:0 12px">

                    {!! $icon('edit') !!}

                  </a>

                @endcan

              </td>

            </tr>

          @empty

            <tr>

              <td colspan="7"
                  class="text-center py-14 text-slate-500 font-bold">

                No hay materiales registrados.

              </td>

            </tr>

          @endforelse

        </tbody>

      </table>

    </div>

    <div class="pagination-wrap">
      {{ $items->links() }}
    </div>

  </div>

</div>

@endsection