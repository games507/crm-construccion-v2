@extends('layouts.base')
@section('title','Detalle del Proyecto')

@section('content')
@php
  $estado = (string)($proyecto->estado ?? '');
  $estadoLabel = $estado ? ucfirst(str_replace('_', ' ', $estado)) : '—';

  $badgeEstado = match($estado) {
    'planeado'     => 'vs-badge-slate',
    'en_ejecucion' => 'vs-badge-indigo',
    'pausado'      => 'vs-badge-warn',
    'finalizado'   => 'vs-badge-ok',
    default        => 'vs-badge-slate',
  };

  $responsableNombre = $proyecto->responsable->name
    ?? $proyecto->responsable->nombre
    ?? $proyecto->responsable->nombre_completo
    ?? '—';

  $fi = $proyecto->fecha_inicio?->format('Y-m-d') ?? '—';
  $ff = $proyecto->fecha_fin?->format('Y-m-d') ?? '—';

  $avance = max(0, min(100, (float)($proyecto->porcentaje ?? $proyecto->avance ?? 0)));

  $avanceColor = match (true) {
    $avance >= 100 => 'from-emerald-500 to-emerald-600',
    $avance >= 70  => 'from-green-500 to-green-600',
    $avance >= 31  => 'from-amber-500 to-amber-600',
    $avance > 0    => 'from-rose-500 to-rose-600',
    default        => 'from-slate-400 to-slate-500',
  };

  $avanceText = match (true) {
    $avance >= 100 => 'text-emerald-700',
    $avance >= 70  => 'text-green-700',
    $avance >= 31  => 'text-amber-700',
    $avance > 0    => 'text-rose-700',
    default        => 'text-slate-500',
  };

  $presupuesto = (float)($finanzas['presupuesto'] ?? 0);
  $ejecutado = (float)($finanzas['ejecutado'] ?? 0);
  $saldoDisponible = (float)($finanzas['saldo_disponible'] ?? 0);
  $porcentajeConsumido = max(0, min(100, (float)($finanzas['porcentaje_consumido'] ?? 0)));

  $consumoColor = match (true) {
    $porcentajeConsumido >= 100 => 'from-rose-600 to-red-700',
    $porcentajeConsumido >= 70  => 'from-amber-500 to-orange-600',
    $porcentajeConsumido > 0    => 'from-sky-500 to-indigo-600',
    default                     => 'from-slate-400 to-slate-500',
  };

  $consumoText = match (true) {
    $porcentajeConsumido >= 100 => 'text-rose-700',
    $porcentajeConsumido >= 70  => 'text-amber-700',
    $porcentajeConsumido > 0    => 'text-sky-700',
    default                     => 'text-slate-500',
  };

  $tiposCosto = \App\Models\ProyectoCosto::tipos();
  $costosLista = $costos ?? collect();

  $icon = function($name){
    if($name==='back') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    if($name==='edit') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M4 20h4l10-10-4-4L4 16v4Z" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='plus') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 5v14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='trash') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M3 6h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M8 6V4h8v2" stroke="currentColor" stroke-width="2"/><path d="M6 6l1 14h10l1-14" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='code') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="m8 9-3 3 3 3m8-6 3 3-3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    if($name==='user') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/><path d="M4 21a8 8 0 0 1 16 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='map') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="m9 19-6 2V5l6-2 6 2 6-2v16l-6 2-6-2Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M9 3v16m6-14v16" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='calendar') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M8 3v3m8-3v3M4 9h16M5 5h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    return '';
  };
@endphp

<style>
.vs-wrap{max-width:1450px;margin:0 auto;padding:18px}
.vs-head{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:18px}
.vs-title{font-size:30px;font-weight:950;color:#0f172a;line-height:1}
.vs-sub{margin-top:7px;font-size:13px;color:#64748b;font-weight:700}
.vs-actions{display:flex;align-items:center;gap:10px;flex-wrap:wrap}

.vs-btn{height:40px;border:none;border-radius:16px;padding:0 18px;display:inline-flex;align-items:center;justify-content:center;gap:10px;font-weight:900;text-decoration:none;cursor:pointer;transition:.2s ease}
.vs-btn:hover{transform:translateY(-2px)}
.vs-btn-primary{background:linear-gradient(135deg,#0f172a,#0b4f7d);color:white;box-shadow:0 12px 30px rgba(15,23,42,.15)}
.vs-btn-primary:hover{color:white}
.vs-btn-light{background:#f1f5f9;color:#334155;border:1px solid #e2e8f0}
.vs-btn-light:hover{background:#e2e8f0;color:#334155}
.vs-btn-green{background:#059669;color:white;box-shadow:0 12px 30px rgba(5,150,105,.15)}
.vs-btn-green:hover{color:white}
.vs-btn-danger{background:#e11d48;color:white;box-shadow:0 12px 30px rgba(225,29,72,.15)}
.vs-btn-danger:hover{color:white}

.vs-card{background:white;border-radius:28px;border:1px solid #e2e8f0;box-shadow:0 18px 50px rgba(15,23,42,.07);overflow:hidden}
.vs-card-head{padding:18px;border-bottom:1px solid #e2e8f0;display:flex;align-items:flex-start;justify-content:space-between;gap:14px;flex-wrap:wrap}
.vs-card-title{font-weight:950;color:#0f172a;font-size:16px}
.vs-card-sub{font-size:12px;color:#64748b;margin-top:4px;font-weight:700}
.vs-card-body{padding:18px}

.vs-grid-3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}
.vs-grid-2{display:grid;grid-template-columns:2fr 1fr;gap:18px}
@media(max-width:1100px){.vs-grid-3,.vs-grid-2{grid-template-columns:1fr}}

.kpi-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px}
@media(max-width:900px){.kpi-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:620px){.kpi-grid{grid-template-columns:1fr}}

.kpi{background:white;border-radius:24px;border:1px solid #e2e8f0;padding:16px;box-shadow:0 14px 40px rgba(15,23,42,.06)}
.kpi-label{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#64748b;font-weight:950}
.kpi-value{margin-top:10px;font-size:24px;font-weight:950;color:#0f172a}

.vs-badge{display:inline-flex;align-items:center;padding:7px 12px;border-radius:999px;font-size:11px;font-weight:950;border:1px solid transparent}
.vs-badge-slate{background:#f1f5f9;color:#334155;border-color:#e2e8f0}
.vs-badge-indigo{background:#eef2ff;color:#4338ca;border-color:#c7d2fe}
.vs-badge-warn{background:#fef3c7;color:#92400e;border-color:#fde68a}
.vs-badge-ok{background:#dcfce7;color:#166534;border-color:#bbf7d0}
.vs-badge-danger{background:#fee2e2;color:#991b1b;border-color:#fecaca}

.info-row{display:flex;gap:12px;align-items:flex-start}
.info-icon{height:38px;width:38px;border-radius:14px;background:#f8fafc;border:1px solid #e2e8f0;color:#64748b;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.info-label{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#64748b;font-weight:950}
.info-value{margin-top:3px;color:#0f172a;font-weight:900;font-size:13px}

.input-vs,.select-vs,.textarea-vs{width:100%;border:1px solid #dbe2ea;border-radius:16px;padding:0 14px;font-weight:700;outline:none;background:white;color:#0f172a}
.input-vs,.select-vs{height:40px}
.textarea-vs{padding-top:12px;padding-bottom:12px;min-height:90px;resize:vertical}
.input-vs:focus,.select-vs:focus,.textarea-vs:focus{border-color:#38bdf8;box-shadow:0 0 0 4px rgba(14,165,233,.12)}
.form-mini{border:1px solid #e2e8f0;background:#f8fafc;border-radius:22px;padding:16px;margin-top:14px}

.item-card{border:1px solid #e2e8f0;background:white;border-radius:22px;padding:16px;margin-bottom:14px}
.item-card:last-child{margin-bottom:0}
.action-btn{height:32px;width:32px;border-radius:12px;border:1px solid #e2e8f0;background:white;display:inline-flex;align-items:center;justify-content:center;color:#64748b;transition:.2s ease}
.action-btn:hover{background:#f8fafc;transform:translateY(-1px)}

.soft-box{border-radius:20px;border:1px solid #e2e8f0;background:#f8fafc;padding:14px}
.progress-bg{width:100%;background:#e2e8f0;border-radius:999px;overflow:hidden}
</style>

<div class="vs-wrap" x-data="{ tab:'resumen' }">
  <div class="vs-head">
    <div>
      <div class="vs-title">{{ $proyecto->nombre }}</div>
      <div class="vs-sub">Panel ejecutivo del proyecto, costos, fases, tareas y cuentas por pagar.</div>
    </div>

    <div class="vs-actions">
      <span class="vs-badge {{ $badgeEstado }}">{{ $estadoLabel }}</span>

      <a href="{{ route('admin.proyectos') }}" class="vs-btn vs-btn-light">
        {!! $icon('back') !!} Volver
      </a>

      @can('proyectos.editar')
        <a href="{{ route('admin.proyectos.edit', $proyecto->id) }}" class="vs-btn vs-btn-primary">
          {!! $icon('edit') !!} Editar
        </a>
      @endcan
    </div>
  </div>

  @if (session('ok'))
    <div class="mb-4 rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 font-black">
      {{ session('ok') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="mb-4 rounded-3xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 font-black">
      {{ $errors->first() }}
    </div>
  @endif
<div class="mb-5">

  <div class="vs-card p-2">

    <div class="flex flex-wrap gap-2">

      @php
        $tabs = [
          'resumen' => 'Resumen',
          'costos'  => 'Costos',
          'cxp'     => 'Cuentas por pagar',
          'fases'   => 'Fases',
          'tareas'  => 'Tareas',
        ];
      @endphp

      @foreach($tabs as $key => $label)
        <button
          type="button"
          @click="tab='{{ $key }}'"
          :class="tab==='{{ $key }}'
            ? 'bg-slate-900 text-white shadow-lg'
            : 'bg-white text-slate-600 border border-slate-200'"
          class="h-11 px-5 rounded-2xl text-sm font-black transition-all duration-200"
        >
          {{ $label }}
        </button>
      @endforeach

    </div>

  </div>

</div>

<div x-show="tab==='resumen'" x-transition>
  <div class="vs-grid-2 mb-4">

    <div class="vs-card">
      <div class="vs-card-head">
        <div>
          <div class="vs-card-title">Avance del proyecto</div>
          <div class="vs-card-sub">Resumen de avance operativo y estado general.</div>
        </div>

        <div class="text-right">
          <div class="text-[11px] uppercase tracking-wide text-slate-500 font-black">Avance</div>
          <div class="text-2xl font-black {{ $avanceText }}">
            {{ number_format($avance, 2, '.', ',') }}%
          </div>
        </div>
      </div>

      <div class="vs-card-body">
        <div class="progress-bg h-4 shadow-inner">
          <div class="bg-gradient-to-r {{ $avanceColor }} h-4 rounded-full transition-all duration-500 ease-out"
               style="width: {{ $avance }}%;"></div>
        </div>

        <div class="mt-5 kpi-grid">
          <div class="soft-box">
            <div class="kpi-label">Tareas</div>
            <div class="kpi-value">{{ $stats['tareas_total'] }}</div>
          </div>

          <div class="soft-box">
            <div class="kpi-label">Fases</div>
            <div class="kpi-value">{{ $stats['fases_total'] }}</div>
          </div>

          <div class="soft-box">
            <div class="kpi-label">Fases OK</div>
            <div class="kpi-value text-emerald-700">{{ $stats['fases_completadas'] }}</div>
          </div>

          <div class="soft-box">
            <div class="kpi-label">Finalizadas</div>
            <div class="kpi-value text-emerald-700">{{ $stats['tareas_finalizadas'] }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="vs-card">
      <div class="vs-card-head">
        <div>
          <div class="vs-card-title">Información general</div>
          <div class="vs-card-sub">Datos principales del proyecto.</div>
        </div>
      </div>

      <div class="vs-card-body space-y-4">
        <div class="info-row">
          <div class="info-icon">{!! $icon('code') !!}</div>
          <div>
            <div class="info-label">Código</div>
            <div class="info-value">{{ $proyecto->codigo ?? '—' }}</div>
          </div>
        </div>

        <div class="info-row">
          <div class="info-icon">{!! $icon('user') !!}</div>
          <div>
            <div class="info-label">Responsable</div>
            <div class="info-value">{{ $responsableNombre }}</div>
          </div>
        </div>

        <div class="info-row">
          <div class="info-icon">{!! $icon('map') !!}</div>
          <div>
            <div class="info-label">Ubicación</div>
            <div class="info-value">{{ $proyecto->ubicacion ?? '—' }}</div>
          </div>
        </div>

        <div class="info-row">
          <div class="info-icon">{!! $icon('calendar') !!}</div>
          <div>
            <div class="info-label">Fechas</div>
            <div class="info-value">{{ $fi }} / {{ $ff }}</div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="vs-grid-2 mb-4">

    <div class="vs-card">
      <div class="vs-card-head">
        <div>
          <div class="vs-card-title">Control de costos</div>
          <div class="vs-card-sub">Resumen financiero del proyecto.</div>
        </div>

        <div class="text-right">
          <div class="text-[11px] uppercase tracking-wide text-slate-500 font-black">% consumido</div>
          <div class="text-2xl font-black {{ $consumoText }}">
            {{ number_format($porcentajeConsumido, 2, '.', ',') }}%
          </div>
        </div>
      </div>

      <div class="vs-card-body">
        <div class="kpi-grid">
          <div class="soft-box">
            <div class="kpi-label">Presupuesto</div>
            <div class="kpi-value">$ {{ number_format($presupuesto, 2, '.', ',') }}</div>
          </div>

          <div class="soft-box">
            <div class="kpi-label">Ejecutado</div>
            <div class="kpi-value text-sky-700">$ {{ number_format($ejecutado, 2, '.', ',') }}</div>
          </div>

          <div class="soft-box">
            <div class="kpi-label">Disponible</div>
            <div class="kpi-value {{ $saldoDisponible < 0 ? 'text-rose-700' : 'text-emerald-700' }}">
              $ {{ number_format($saldoDisponible, 2, '.', ',') }}
            </div>
          </div>

          <div class="soft-box">
            <div class="kpi-label">Registros</div>
            <div class="kpi-value text-indigo-700">{{ $costosLista->count() }}</div>
          </div>
        </div>

        <div class="mt-5">
          <div class="flex justify-between items-center gap-3 mb-2">
            <span class="text-sm font-black text-slate-600">Consumo del presupuesto</span>
            <span class="text-sm font-black {{ $consumoText }}">
              {{ number_format($porcentajeConsumido, 2, '.', ',') }}%
            </span>
          </div>

          <div class="progress-bg h-4 shadow-inner">
            <div class="bg-gradient-to-r {{ $consumoColor }} h-4 rounded-full transition-all duration-500 ease-out"
                 style="width: {{ min($porcentajeConsumido, 100) }}%;"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="vs-card">
      <div class="vs-card-head">
        <div>
          <div class="vs-card-title">Descripción</div>
          <div class="vs-card-sub">Detalle general del proyecto.</div>
        </div>
      </div>

      <div class="vs-card-body">
        <div class="text-sm leading-7 text-slate-700 font-semibold">
          {{ $proyecto->descripcion ?: 'Sin descripción registrada.' }}
        </div>
      </div>
    </div>

  </div>
</div>

<div x-show="tab==='costos'" x-transition>
  <div class="vs-card mb-4">
    <div class="vs-card-head">
      <div>
        <div class="vs-card-title">Costos del proyecto</div>
        <div class="vs-card-sub">Listado y registro de costos.</div>
      </div>

      <details class="group">
        <summary class="list-none cursor-pointer vs-btn vs-btn-primary">
          {!! $icon('plus') !!} Agregar costo
        </summary>

        <div class="form-mini">
          <form method="POST" action="{{ route('admin.proyectos.costos.store') }}">
            @csrf
            <input type="hidden" name="proyecto_id" value="{{ $proyecto->id }}">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
              <select name="tipo" class="select-vs">
                <option value="">Tipo de costo</option>
                @foreach($tiposCosto as $key => $label)
                  <option value="{{ $key }}" @selected(old('tipo') == $key)>{{ $label }}</option>
                @endforeach
              </select>

              <input type="text" name="categoria" value="{{ old('categoria') }}" placeholder="Categoría" class="input-vs">
              <input type="text" name="proveedor" value="{{ old('proveedor') }}" placeholder="Proveedor / acreedor" class="input-vs">
              <input type="number" step="0.01" name="monto" value="{{ old('monto') }}" placeholder="Monto" class="input-vs">
              <input type="date" name="fecha" value="{{ old('fecha') }}" class="input-vs">

              <select name="estado_pago" class="select-vs">
                <option value="pendiente" @selected(old('estado_pago') == 'pendiente')>Pendiente</option>
                <option value="parcial" @selected(old('estado_pago') == 'parcial')>Parcial</option>
                <option value="pagado" @selected(old('estado_pago') == 'pagado')>Pagado</option>
              </select>

              <label class="h-10 rounded-2xl border border-slate-200 px-3 flex items-center gap-2 text-sm text-slate-700 bg-white font-bold">
                <input type="checkbox" name="requiere_pago" value="1" class="rounded border-slate-300" @checked(old('requiere_pago'))>
                Requiere pago
              </label>

              <button class="vs-btn vs-btn-primary">Guardar costo</button>
            </div>

            <div class="mt-3">
              <textarea name="descripcion" rows="3" placeholder="Descripción del costo" class="textarea-vs">{{ old('descripcion') }}</textarea>
            </div>
          </form>
        </div>
      </details>
    </div>

    <div class="vs-card-body">
      @forelse($costosLista as $costo)
        @php
          $estadoPago = (string) ($costo->estado_pago ?? 'pendiente');

          $badgePago = match($estadoPago) {
            'pagado'    => 'vs-badge-ok',
            'parcial'   => 'vs-badge-warn',
            default     => 'vs-badge-danger',
          };

          $tipoLabel = $tiposCosto[$costo->tipo] ?? ucfirst(str_replace('_', ' ', (string) $costo->tipo));
        @endphp

        <div class="item-card">
          <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
            <div class="min-w-0">
              <div class="font-black text-slate-900">{{ $tipoLabel }}</div>

              <div class="mt-2 flex flex-wrap gap-2">
                @if(!empty($costo->categoria))
                  <span class="vs-badge vs-badge-slate">{{ $costo->categoria }}</span>
                @endif

                <span class="vs-badge {{ $badgePago }}">{{ ucfirst($estadoPago) }}</span>

                @if((bool) ($costo->requiere_pago ?? false))
                  <span class="vs-badge vs-badge-indigo">Requiere pago</span>
                @endif
              </div>

              <div class="text-sm text-slate-500 mt-2 font-bold">
                Proveedor: {{ $costo->proveedor ?: '—' }} · Fecha: {{ $costo->fecha?->format('Y-m-d') ?? '—' }}
              </div>

              @if(!empty($costo->descripcion))
                <div class="mt-3 text-sm text-slate-600">{{ $costo->descripcion }}</div>
              @endif
            </div>

            <div class="text-right">
              <div class="kpi-label">Monto</div>
              <div class="text-lg font-black text-slate-900">
                $ {{ number_format((float) $costo->monto, 2, '.', ',') }}
              </div>

              <div class="mt-3 flex items-center justify-end gap-2">
                <a href="{{ route('admin.proyectos.costos.edit', $costo->id) }}" title="Editar costo" class="action-btn hover:text-indigo-600">
                  {!! $icon('edit') !!}
                </a>

                <form method="POST" action="{{ route('admin.proyectos.costos.destroy', $costo->id) }}" onsubmit="return confirm('¿Seguro que deseas eliminar este costo?');" class="inline-flex">
                  @csrf
                  @method('DELETE')

                  <button type="submit" title="Eliminar costo" class="action-btn hover:text-rose-600">
                    {!! $icon('trash') !!}
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="text-sm text-slate-500 font-bold">No hay costos registrados aún.</div>
      @endforelse
    </div>
  </div>
</div>

<div x-show="tab==='cxp'" x-transition>
  <div class="vs-card mb-4">
    <div class="vs-card-head">
      <div>
        <div class="vs-card-title">Cuentas por pagar</div>
        <div class="vs-card-sub">Control de saldos pendientes del proyecto.</div>
      </div>

      <details class="group">
        <summary class="list-none cursor-pointer vs-btn vs-btn-primary">
          {!! $icon('plus') !!} Agregar cuenta
        </summary>

        <div class="form-mini">
          <form method="POST" action="{{ route('admin.cuentas.store') }}">
            @csrf
            <input type="hidden" name="proyecto_id" value="{{ $proyecto->id }}">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
              <input type="text" name="proveedor" placeholder="Proveedor / acreedor" class="input-vs" required>
              <input type="number" step="0.01" name="monto_total" placeholder="Monto total" class="input-vs" required>
              <input type="date" name="fecha" class="input-vs">
              <input type="date" name="fecha_vencimiento" class="input-vs">

              <textarea name="descripcion" rows="3" placeholder="Descripción" class="textarea-vs lg:col-span-3"></textarea>

              <button class="vs-btn vs-btn-primary">Guardar cuenta</button>
            </div>
          </form>
        </div>
      </details>
    </div>

    <div class="vs-card-body">
      @forelse(($proyecto->cuentasPorPagar ?? collect()) as $c)
        @php
          $estadoCxP = (string)($c->estado ?? 'pendiente');

          $badgeCxP = match($estadoCxP) {
            'pagado'  => 'vs-badge-ok',
            'parcial' => 'vs-badge-warn',
            default   => 'vs-badge-danger',
          };
        @endphp

        <div class="item-card">
          <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
            <div class="min-w-0">
              <div class="font-black text-slate-900">{{ $c->proveedor }}</div>

              <div class="mt-2">
                <span class="vs-badge {{ $badgeCxP }}">{{ ucfirst($estadoCxP) }}</span>
              </div>

              <div class="text-sm text-slate-500 mt-2 font-bold">
                Registro: {{ $c->fecha?->format('Y-m-d') ?? '—' }} · Vence: {{ $c->fecha_vencimiento?->format('Y-m-d') ?? '—' }}
              </div>

              @if(!empty($c->descripcion))
                <div class="mt-3 text-sm text-slate-600">{{ $c->descripcion }}</div>
              @endif
            </div>

            <div class="text-right min-w-[180px]">
              <div class="kpi-label">Saldo</div>
              <div class="text-lg font-black text-rose-700">
                $ {{ number_format((float)$c->saldo, 2, '.', ',') }}
              </div>

              <div class="mt-2 text-xs text-slate-500 font-bold">
                Total: $ {{ number_format((float)$c->monto_total, 2, '.', ',') }}
              </div>
              <div class="text-xs text-slate-500 font-bold">
                Pagado: $ {{ number_format((float)$c->monto_pagado, 2, '.', ',') }}
              </div>
            </div>
          </div>

          <form method="POST" action="{{ route('admin.cuentas.pagar', $c->id) }}" class="mt-4 flex flex-col sm:flex-row gap-2">
            @csrf
            <input type="number" step="0.01" name="monto" placeholder="Monto pago" class="input-vs sm:max-w-[180px]">
            <button class="vs-btn vs-btn-green">Registrar pago</button>
          </form>
        </div>
      @empty
        <div class="text-sm text-slate-500 font-bold">No hay cuentas por pagar.</div>
      @endforelse
    </div>
  </div>
</div>

<div x-show="tab==='fases'" x-transition>
  <div class="vs-card mb-4">
    <div class="vs-card-head">
      <div>
        <div class="vs-card-title">Fases del proyecto</div>
        <div class="vs-card-sub">Control por etapas.</div>
      </div>

      <details class="group">
        <summary class="list-none cursor-pointer vs-btn vs-btn-primary">
          {!! $icon('plus') !!} Agregar fase
        </summary>

        <div class="form-mini">
          <form method="POST" action="{{ route('admin.proyectos.fases.store') }}">
            @csrf
            <input type="hidden" name="proyecto_id" value="{{ $proyecto->id }}">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
              <input type="text" name="nombre" placeholder="Nombre de la fase" class="input-vs">
              <input type="text" inputmode="numeric" name="orden" placeholder="Orden" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="input-vs">
              <input type="text" inputmode="decimal" name="porcentaje" placeholder="%" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" class="input-vs">
              <button class="vs-btn vs-btn-primary">Guardar fase</button>
            </div>
          </form>
        </div>
      </details>
    </div>

    <div class="vs-card-body">
      @forelse($proyecto->fases as $fase)
        @php
          $porcentaje = max(0, min(100, (float)($fase->porcentaje ?? 0)));

          $faseColor = match(true) {
            $porcentaje >= 100 => 'from-emerald-500 to-emerald-600',
            $porcentaje >= 70  => 'from-green-500 to-green-600',
            $porcentaje >= 31  => 'from-amber-500 to-amber-600',
            $porcentaje > 0    => 'from-rose-500 to-rose-600',
            default            => 'from-slate-400 to-slate-500',
          };

          $faseText = match(true) {
            $porcentaje >= 100 => 'text-emerald-700',
            $porcentaje >= 70  => 'text-green-700',
            $porcentaje >= 31  => 'text-amber-700',
            $porcentaje > 0    => 'text-rose-700',
            default            => 'text-slate-500',
          };
        @endphp

        <div class="item-card">
          <div class="flex items-center justify-between gap-3">
            <div>
              <div class="font-black text-slate-900">{{ $fase->nombre }}</div>
              <div class="text-xs text-slate-500 font-bold">Orden: {{ $fase->orden ?? 0 }}</div>
            </div>

            <div class="flex items-center gap-2">
              <div class="text-sm font-black {{ $faseText }}">
                {{ number_format($porcentaje, 2, '.', ',') }}%
              </div>

              <details class="relative group">
                <summary title="Editar fase" class="list-none cursor-pointer action-btn hover:text-indigo-600">
                  {!! $icon('edit') !!}
                </summary>

                <div class="absolute right-0 z-30 mt-2 w-[320px] rounded-2xl border border-slate-200 bg-white p-4 shadow-xl">
                  <form method="POST" action="{{ route('admin.proyectos.fases.update', $fase->id) }}" class="space-y-3">
                    @csrf
                    @method('PUT')

                    <div>
                      <label class="text-xs font-bold text-slate-500">Nombre</label>
                      <input type="text" name="nombre" value="{{ $fase->nombre }}" class="mt-1 input-vs" required>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                      <div>
                        <label class="text-xs font-bold text-slate-500">Orden</label>
                        <input type="text" inputmode="numeric" name="orden" value="{{ $fase->orden ?? 0 }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="mt-1 input-vs">
                      </div>

                      <div>
                        <label class="text-xs font-bold text-slate-500">%</label>
                        <input type="text" inputmode="decimal" name="porcentaje" value="{{ number_format($porcentaje, 2, '.', '') }}" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" class="mt-1 input-vs">
                      </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-1">
                      <button type="submit" class="vs-btn vs-btn-primary">Guardar</button>
                    </div>
                  </form>
                </div>
              </details>

              <form method="POST" action="{{ route('admin.proyectos.fases.destroy', $fase->id) }}" onsubmit="return confirm('¿Seguro que deseas eliminar esta fase? Las tareas quedarán sin fase.');">
                @csrf
                @method('DELETE')
                <button type="submit" title="Eliminar fase" class="action-btn hover:text-rose-600">
                  {!! $icon('trash') !!}
                </button>
              </form>
            </div>
          </div>

          <div class="mt-3 progress-bg h-3 shadow-inner">
            <div class="h-3 rounded-full bg-gradient-to-r {{ $faseColor }} transition-all duration-500 ease-out"
                 style="width: {{ $porcentaje }}%;"></div>
          </div>
        </div>
      @empty
        <div class="text-sm text-slate-500 font-bold">No hay fases registradas.</div>
      @endforelse
    </div>
  </div>
</div>

<div x-show="tab==='tareas'" x-transition>
  <div class="vs-card">
    <div class="vs-card-head">
      <div>
        <div class="vs-card-title">Tareas del proyecto</div>
        <div class="vs-card-sub">Seguimiento operativo.</div>
      </div>

      @if(isset($usuarios) && isset($nameField))
      <details class="group">
        <summary class="list-none cursor-pointer vs-btn vs-btn-primary">
          {!! $icon('plus') !!} Agregar tarea
        </summary>

        <div class="form-mini">
          <form method="POST" action="{{ route('admin.proyectos.tareas.store') }}">
            @csrf
            <input type="hidden" name="proyecto_id" value="{{ $proyecto->id }}">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
              <input type="text" name="nombre" placeholder="Nombre de la tarea" class="input-vs">

              <select name="fase_id" class="select-vs">
                <option value="">Sin fase</option>
                @foreach($proyecto->fases as $fase)
                  <option value="{{ $fase->id }}">{{ $fase->nombre }}</option>
                @endforeach
              </select>

              <select name="responsable_id" class="select-vs">
                <option value="">Sin responsable</option>
                @foreach($usuarios as $u)
                  <option value="{{ $u->id }}">{{ $u->{$nameField} ?? ('Usuario #' . $u->id) }}</option>
                @endforeach
              </select>

              <select name="estado" class="select-vs">
                <option value="pendiente">Pendiente</option>
                <option value="en_proceso">En proceso</option>
                <option value="finalizada">Finalizada</option>
                <option value="pausada">Pausada</option>
              </select>

              <input type="date" name="fecha_inicio" class="input-vs">
              <input type="date" name="fecha_fin" class="input-vs">
              <input type="text" inputmode="decimal" name="porcentaje" placeholder="% avance" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" class="input-vs">

              <button class="vs-btn vs-btn-primary">Guardar tarea</button>
            </div>

            <div class="mt-3">
              <textarea name="descripcion" rows="3" placeholder="Descripción de la tarea" class="textarea-vs"></textarea>
            </div>
          </form>
        </div>
      </details>
      @endif
    </div>

    <div class="vs-card-body">
      @forelse($proyecto->tareas ?? [] as $tarea)
        @php
          $estadoT = (string)($tarea->estado ?? '');
          $porcentajeT = max(0, min(100, (float)($tarea->porcentaje ?? 0)));

          $responsableT = $tarea->responsable->name
            ?? $tarea->responsable->nombre
            ?? $tarea->responsable->nombre_completo
            ?? '—';

          $colorBarraT = match($estadoT) {
            'finalizada' => 'from-emerald-500 to-emerald-600',
            'en_proceso' => 'from-indigo-500 to-indigo-700',
            'pausada'    => 'from-amber-400 to-amber-500',
            default      => 'from-slate-400 to-slate-500',
          };

          $colorChipT = match($estadoT) {
            'finalizada' => 'bg-emerald-500 text-white',
            'en_proceso' => 'bg-indigo-500 text-white',
            'pausada'    => 'bg-amber-400 text-white',
            default      => 'bg-slate-500 text-white',
          };

          $hoy = now()->startOfDay();
          $fechaFinT = $tarea->fecha_fin ? \Illuminate\Support\Carbon::parse($tarea->fecha_fin)->startOfDay() : null;

          $vencida = $fechaFinT && $fechaFinT->lt($hoy) && $estadoT !== 'finalizada';
          $sinResponsable = empty($tarea->responsable_id);

          $alertClass = $vencida
            ? 'border-red-300 bg-red-50'
            : ($sinResponsable ? 'border-amber-300 bg-amber-50' : 'border-slate-200 bg-white');
        @endphp

        <div class="item-card {{ $alertClass }}">
          <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
            <div class="min-w-0">
              <div class="font-black text-slate-900">{{ $tarea->nombre }}</div>

              <div class="flex gap-2 mt-2 flex-wrap">
                @if($vencida)
                  <span class="vs-badge vs-badge-danger">Vencida</span>
                @endif

                @if($sinResponsable)
                  <span class="vs-badge vs-badge-warn">Sin responsable</span>
                @endif

                @if($estadoT === 'finalizada')
                  <span class="vs-badge vs-badge-ok">Completada</span>
                @endif
              </div>

              <div class="text-sm text-slate-500 mt-2 font-bold">
                Fase: {{ $tarea->fase->nombre ?? 'Sin fase' }} · Responsable: {{ $responsableT }}
              </div>

              @if(!empty($tarea->descripcion))
                <div class="mt-3 text-sm text-slate-600">{{ $tarea->descripcion }}</div>
              @endif

              <div class="mt-3 flex flex-wrap gap-4 text-xs text-slate-500 font-bold">
                <div>Inicio: {{ $tarea->fecha_inicio?->format('Y-m-d') ?? '—' }}</div>
                <div>Fin: {{ $tarea->fecha_fin?->format('Y-m-d') ?? '—' }}</div>
              </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 lg:justify-end">
              <form method="POST" action="{{ route('admin.proyectos.tareas.update') }}" class="flex flex-wrap items-center gap-2">
                @csrf
                <input type="hidden" name="id" value="{{ $tarea->id }}">

                <input type="text" inputmode="decimal" name="porcentaje" value="{{ number_format($porcentajeT, 2, '.', '') }}" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" class="input-vs" style="width:90px;">

                <select name="estado" class="select-vs" style="width:145px;">
                  <option value="pendiente" @selected($estadoT=='pendiente')>Pendiente</option>
                  <option value="en_proceso" @selected($estadoT=='en_proceso')>En proceso</option>
                  <option value="finalizada" @selected($estadoT=='finalizada')>Finalizada</option>
                  <option value="pausada" @selected($estadoT=='pausada')>Pausada</option>
                </select>

                <button class="vs-btn vs-btn-primary">Guardar</button>
              </form>

              <a href="{{ route('admin.proyectos.tareas.edit', $tarea->id) }}" title="Editar tarea" class="action-btn hover:text-indigo-600">
                {!! $icon('edit') !!}
              </a>

              <form method="POST" action="{{ route('admin.proyectos.tareas.destroy', $tarea->id) }}" onsubmit="return confirm('¿Seguro que deseas eliminar esta tarea?');">
                @csrf
                @method('DELETE')

                <button type="submit" title="Eliminar tarea" class="action-btn hover:text-rose-600">
                  {!! $icon('trash') !!}
                </button>
              </form>
            </div>
          </div>

          <div class="mt-3">
            <div class="flex justify-between text-sm font-bold text-slate-700">
              <span>Avance</span>
              <span class="{{ $colorChipT }} px-2 py-0.5 rounded text-xs font-bold">
                {{ number_format($porcentajeT, 0) }}%
              </span>
            </div>

            <div class="progress-bg h-3 mt-2 shadow-inner">
              <div class="bg-gradient-to-r {{ $colorBarraT }} h-3 rounded-full transition-all duration-500 ease-out"
                   style="width: {{ $porcentajeT }}%;"></div>
            </div>
          </div>
        </div>
      @empty
        <div class="text-sm text-slate-500 font-bold">No hay tareas registradas.</div>
      @endforelse

    </div>
  </div>

</div>


@endsection