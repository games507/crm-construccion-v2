@extends('layouts.base')
@section('title','Proyectos')

@section('content')
@php
  $baseResumen = $proyectosResumen ?? $proyectos;

  $qVal = trim((string)($q ?? request('q', '')));
  $estadoVal = trim((string)($estado ?? request('estado', '')));

  $totalProyectos = $baseResumen->count();
  $totalPlaneados = $baseResumen->where('estado', 'planeado')->count();
  $totalEjecucion = $baseResumen->where('estado', 'en_ejecucion')->count();
  $totalFinalizados = $baseResumen->where('estado', 'finalizado')->count();

  $presupuestoTotal = (float) $baseResumen->sum('presupuesto');
  $avancePromedio = $totalProyectos > 0
      ? round((float) $baseResumen->avg(fn($p) => (float)($p->porcentaje ?? $p->avance ?? 0)), 2)
      : 0;

  $icon = function($name){
    if($name==='plus') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 5v14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='eye') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='edit') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M4 20h4l10-10-4-4L4 16v4Z" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='folder') return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M3 7.5A2.5 2.5 0 0 1 5.5 5H10l2 2h6.5A2.5 2.5 0 0 1 21 9.5v7A2.5 2.5 0 0 1 18.5 19h-13A2.5 2.5 0 0 1 3 16.5v-9Z" stroke="currentColor" stroke-width="2"/></svg>';
    return '';
  };
@endphp

<style>
.vs-wrap{max-width:1450px;margin:0 auto;padding:18px}
.vs-head{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:18px}
.vs-title{font-size:30px;font-weight:950;color:#0f172a;line-height:1}
.vs-sub{margin-top:7px;font-size:13px;color:#64748b;font-weight:700}
.vs-btn{height:40px;border:none;border-radius:16px;padding:0 18px;display:inline-flex;align-items:center;justify-content:center;gap:10px;font-weight:900;text-decoration:none;cursor:pointer;transition:.2s ease}
.vs-btn:hover{transform:translateY(-2px)}
.vs-btn-primary{background:linear-gradient(135deg,#0f172a,#0b4f7d);color:white;box-shadow:0 12px 30px rgba(15,23,42,.15)}
.vs-btn-primary:hover{color:white}
.vs-btn-light{background:#f1f5f9;color:#334155;border:1px solid #e2e8f0}
.vs-btn-light:hover{background:#e2e8f0;color:#334155}

.vs-grid{display:grid;grid-template-columns:300px minmax(0,1fr);gap:18px;max-width:100%;overflow:hidden}
@media(max-width:1100px){.vs-grid{grid-template-columns:1fr}}

.kpi-grid{display:grid;grid-template-columns:1fr;gap:14px}
.kpi{background:white;border-radius:24px;border:1px solid #e2e8f0;padding:18px;box-shadow:0 14px 40px rgba(15,23,42,.06)}
.kpi-label{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#64748b;font-weight:950}
.kpi-value{margin-top:10px;font-size:26px;font-weight:950;color:#0f172a}
.green{color:#047857}.rose{color:#b91c1c}.amber{color:#b45309}.indigo{color:#4338ca}

.vs-card{background:white;border-radius:28px;border:1px solid #e2e8f0;box-shadow:0 18px 50px rgba(15,23,42,.07);overflow:hidden;max-width:100%}
.vs-card-head{padding:18px;border-bottom:1px solid #e2e8f0}
.vs-card-title{font-weight:950;color:#0f172a;font-size:16px}
.vs-card-sub{font-size:12px;color:#64748b;margin-top:4px;font-weight:700}
.vs-card-body{padding:14px 18px}

.filter-grid{display:flex;gap:8px;flex-wrap:wrap;align-items:end}
.input-vs,.select-vs{height:40px;border:1px solid #dbe2ea;border-radius:16px;padding:0 14px;font-weight:700;outline:none;background:white;color:#0f172a}
.input-vs:focus,.select-vs:focus{border-color:#38bdf8;box-shadow:0 0 0 4px rgba(14,165,233,.12)}

.vs-table-wrap{overflow:auto;max-height:520px;width:100%}
.vs-table-wrap::-webkit-scrollbar{width:8px;height:8px}
.vs-table-wrap::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:999px}
table{width:100%;min-width:1150px;border-collapse:collapse}
thead{background:#f8fafc;position:sticky;top:0;z-index:10}
th{padding:15px 14px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#64748b;font-weight:950;white-space:nowrap}
td{padding:14px;border-top:1px solid #edf2f7;vertical-align:middle;font-size:13px}
tr:hover{background:#fafcff}

.vs-badge{display:inline-flex;align-items:center;padding:7px 12px;border-radius:999px;font-size:11px;font-weight:950;border:1px solid transparent}
.vs-badge-slate{background:#f1f5f9;color:#334155;border-color:#e2e8f0}
.vs-badge-indigo{background:#eef2ff;color:#4338ca;border-color:#c7d2fe}
.vs-badge-warn{background:#fef3c7;color:#92400e;border-color:#fde68a}
.vs-badge-ok{background:#dcfce7;color:#166534;border-color:#bbf7d0}
.vs-badge-danger{background:#fee2e2;color:#991b1b;border-color:#fecaca}

.action-btn{height:32px;width:32px;border-radius:12px;border:1px solid #e2e8f0;background:white;display:inline-flex;align-items:center;justify-content:center;color:#64748b;transition:.2s ease}
.action-btn:hover{background:#f8fafc;transform:translateY(-1px)}
</style>

<div class="vs-wrap">

  <div class="vs-head">
    <div>
      <div class="vs-title">Proyectos</div>
      <div class="vs-sub">Gestión general de proyectos, avances, responsables y presupuestos.</div>
    </div>

    @can('proyectos.crear')
      <a href="{{ route('admin.proyectos.create') }}" class="vs-btn vs-btn-primary">
        {!! $icon('plus') !!} Nuevo proyecto
      </a>
    @endcan
  </div>

  @if (session('ok'))
    <div class="mb-4 rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 font-black">
      {{ session('ok') }}
    </div>
  @endif

  @if (session('err'))
    <div class="mb-4 rounded-3xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 font-black">
      {{ session('err') }}
    </div>
  @endif

  <div class="vs-grid">

    <div class="space-y-4">
      <div class="kpi-grid">
        <div class="kpi">
          <div class="kpi-label">Total proyectos</div>
          <div class="kpi-value">{{ $totalProyectos }}</div>
        </div>

        <div class="kpi">
          <div class="kpi-label">En ejecución</div>
          <div class="kpi-value indigo">{{ $totalEjecucion }}</div>
        </div>

        <div class="kpi">
          <div class="kpi-label">Finalizados</div>
          <div class="kpi-value green">{{ $totalFinalizados }}</div>
        </div>

        <div class="kpi">
          <div class="kpi-label">Planeados</div>
          <div class="kpi-value amber">{{ $totalPlaneados }}</div>
        </div>

        <div class="kpi">
          <div class="kpi-label">Presupuesto total</div>
          <div class="kpi-value">$ {{ number_format($presupuestoTotal, 2, '.', ',') }}</div>
        </div>

        <div class="kpi">
          <div class="kpi-label">Avance promedio</div>
          <div class="kpi-value indigo">{{ number_format($avancePromedio, 0) }}%</div>
        </div>
      </div>
    </div>

    <div class="space-y-4">

      <div class="vs-card">
        <div class="vs-card-body">
          <form method="GET" action="{{ route('admin.proyectos') }}" class="filter-grid">

            <div>
              <label class="text-xs font-black text-slate-500 uppercase">Buscar</label>
              <input name="q" value="{{ $qVal }}" placeholder="Código, nombre, ubicación..."
                     class="input-vs mt-1" style="width:280px;max-width:100%;">
            </div>

            <div>
              <label class="text-xs font-black text-slate-500 uppercase">Estado</label>
              <select name="estado" class="select-vs mt-1">
                <option value="">Todos</option>
                <option value="planeado" @selected($estadoVal === 'planeado')>Planeado</option>
                <option value="en_ejecucion" @selected($estadoVal === 'en_ejecucion')>En ejecución</option>
                <option value="pausado" @selected($estadoVal === 'pausado')>Pausado</option>
                <option value="finalizado" @selected($estadoVal === 'finalizado')>Finalizado</option>
              </select>
            </div>

            <button type="submit" class="vs-btn vs-btn-primary">Filtrar</button>

            @if($qVal !== '' || $estadoVal !== '')
              <a href="{{ route('admin.proyectos') }}" class="vs-btn vs-btn-light">Limpiar</a>
            @endif

            <div class="ml-auto text-right">
              <div class="text-[11px] uppercase tracking-wide text-slate-500 font-black">Registros</div>
              <div class="text-sm font-black text-slate-900">{{ $proyectos->total() }}</div>
            </div>

          </form>
        </div>
      </div>

      <div class="vs-card">
        <div class="vs-card-head">
          <div>
            <div class="vs-card-title">Detalle de proyectos</div>
            <div class="vs-card-sub">Listado de proyectos, responsables, estados, avances y presupuesto.</div>
          </div>
        </div>

        <div class="vs-table-wrap">
          <table>
            <thead>
              <tr>
                <th>Código</th>
                <th>Proyecto</th>
                <th>Responsable</th>
                <th style="text-align:center">Estado</th>
                <th>Progreso</th>
                <th>Fechas</th>
                <th style="text-align:right">Presupuesto</th>
                <th style="text-align:center">Acciones</th>
              </tr>
            </thead>

            <tbody>
              @forelse($proyectos as $p)
                @php
                  $estadoP = (string) ($p->estado ?? '');
                  $estadoLabel = $estadoP ? ucfirst(str_replace('_', ' ', $estadoP)) : '—';

                  $badge = match($estadoP) {
                    'planeado'     => 'vs-badge-slate',
                    'en_ejecucion' => 'vs-badge-indigo',
                    'pausado'      => 'vs-badge-warn',
                    'finalizado'   => 'vs-badge-ok',
                    default        => 'vs-badge-slate',
                  };

                  $responsableNombre = $p->responsable->name
                    ?? $p->responsable->nombre
                    ?? $p->responsable->nombre_completo
                    ?? '—';

                  $porcentaje = (float) ($p->porcentaje ?? $p->avance ?? 0);
                  $porcentaje = max(0, min(100, round($porcentaje, 2)));

                  $progressColor = match (true) {
                    $porcentaje >= 100 => 'bg-emerald-500',
                    $porcentaje >= 70  => 'bg-green-500',
                    $porcentaje >= 31  => 'bg-amber-500',
                    $porcentaje > 0    => 'bg-rose-500',
                    default            => 'bg-slate-300',
                  };

                  $progressText = match (true) {
                    $porcentaje >= 100 => 'text-emerald-700',
                    $porcentaje >= 70  => 'text-green-700',
                    $porcentaje >= 31  => 'text-amber-700',
                    $porcentaje > 0    => 'text-rose-700',
                    default            => 'text-slate-500',
                  };
                @endphp

                <tr>
                  <td class="font-black text-slate-900">
                    {{ $p->codigo ?? '—' }}
                  </td>

                  <td>
                    <div class="font-black text-slate-900">{{ $p->nombre }}</div>
                    <div class="text-xs text-slate-500 mt-1 max-w-[260px] truncate">
                      {{ $p->ubicacion ?? '—' }}
                    </div>

                    @if(!empty($p->descripcion))
                      <div class="text-xs text-slate-400 mt-1 max-w-[260px] truncate">
                        {{ $p->descripcion }}
                      </div>
                    @endif

                    @if((int)($p->activo ?? 1) !== 1)
                      <div class="mt-2">
                        <span class="vs-badge vs-badge-danger">Inactivo</span>
                      </div>
                    @endif
                  </td>

                  <td class="font-bold text-slate-600">
                    {{ $responsableNombre }}
                  </td>

                  <td style="text-align:center">
                    <span class="vs-badge {{ $badge }}">
                      {{ $estadoLabel }}
                    </span>
                  </td>

                  <td style="min-width:210px">
                    <div class="flex items-center justify-between gap-3 mb-2">
                      <span class="text-xs font-bold text-slate-500">Avance</span>
                      <span class="text-xs font-black {{ $progressText }}">
                        {{ number_format($porcentaje, 0) }}%
                      </span>
                    </div>

                    <div class="w-full h-2.5 rounded-full bg-slate-200 overflow-hidden">
                      <div class="h-2.5 rounded-full {{ $progressColor }} transition-all duration-500"
                           style="width: {{ $porcentaje }}%;"></div>
                    </div>
                  </td>

                  <td class="text-slate-700">
                    <div class="text-xs font-bold">
                      <div>Inicio: {{ $p->fecha_inicio?->format('Y-m-d') ?? '—' }}</div>
                      <div>Fin: {{ $p->fecha_fin?->format('Y-m-d') ?? '—' }}</div>
                    </div>
                  </td>

                  <td style="text-align:right" class="font-black text-slate-900 whitespace-nowrap">
                    $ {{ number_format((float)($p->presupuesto ?? 0), 2, '.', ',') }}
                  </td>

                  <td>
                    <div class="flex justify-center gap-2">
                      @can('proyectos.ver')
                        <a href="{{ route('admin.proyectos.show', $p->id) }}"
                           title="Ver proyecto"
                           class="action-btn hover:text-sky-600">
                          {!! $icon('eye') !!}
                        </a>
                      @endcan

                      @can('proyectos.editar')
                        <a href="{{ route('admin.proyectos.edit', $p->id) }}"
                           title="Editar proyecto"
                           class="action-btn hover:text-indigo-600">
                          {!! $icon('edit') !!}
                        </a>
                      @endcan
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center py-14 text-slate-500 font-bold">
                    No hay proyectos para los filtros seleccionados.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="px-5 py-4 border-t border-slate-200 bg-white">
          {{ $proyectos->links() }}
        </div>
      </div>

    </div>
  </div>

</div>
@endsection