@extends('layouts.base')
@section('title','Mis tareas')

@section('content')
@php
  $estadoOptions = [
    '' => 'Estado',
    'pendiente' => 'Pendiente',
    'en_proceso' => 'En proceso',
    'finalizada' => 'Finalizada',
    'pausada' => 'Pausada',
  ];

  $estadoLabel = [
    'pendiente' => 'Pendiente',
    'en_proceso' => 'En proceso',
    'finalizada' => 'Finalizada',
    'pausada' => 'Pausada',
  ];

  $estadoBadge = [
    'pendiente' => 'vs-badge-danger',
    'en_proceso' => 'vs-badge-indigo',
    'finalizada' => 'vs-badge-ok',
    'pausada' => 'vs-badge-warn',
  ];

  $columns = [
    'pendiente' => [
      'title' => 'Pendientes',
      'tone' => 'rose',
    ],
    'en_proceso' => [
      'title' => 'En proceso',
      'tone' => 'indigo',
    ],
    'pausada' => [
      'title' => 'Pausadas',
      'tone' => 'amber',
    ],
    'finalizada' => [
      'title' => 'Finalizadas',
      'tone' => 'emerald',
    ],
  ];

  $tareasPorEstado = collect($tareas)->groupBy('estado');

  $icon = function($name){
    if($name==='eye') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='check') return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    if($name==='folder') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M3 7.5A2.5 2.5 0 0 1 5.5 5H10l2 2h6.5A2.5 2.5 0 0 1 21 9.5v7A2.5 2.5 0 0 1 18.5 19h-13A2.5 2.5 0 0 1 3 16.5v-9Z" stroke="currentColor" stroke-width="2"/></svg>';
    return '';
  };
@endphp

<style>
.vs-wrap{max-width:1450px;margin:0 auto;padding:18px}
.vs-head{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:18px}
.vs-title{font-size:30px;font-weight:950;color:#0f172a;line-height:1}
.vs-sub{margin-top:7px;font-size:13px;color:#64748b;font-weight:700}
.vs-actions{display:flex;gap:10px;flex-wrap:wrap}

.vs-btn{height:40px;border:none;border-radius:16px;padding:0 18px;display:inline-flex;align-items:center;justify-content:center;gap:10px;font-weight:900;text-decoration:none;cursor:pointer;transition:.2s ease}
.vs-btn:hover{transform:translateY(-2px)}
.vs-btn-primary{background:linear-gradient(135deg,#0f172a,#0b4f7d);color:white;box-shadow:0 12px 30px rgba(15,23,42,.15)}
.vs-btn-light{background:#f1f5f9;color:#334155;border:1px solid #e2e8f0}
.vs-btn-light:hover{background:#e2e8f0;color:#334155}

.kpi-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:14px;margin-bottom:16px}
@media(max-width:1100px){.kpi-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:640px){.kpi-grid{grid-template-columns:1fr}}

.kpi{background:white;border-radius:24px;border:1px solid #e2e8f0;padding:16px;box-shadow:0 14px 40px rgba(15,23,42,.06)}
.kpi-label{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#64748b;font-weight:950}
.kpi-value{margin-top:8px;font-size:24px;font-weight:950;color:#0f172a}

.vs-card{background:white;border-radius:28px;border:1px solid #e2e8f0;box-shadow:0 18px 50px rgba(15,23,42,.07);overflow:hidden}
.vs-card-body{padding:14px 18px}

.filter-grid{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
.input-vs,.select-vs{height:40px;border:1px solid #dbe2ea;border-radius:16px;padding:0 14px;font-weight:700;outline:none;background:white;color:#0f172a}
.input-vs:focus,.select-vs:focus{border-color:#38bdf8;box-shadow:0 0 0 4px rgba(14,165,233,.12)}

.kanban-wrap{display:grid;grid-template-columns:repeat(4,minmax(280px,1fr));gap:16px;align-items:start}
@media(max-width:1250px){.kanban-wrap{grid-template-columns:repeat(2,minmax(280px,1fr))}}
@media(max-width:720px){.kanban-wrap{grid-template-columns:1fr}}

.kanban-col{background:#f8fafc;border:1px solid #e2e8f0;border-radius:28px;padding:12px;min-height:320px}
.kanban-head{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:12px}
.kanban-title{font-size:13px;font-weight:950;color:#0f172a;text-transform:uppercase;letter-spacing:.06em}
.kanban-count{height:28px;min-width:28px;border-radius:999px;display:inline-flex;align-items:center;justify-content:center;background:white;border:1px solid #e2e8f0;font-size:12px;font-weight:950;color:#475569}

.task-card{background:white;border:1px solid #e2e8f0;border-radius:24px;padding:14px;margin-bottom:12px;box-shadow:0 12px 30px rgba(15,23,42,.06);transition:.2s ease}
.task-card:hover{transform:translateY(-2px);box-shadow:0 16px 40px rgba(15,23,42,.10)}
.task-title{font-size:14px;font-weight:950;color:#0f172a;line-height:1.25}
.task-desc{margin-top:5px;font-size:12px;color:#64748b;line-height:1.45}
.task-meta{display:flex;align-items:center;gap:7px;flex-wrap:wrap;margin-top:10px}

.vs-badge{display:inline-flex;align-items:center;padding:6px 10px;border-radius:999px;font-size:10px;font-weight:950;border:1px solid transparent}
.vs-badge-slate{background:#f1f5f9;color:#334155;border-color:#e2e8f0}
.vs-badge-indigo{background:#eef2ff;color:#4338ca;border-color:#c7d2fe}
.vs-badge-warn{background:#fef3c7;color:#92400e;border-color:#fde68a}
.vs-badge-ok{background:#dcfce7;color:#166534;border-color:#bbf7d0}
.vs-badge-danger{background:#fee2e2;color:#991b1b;border-color:#fecaca}

.progress-bg{width:100%;height:7px;border-radius:999px;background:#e2e8f0;overflow:hidden}
.progress-bar{height:7px;border-radius:999px;background:#4f46e5}

.action-btn{height:32px;width:32px;border-radius:12px;border:1px solid #e2e8f0;background:white;display:inline-flex;align-items:center;justify-content:center;color:#64748b;transition:.2s ease}
.action-btn:hover{background:#f8fafc;transform:translateY(-1px)}
</style>

<div class="vs-wrap">

  <div class="vs-head">
    <div>
      <div class="vs-title">Mis tareas</div>
      <div class="vs-sub">Vista Kanban para controlar tareas asignadas, vencimientos y avance.</div>
    </div>

    <div class="vs-actions">
      <a href="{{ route('admin.proyectos') }}" class="vs-btn vs-btn-light">
        {!! $icon('folder') !!} Ver proyectos
      </a>
    </div>
  </div>

  @if(session('ok'))
    <div class="mb-4 rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 font-black">
      {{ session('ok') }}
    </div>
  @endif

  @if($errors->any())
    <div class="mb-4 rounded-3xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 font-black">
      {{ $errors->first() }}
    </div>
  @endif

  <div class="kpi-grid">
    <div class="kpi">
      <div class="kpi-label">Total</div>
      <div class="kpi-value">{{ $stats['total'] }}</div>
    </div>

    <div class="kpi">
      <div class="kpi-label">Pendientes</div>
      <div class="kpi-value text-rose-700">{{ $stats['pendientes'] }}</div>
    </div>

    <div class="kpi">
      <div class="kpi-label">En proceso</div>
      <div class="kpi-value text-indigo-700">{{ $stats['en_proceso'] }}</div>
    </div>

    <div class="kpi">
      <div class="kpi-label">Vencidas</div>
      <div class="kpi-value text-amber-700">{{ $stats['vencidas'] }}</div>
    </div>

    <div class="kpi">
      <div class="kpi-label">Finalizadas</div>
      <div class="kpi-value text-emerald-700">{{ $stats['finalizadas'] }}</div>
    </div>
  </div>

  <div class="vs-card mb-4">
    <div class="vs-card-body">
      <form method="GET" action="{{ route('admin.proyectos.mis_tareas') }}" class="filter-grid">
        <input type="text"
               name="q"
               value="{{ $q }}"
               placeholder="Buscar tarea, proyecto o fase..."
               class="input-vs"
               style="width:280px;max-width:100%;">

        <select name="proyecto_id" class="select-vs">
          <option value="">Proyecto</option>
          @foreach($proyectos as $p)
            <option value="{{ $p->id }}" @selected((string)$proyectoId === (string)$p->id)>
              {{ $p->nombre }}
            </option>
          @endforeach
        </select>

        <select name="estado" class="select-vs">
          @foreach($estadoOptions as $value => $label)
            <option value="{{ $value }}" @selected($estado === $value)>{{ $label }}</option>
          @endforeach
        </select>

        <label class="h-10 rounded-2xl border border-slate-200 bg-slate-50 px-4 flex items-center gap-2 text-sm font-black text-slate-700">
          <input type="checkbox" name="vencidas" value="1" @checked($vencidas) class="rounded border-slate-300">
          Vencidas
        </label>

        <button type="submit" class="vs-btn vs-btn-primary">Filtrar</button>

        <a href="{{ route('admin.proyectos.mis_tareas') }}" class="vs-btn vs-btn-light">
          Limpiar
        </a>
      </form>
    </div>
  </div>

  <div class="kanban-wrap">
    @foreach($columns as $estadoKey => $col)
      @php
        $items = $tareasPorEstado->get($estadoKey, collect());
      @endphp

      <div class="kanban-col">
        <div class="kanban-head">
          <div class="kanban-title">{{ $col['title'] }}</div>
          <div class="kanban-count">{{ $items->count() }}</div>
        </div>

        @forelse($items as $t)
          @php
            $vencida = $t->esta_vencida ?? false;
            $badgeClass = $estadoBadge[$t->estado] ?? 'vs-badge-slate';
            $label = $estadoLabel[$t->estado] ?? ucfirst((string)$t->estado);
            $avance = max(0, min(100, (float)$t->porcentaje));
          @endphp

          <div class="task-card {{ $vencida ? 'border-rose-300 bg-rose-50/60' : '' }}">
            <div class="flex items-start justify-between gap-2">
              <div class="min-w-0">
                <div class="task-title">{{ $t->nombre }}</div>

                @if($t->descripcion)
                  <div class="task-desc">
                    {{ \Illuminate\Support\Str::limit($t->descripcion, 95) }}
                  </div>
                @endif
              </div>

              <a href="{{ route('admin.proyectos.show', $t->proyecto_id) }}"
                 title="Ver proyecto"
                 class="action-btn hover:text-indigo-600">
                {!! $icon('eye') !!}
              </a>
            </div>

            <div class="task-meta">
              <span class="vs-badge {{ $badgeClass }}">{{ $label }}</span>

              @if($vencida)
                <span class="vs-badge vs-badge-danger">Vencida</span>
              @endif
            </div>

            <div class="mt-3 text-xs font-black text-slate-500">
              Proyecto
            </div>
            <div class="text-sm font-black text-slate-800 truncate" title="{{ $t->proyecto->nombre ?? '—' }}">
              {{ $t->proyecto->nombre ?? '—' }}
            </div>

            <div class="mt-2 text-xs font-bold text-slate-500 truncate" title="{{ $t->fase->nombre ?? 'Sin fase' }}">
              Fase: {{ $t->fase->nombre ?? 'Sin fase' }}
            </div>

            <div class="mt-3 flex items-center justify-between">
              <div class="text-xs font-black {{ $vencida ? 'text-rose-700' : 'text-slate-500' }}">
                Vence: {{ $t->fecha_fin?->format('Y-m-d') ?? '—' }}
              </div>

              <div class="text-xs font-black text-slate-900">
                {{ number_format($avance, 0) }}%
              </div>
            </div>

            <div class="mt-2 progress-bg">
              <div class="progress-bar" style="width: {{ $avance }}%"></div>
            </div>

            <form method="POST"
                  action="{{ route('admin.proyectos.mis_tareas.update', $t->id) }}"
                  class="mt-4 grid grid-cols-[1fr_70px_34px] gap-2 items-center">
              @csrf
              @method('PUT')

              <select name="estado" class="select-vs" style="height:34px;border-radius:13px;font-size:11px;padding:0 8px;">
                <option value="pendiente" @selected($t->estado === 'pendiente')>Pendiente</option>
                <option value="en_proceso" @selected($t->estado === 'en_proceso')>Proceso</option>
                <option value="finalizada" @selected($t->estado === 'finalizada')>Finalizada</option>
                <option value="pausada" @selected($t->estado === 'pausada')>Pausada</option>
              </select>

              <input type="text"
                     name="porcentaje"
                     inputmode="decimal"
                     value="{{ old('porcentaje', (float)$t->porcentaje) }}"
                     oninput="this.value=this.value.replace(/[^0-9.]/g,'')"
                     class="input-vs text-center"
                     style="height:34px;border-radius:13px;font-size:11px;padding:0 6px;"
                     placeholder="%">

              <button type="submit"
                      title="Guardar avance"
                      class="h-[34px] w-[34px] rounded-xl bg-emerald-600 text-white flex items-center justify-center hover:bg-emerald-700 transition">
                {!! $icon('check') !!}
              </button>
            </form>
          </div>
        @empty
          <div class="rounded-2xl border border-dashed border-slate-300 bg-white/70 p-5 text-center text-sm font-bold text-slate-400">
            Sin tareas
          </div>
        @endforelse
      </div>
    @endforeach
  </div>

</div>
@endsection