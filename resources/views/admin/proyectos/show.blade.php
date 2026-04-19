@extends('layouts.base')
@section('title','Detalle del Proyecto')

@section('content')
@php
  $estado = (string)($proyecto->estado ?? '');
  $estadoLabel = $estado ? ucfirst(str_replace('_', ' ', $estado)) : '—';

  $badge = match($estado) {
    'planeado'     => 'bg-slate-100 text-slate-700 border-slate-200',
    'en_ejecucion' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
    'pausado'      => 'bg-amber-50 text-amber-700 border-amber-200',
    'finalizado'   => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    default        => 'bg-slate-100 text-slate-700 border-slate-200',
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

  // ===== ICONOS SVG =====
  $iconTasks = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 6h11M9 12h11M9 18h11M4.5 6h.01M4.5 12h.01M4.5 18h.01"/></svg>';

  $iconPending = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v6l4 2"/></svg>';

  $iconProcess = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v3m0 12v3m9-9h-3M6 12H3"/></svg>';

  $iconDone = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m5 13 4 4L19 7"/></svg>';

  $iconPaused = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 6v12M14 6v12"/></svg>';

  $iconPhase = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7.5A2.5 2.5 0 0 1 5.5 5H10l2 2h6.5A2.5 2.5 0 0 1 21 9.5v7"/></svg>';

  $iconState = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="9"/></svg>';

  $iconCode = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m8 9-3 3 3 3m8-6 3 3-3 3"/></svg>';

  $iconUser = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 19.5a7.5 7.5 0 0 1 15 0"/></svg>';

  $iconMoney = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 9.75h19.5v7.5H2.25z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 13.5h.008v.008H6zm12 0h.008v.008H18z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 12a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z"/></svg>';

  $iconMap = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m9 19-6 2V5l6-2 6 2 6-2v16l-6 2-6-2Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 3v16m6-14v16"/></svg>';

  $iconCalendar = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 3v3m8-3v3M4 9h16M5.25 5.25h13.5A1.75 1.75 0 0 1 20.5 7v11.75a1.75 1.75 0 0 1-1.75 1.75H5.25A1.75 1.75 0 0 1 3.5 18.75V7a1.75 1.75 0 0 1 1.75-1.75Z"/></svg>';
@endphp

<div class="space-y-6">

  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
        {{ $proyecto->nombre }}
      </h1>
      <p class="text-sm text-slate-500 mt-1">
        Dashboard del proyecto, avance, fases y tareas.
      </p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.proyectos') }}"
         class="inline-flex items-center justify-center rounded-xl px-4 h-11 text-sm font-semibold bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm">
        Volver
      </a>

      @can('proyectos.editar')
        <a href="{{ route('admin.proyectos.edit', $proyecto->id) }}"
           class="inline-flex items-center justify-center rounded-xl px-4 h-11 text-sm font-semibold bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm">
          Editar
        </a>
      @endcan
    </div>
  </div>

  @if (session('ok'))
    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
      ✅ {{ session('ok') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
      ❌ {{ $errors->first() }}
    </div>
  @endif

  {{-- AVANCE GENERAL --}}
  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
    <div class="flex justify-between items-center gap-3">
      <h2 class="font-bold text-slate-700">Avance del Proyecto</h2>
      <span class="font-extrabold {{ $avanceText }}">
        {{ number_format($avance, 2) }}%
      </span>
    </div>

    <div class="mt-3 w-full bg-slate-200 rounded-full h-4 overflow-hidden shadow-inner">
      <div class="bg-gradient-to-r {{ $avanceColor }} h-4 rounded-full transition-all duration-500 ease-out"
           style="width: {{ $avance }}%;">
      </div>
    </div>
  </div>

  {{-- DASHBOARD COMPACTO --}}
  <div class="rounded-2xl border border-slate-200 bg-slate-50/90 p-3">
    <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-6 gap-3">

      <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-3 flex justify-between items-center">
        <div>
          <div class="text-[10px] uppercase text-slate-500">Tareas</div>
          <div class="text-lg font-extrabold text-slate-900">{{ $stats['tareas_total'] }}</div>
        </div>
        <div class="text-indigo-500">{!! $iconTasks !!}</div>
      </div>

      <div class="rounded-xl border border-amber-200 bg-amber-50 shadow-sm p-3 flex justify-between items-center">
        <div>
          <div class="text-[10px] uppercase text-amber-700">Pendientes</div>
          <div class="text-lg font-extrabold text-amber-800">{{ $stats['tareas_pendientes'] }}</div>
        </div>
        <div class="text-amber-500">{!! $iconPending !!}</div>
      </div>

      <div class="rounded-xl border border-indigo-200 bg-indigo-50 shadow-sm p-3 flex justify-between items-center">
        <div>
          <div class="text-[10px] uppercase text-indigo-700">Proceso</div>
          <div class="text-lg font-extrabold text-indigo-800">{{ $stats['tareas_proceso'] }}</div>
        </div>
        <div class="text-indigo-500">{!! $iconProcess !!}</div>
      </div>

      <div class="rounded-xl border border-emerald-200 bg-emerald-50 shadow-sm p-3 flex justify-between items-center">
        <div>
          <div class="text-[10px] uppercase text-emerald-700">Finalizadas</div>
          <div class="text-lg font-extrabold text-emerald-800">{{ $stats['tareas_finalizadas'] }}</div>
        </div>
        <div class="text-emerald-500">{!! $iconDone !!}</div>
      </div>

      <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-3 flex justify-between items-center">
        <div>
          <div class="text-[10px] uppercase text-slate-500">Pausadas</div>
          <div class="text-lg font-extrabold text-slate-900">{{ $stats['tareas_pausadas'] }}</div>
        </div>
        <div class="text-slate-400">{!! $iconPaused !!}</div>
      </div>

      <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-3 flex justify-between items-center">
        <div>
          <div class="text-[10px] uppercase text-slate-500">Fases</div>
          <div class="text-lg font-extrabold text-slate-900">{{ $stats['fases_total'] }}</div>
        </div>
        <div class="text-violet-500">{!! $iconPhase !!}</div>
      </div>

      <div class="rounded-xl border border-emerald-200 bg-emerald-50 shadow-sm p-3 flex justify-between items-center">
        <div>
          <div class="text-[10px] uppercase text-emerald-700">Fases OK</div>
          <div class="text-lg font-extrabold text-emerald-800">{{ $stats['fases_completadas'] }}</div>
        </div>
        <div class="text-emerald-500">{!! $iconDone !!}</div>
      </div>

      <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-3 flex justify-between items-center">
        <div>
          <div class="text-[10px] uppercase text-slate-500">Estado</div>
          <div class="mt-1">
            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold border {{ $badge }}">
              {{ $estadoLabel }}
            </span>
          </div>
        </div>
        <div class="text-slate-500">{!! $iconState !!}</div>
      </div>

    </div>
  </div>

  {{-- INFO GENERAL --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
      <div class="flex items-center gap-2 text-slate-500">
        {!! $iconCode !!}
        <div class="text-xs font-semibold uppercase tracking-wide">Código</div>
      </div>
      <div class="mt-2 text-lg font-extrabold text-slate-900">
        {{ $proyecto->codigo ?? '—' }}
      </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
      <div class="flex items-center gap-2 text-slate-500">
        {!! $iconUser !!}
        <div class="text-xs font-semibold uppercase tracking-wide">Responsable</div>
      </div>
      <div class="mt-2 text-lg font-extrabold text-slate-900">
        {{ $responsableNombre }}
      </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
      <div class="flex items-center gap-2 text-slate-500">
        {!! $iconMoney !!}
        <div class="text-xs font-semibold uppercase tracking-wide">Presupuesto</div>
      </div>
      <div class="mt-2 text-lg font-extrabold text-slate-900">
        $ {{ number_format((float)($proyecto->presupuesto ?? 0), 2, '.', ',') }}
      </div>
    </div>

  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
      <h2 class="text-base font-extrabold text-slate-900 mb-4">Información general</h2>

      <div class="space-y-4 text-sm">
        <div class="flex items-start gap-3">
          <div class="text-slate-500 mt-0.5">{!! $iconMap !!}</div>
          <div>
            <div class="text-slate-500 font-semibold">Ubicación</div>
            <div class="text-slate-900 font-semibold">{{ $proyecto->ubicacion ?? '—' }}</div>
          </div>
        </div>

        <div class="flex items-start gap-3">
          <div class="text-slate-500 mt-0.5">{!! $iconState !!}</div>
          <div>
            <div class="text-slate-500 font-semibold">Activo</div>
            <div class="text-slate-900 font-semibold">
              {{ (int)($proyecto->activo ?? 1) === 1 ? 'Sí' : 'No' }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
      <h2 class="text-base font-extrabold text-slate-900 mb-4">Fechas</h2>

      <div class="space-y-4 text-sm">
        <div class="flex items-start gap-3">
          <div class="text-slate-500 mt-0.5">{!! $iconCalendar !!}</div>
          <div>
            <div class="text-slate-500 font-semibold">Fecha inicio</div>
            <div class="text-slate-900 font-semibold">{{ $fi }}</div>
          </div>
        </div>

        <div class="flex items-start gap-3">
          <div class="text-slate-500 mt-0.5">{!! $iconCalendar !!}</div>
          <div>
            <div class="text-slate-500 font-semibold">Fecha fin</div>
            <div class="text-slate-900 font-semibold">{{ $ff }}</div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
    <h2 class="text-base font-extrabold text-slate-900 mb-3">Descripción</h2>
    <div class="text-sm leading-6 text-slate-700">
      {{ $proyecto->descripcion ?: 'Sin descripción registrada.' }}
    </div>
  </div>

  {{-- AGREGAR FASE --}}
  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
    <h2 class="text-base font-extrabold text-slate-900 mb-4">Agregar fase</h2>

    <form method="POST" action="{{ route('admin.proyectos.fases.store') }}">
      @csrf
      <input type="hidden" name="proyecto_id" value="{{ $proyecto->id }}">

      <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <input type="text" name="nombre" placeholder="Nombre de la fase"
          class="h-11 rounded-xl border border-slate-300 px-3">

        <input type="number" name="orden" placeholder="Orden"
          class="h-11 rounded-xl border border-slate-300 px-3">

        <input type="number" step="0.01" name="porcentaje" placeholder="%"
          class="h-11 rounded-xl border border-slate-300 px-3">

        <button class="bg-indigo-600 text-white rounded-xl h-11 font-semibold px-4">
          Guardar fase
        </button>
      </div>
    </form>
  </div>

  {{-- FASES --}}
  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
    <h2 class="text-base font-extrabold text-slate-900 mb-4">Fases del Proyecto</h2>

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

      <div class="mb-5 last:mb-0">
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="font-semibold text-slate-900">{{ $fase->nombre }}</div>
            <div class="text-xs text-slate-500">Orden: {{ $fase->orden ?? 0 }}</div>
          </div>
          <div class="text-sm font-extrabold {{ $faseText }}">
            {{ number_format($porcentaje, 2) }}%
          </div>
        </div>

        <div class="mt-2 w-full h-3 rounded-full bg-slate-200 overflow-hidden shadow-inner">
          <div class="h-3 rounded-full bg-gradient-to-r {{ $faseColor }} transition-all duration-500 ease-out"
               style="width: {{ $porcentaje }}%;">
          </div>
        </div>
      </div>
    @empty
      <div class="text-sm text-slate-500">No hay fases registradas.</div>
    @endforelse
  </div>

  {{-- AGREGAR TAREA --}}
  @if(isset($usuarios) && isset($nameField))
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
      <h2 class="text-base font-extrabold text-slate-900 mb-4">Agregar tarea</h2>

      <form method="POST" action="{{ route('admin.proyectos.tareas.store') }}">
        @csrf
        <input type="hidden" name="proyecto_id" value="{{ $proyecto->id }}">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
          <input type="text" name="nombre" placeholder="Nombre de la tarea"
            class="h-11 rounded-xl border border-slate-300 px-3">

          <select name="fase_id" class="h-11 rounded-xl border border-slate-300 px-3">
            <option value="">Sin fase</option>
            @foreach($proyecto->fases as $fase)
              <option value="{{ $fase->id }}">{{ $fase->nombre }}</option>
            @endforeach
          </select>

          <select name="responsable_id" class="h-11 rounded-xl border border-slate-300 px-3">
            <option value="">Sin responsable</option>
            @foreach($usuarios as $u)
              <option value="{{ $u->id }}">{{ $u->{$nameField} ?? ('Usuario #' . $u->id) }}</option>
            @endforeach
          </select>

          <select name="estado" class="h-11 rounded-xl border border-slate-300 px-3">
            <option value="pendiente">Pendiente</option>
            <option value="en_proceso">En proceso</option>
            <option value="finalizada">Finalizada</option>
            <option value="pausada">Pausada</option>
          </select>

          <input type="date" name="fecha_inicio" class="h-11 rounded-xl border border-slate-300 px-3">
          <input type="date" name="fecha_fin" class="h-11 rounded-xl border border-slate-300 px-3">
          <input type="number" step="0.01" name="porcentaje" placeholder="% avance"
            class="h-11 rounded-xl border border-slate-300 px-3">

          <button class="bg-indigo-600 text-white rounded-xl h-11 font-semibold px-4">
            Guardar tarea
          </button>
        </div>

        <div class="mt-3">
          <textarea name="descripcion" rows="3" placeholder="Descripción de la tarea"
            class="w-full rounded-xl border border-slate-300 px-3 py-3"></textarea>
        </div>
      </form>
    </div>
  @endif

  {{-- TAREAS --}}
  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
    <h2 class="text-base font-extrabold text-slate-900 mb-4">Tareas del Proyecto</h2>

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

        $alertColor = $vencida
          ? 'border-red-300 bg-red-50'
          : ($sinResponsable ? 'border-amber-300 bg-amber-50' : 'border-slate-200 bg-white');
      @endphp

      <div class="border {{ $alertColor }} rounded-2xl p-4 mb-4 last:mb-0">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">

          <div class="min-w-0">
            <div class="font-bold text-slate-900">{{ $tarea->nombre }}</div>

            <div class="flex gap-2 mt-2 flex-wrap">
              @if($vencida)
                <span class="text-xs px-2 py-1 rounded bg-red-600 text-white font-bold">
                  Vencida
                </span>
              @endif

              @if($sinResponsable)
                <span class="text-xs px-2 py-1 rounded bg-amber-500 text-white font-bold">
                  Sin responsable
                </span>
              @endif

              @if($estadoT === 'finalizada')
                <span class="text-xs px-2 py-1 rounded bg-emerald-600 text-white font-bold">
                  Completada
                </span>
              @endif
            </div>

            <div class="text-sm text-slate-500 mt-2">
              Fase: {{ $tarea->fase->nombre ?? 'Sin fase' }} · Responsable: {{ $responsableT }}
            </div>

            @if(!empty($tarea->descripcion))
              <div class="mt-3 text-sm text-slate-600">
                {{ $tarea->descripcion }}
              </div>
            @endif

            <div class="mt-3 flex flex-wrap gap-4 text-xs text-slate-500">
              <div>Inicio: {{ $tarea->fecha_inicio?->format('Y-m-d') ?? '—' }}</div>
              <div>Fin: {{ $tarea->fecha_fin?->format('Y-m-d') ?? '—' }}</div>
            </div>
          </div>

          <div class="flex flex-wrap items-center gap-2 lg:justify-end">
            <form method="POST" action="{{ route('admin.proyectos.tareas.update') }}"
                  class="flex flex-wrap items-center gap-2">
              @csrf
              <input type="hidden" name="id" value="{{ $tarea->id }}">

              <input type="number"
                     step="0.01"
                     name="porcentaje"
                     value="{{ $porcentajeT }}"
                     class="w-24 h-10 rounded-lg border border-slate-300 px-2 text-sm">

              <select name="estado"
                      class="h-10 rounded-lg border border-slate-300 px-2 text-sm">
                <option value="pendiente" @selected($estadoT=='pendiente')>Pendiente</option>
                <option value="en_proceso" @selected($estadoT=='en_proceso')>En proceso</option>
                <option value="finalizada" @selected($estadoT=='finalizada')>Finalizada</option>
                <option value="pausada" @selected($estadoT=='pausada')>Pausada</option>
              </select>

              <button class="inline-flex items-center justify-center h-10 px-3 rounded-lg bg-indigo-600 text-white text-sm font-semibold">
                Guardar
              </button>
            </form>

            <a href="{{ route('admin.proyectos.tareas.edit', $tarea->id) }}"
               class="inline-flex items-center justify-center h-10 px-3 rounded-lg bg-white border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50">
              Editar
            </a>
          </div>
        </div>

        <div class="mt-3">
          <div class="flex justify-between text-sm font-semibold text-slate-700">
            <span>Avance</span>
            <span class="{{ $colorChipT }} px-2 py-0.5 rounded text-xs font-bold">
              {{ number_format($porcentajeT, 0) }}%
            </span>
          </div>

          <div class="w-full bg-slate-200 rounded-full h-3 mt-2 overflow-hidden shadow-inner">
            <div class="bg-gradient-to-r {{ $colorBarraT }} h-3 rounded-full transition-all duration-500 ease-out"
                 style="width: {{ $porcentajeT }}%;">
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="text-sm text-slate-500">No hay tareas registradas.</div>
    @endforelse
  </div>

</div>
@endsection