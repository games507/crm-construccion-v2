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

  $iconTasks = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 6h11M9 12h11M9 18h11M4.5 6h.01M4.5 12h.01M4.5 18h.01"/></svg>';
  $iconPending = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v6l4 2"/></svg>';
  $iconProcess = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v3m0 12v3m9-9h-3M6 12H3"/></svg>';
  $iconDone = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m5 13 4 4L19 7"/></svg>';
  $iconPaused = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 6v12M14 6v12"/></svg>';
  $iconPhase = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7.5A2.5 2.5 0 0 1 5.5 5H10l2 2h6.5A2.5 2.5 0 0 1 21 9.5v7"/></svg>';
  $iconState = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="9"/></svg>';
  $iconCode = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m8 9-3 3 3 3m8-6 3 3-3 3"/></svg>';
  $iconUser = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 19.5a7.5 7.5 0 0 1 15 0"/></svg>';
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
        Panel general del proyecto
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

  <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

    <div class="xl:col-span-2 rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
      <div class="flex justify-between items-center gap-3">
        <h2 class="font-bold text-slate-700">Avance del Proyecto</h2>
        <span class="font-extrabold {{ $avanceText }}">
          {{ number_format($avance, 2, '.', ',') }}%
        </span>
      </div>

      <div class="mt-3 w-full bg-slate-200 rounded-full h-4 overflow-hidden shadow-inner">
        <div class="bg-gradient-to-r {{ $avanceColor }} h-4 rounded-full transition-all duration-500 ease-out"
             style="width: {{ $avance }}%;">
        </div>
      </div>

      <div class="mt-5 grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
          <div class="text-[10px] uppercase text-slate-500">Tareas</div>
          <div class="text-lg font-extrabold text-slate-900 mt-1">{{ $stats['tareas_total'] }}</div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
          <div class="text-[10px] uppercase text-slate-500">Fases</div>
          <div class="text-lg font-extrabold text-slate-900 mt-1">{{ $stats['fases_total'] }}</div>
        </div>

        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3">
          <div class="text-[10px] uppercase text-emerald-700">Fases OK</div>
          <div class="text-lg font-extrabold text-emerald-800 mt-1">{{ $stats['fases_completadas'] }}</div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-3">
          <div class="text-[10px] uppercase text-slate-500">Estado</div>
          <div class="mt-2">
            <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-bold border {{ $badge }}">
              {{ $estadoLabel }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
      <h2 class="text-base font-extrabold text-slate-900 mb-4">Información general</h2>

      <div class="space-y-4 text-sm">
        <div class="flex items-start gap-3">
          <div class="text-slate-500 mt-0.5">{!! $iconCode !!}</div>
          <div>
            <div class="text-slate-500 font-semibold">Código</div>
            <div class="text-slate-900 font-semibold">{{ $proyecto->codigo ?? '—' }}</div>
          </div>
        </div>

        <div class="flex items-start gap-3">
          <div class="text-slate-500 mt-0.5">{!! $iconUser !!}</div>
          <div>
            <div class="text-slate-500 font-semibold">Responsable</div>
            <div class="text-slate-900 font-semibold">{{ $responsableNombre }}</div>
          </div>
        </div>

        <div class="flex items-start gap-3">
          <div class="text-slate-500 mt-0.5">{!! $iconMap !!}</div>
          <div>
            <div class="text-slate-500 font-semibold">Ubicación</div>
            <div class="text-slate-900 font-semibold">{{ $proyecto->ubicacion ?? '—' }}</div>
          </div>
        </div>

        <div class="flex items-start gap-3">
          <div class="text-slate-500 mt-0.5">{!! $iconCalendar !!}</div>
          <div>
            <div class="text-slate-500 font-semibold">Fechas</div>
            <div class="text-slate-900 font-semibold">{{ $fi }} / {{ $ff }}</div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

    <div class="xl:col-span-2 rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
      <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
          <h2 class="text-base font-extrabold text-slate-900">Control de Costos</h2>
          <p class="text-sm text-slate-500 mt-1">Resumen financiero del proyecto</p>
        </div>
        <div class="text-right">
          <div class="text-xs uppercase tracking-wide text-slate-500 font-semibold">% consumido</div>
          <div class="text-lg font-extrabold {{ $consumoText }}">
            {{ number_format($porcentajeConsumido, 2, '.', ',') }}%
          </div>
        </div>
      </div>

      <div class="mt-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Presupuesto</div>
          <div class="mt-2 text-xl font-extrabold text-slate-900">
            $ {{ number_format($presupuesto, 2, '.', ',') }}
          </div>
        </div>

        <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4">
          <div class="text-xs uppercase tracking-wide text-sky-700 font-semibold">Ejecutado</div>
          <div class="mt-2 text-xl font-extrabold text-sky-800">
            $ {{ number_format($ejecutado, 2, '.', ',') }}
          </div>
        </div>

        <div class="rounded-2xl border {{ $saldoDisponible < 0 ? 'border-rose-200 bg-rose-50' : 'border-emerald-200 bg-emerald-50' }} p-4">
          <div class="text-xs uppercase tracking-wide {{ $saldoDisponible < 0 ? 'text-rose-700' : 'text-emerald-700' }} font-semibold">
            Disponible
          </div>
          <div class="mt-2 text-xl font-extrabold {{ $saldoDisponible < 0 ? 'text-rose-800' : 'text-emerald-800' }}">
            $ {{ number_format($saldoDisponible, 2, '.', ',') }}
          </div>
        </div>

        <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-4">
          <div class="text-xs uppercase tracking-wide text-indigo-700 font-semibold">Registros</div>
          <div class="mt-2 text-xl font-extrabold text-indigo-800">
            {{ $costosLista->count() }}
          </div>
        </div>
      </div>

      <div class="mt-4">
        <div class="flex justify-between items-center gap-3 mb-2">
          <span class="text-sm font-semibold text-slate-600">Consumo del presupuesto</span>
          <span class="text-sm font-extrabold {{ $consumoText }}">
            {{ number_format($porcentajeConsumido, 2, '.', ',') }}%
          </span>
        </div>

        <div class="w-full bg-slate-200 rounded-full h-4 overflow-hidden shadow-inner">
          <div class="bg-gradient-to-r {{ $consumoColor }} h-4 rounded-full transition-all duration-500 ease-out"
               style="width: {{ min($porcentajeConsumido, 100) }}%;">
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

  </div>

  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
      <div>
        <h2 class="text-base font-extrabold text-slate-900">Costos del Proyecto</h2>
        <p class="text-sm text-slate-500 mt-1">Listado y registro de costos</p>
      </div>

      <details class="group">
        <summary class="list-none cursor-pointer inline-flex items-center justify-center rounded-xl px-4 h-11 text-sm font-semibold bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm">
          + Agregar costo
        </summary>

        <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
          <form method="POST" action="{{ route('admin.proyectos.costos.store') }}">
            @csrf
            <input type="hidden" name="proyecto_id" value="{{ $proyecto->id }}">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
              <select name="tipo" class="h-11 rounded-xl border border-slate-300 px-3">
                <option value="">Tipo de costo</option>
                @foreach($tiposCosto as $key => $label)
                  <option value="{{ $key }}" @selected(old('tipo') == $key)>{{ $label }}</option>
                @endforeach
              </select>

              <input type="text" name="categoria" value="{{ old('categoria') }}" placeholder="Categoría"
                class="h-11 rounded-xl border border-slate-300 px-3">

              <input type="text" name="proveedor" value="{{ old('proveedor') }}" placeholder="Proveedor / acreedor"
                class="h-11 rounded-xl border border-slate-300 px-3">

              <input type="number" step="0.01" name="monto" value="{{ old('monto') }}" placeholder="Monto"
                class="h-11 rounded-xl border border-slate-300 px-3">

              <input type="date" name="fecha" value="{{ old('fecha') }}" class="h-11 rounded-xl border border-slate-300 px-3">

              <select name="estado_pago" class="h-11 rounded-xl border border-slate-300 px-3">
                <option value="pendiente" @selected(old('estado_pago') == 'pendiente')>Pendiente</option>
                <option value="parcial" @selected(old('estado_pago') == 'parcial')>Parcial</option>
                <option value="pagado" @selected(old('estado_pago') == 'pagado')>Pagado</option>
              </select>

              <label class="h-11 rounded-xl border border-slate-300 px-3 flex items-center gap-2 text-sm text-slate-700 bg-white">
                <input type="checkbox" name="requiere_pago" value="1" class="rounded border-slate-300" @checked(old('requiere_pago'))>
                Requiere pago
              </label>

              <button class="bg-indigo-600 text-white rounded-xl h-11 font-semibold px-4">
                Guardar costo
              </button>
            </div>

            <div class="mt-3">
              <textarea name="descripcion" rows="3" placeholder="Descripción del costo"
                class="w-full rounded-xl border border-slate-300 px-3 py-3">{{ old('descripcion') }}</textarea>
            </div>
          </form>
        </div>
      </details>
    </div>

    @forelse($costosLista as $costo)
      @php
        $estadoPago = (string) ($costo->estado_pago ?? 'pendiente');

        $badgePago = match($estadoPago) {
          'pagado'    => 'bg-emerald-50 text-emerald-700 border-emerald-200',
          'parcial'   => 'bg-amber-50 text-amber-700 border-amber-200',
          default     => 'bg-rose-50 text-rose-700 border-rose-200',
        };

        $tipoLabel = $tiposCosto[$costo->tipo] ?? ucfirst(str_replace('_', ' ', (string) $costo->tipo));
      @endphp

      <div class="border border-slate-200 rounded-2xl p-4 mb-4 last:mb-0">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
          <div class="min-w-0">
            <div class="font-bold text-slate-900">{{ $tipoLabel }}</div>

            <div class="mt-2 flex flex-wrap gap-2">
              @if(!empty($costo->categoria))
                <span class="inline-flex items-center rounded-full bg-slate-100 text-slate-700 px-3 py-1 text-xs font-bold border border-slate-200">
                  {{ $costo->categoria }}
                </span>
              @endif

              <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold border {{ $badgePago }}">
                {{ ucfirst($estadoPago) }}
              </span>

              @if((bool) ($costo->requiere_pago ?? false))
                <span class="inline-flex items-center rounded-full bg-indigo-50 text-indigo-700 px-3 py-1 text-xs font-bold border border-indigo-200">
                  Requiere pago
                </span>
              @endif
            </div>

            <div class="text-sm text-slate-500 mt-2">
              Proveedor: {{ $costo->proveedor ?: '—' }} · Fecha: {{ $costo->fecha?->format('Y-m-d') ?? '—' }}
            </div>

            @if(!empty($costo->descripcion))
              <div class="mt-3 text-sm text-slate-600">
                {{ $costo->descripcion }}
              </div>
            @endif
          </div>

          <div class="text-right">
            <div class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Monto</div>
            <div class="text-lg font-extrabold text-slate-900">
              $ {{ number_format((float) $costo->monto, 2, '.', ',') }}
            </div>

            <div class="mt-3 flex items-center justify-end gap-2">
              <a href="{{ route('admin.proyectos.costos.edit', $costo->id) }}"
                 title="Editar costo"
                 class="inline-flex items-center justify-center h-9 w-9 rounded-lg border border-slate-900/10 bg-white text-slate-600 hover:text-indigo-700 hover:border-indigo-200 hover:bg-indigo-50 transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.862 4.487a2.25 2.25 0 1 1 3.182 3.182L7.5 20.213 3 21l.787-4.5L16.862 4.487Z"/>
                </svg>
              </a>

              <form method="POST"
                    action="{{ route('admin.proyectos.costos.destroy', $costo->id) }}"
                    onsubmit="return confirm('¿Seguro que deseas eliminar este costo?');"
                    class="inline-flex">
                @csrf
                @method('DELETE')

                <button type="submit"
                        title="Eliminar costo"
                        class="inline-flex items-center justify-center h-9 w-9 rounded-lg border border-slate-900/10 bg-white text-slate-600 hover:text-rose-700 hover:border-rose-200 hover:bg-rose-50 transition shadow-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                       fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M6 7.5h12m-10.5 0V6a1.5 1.5 0 0 1 1.5-1.5h6A1.5 1.5 0 0 1 16.5 6v1.5m-9 0v10.125A2.625 2.625 0 0 0 10.125 20.25h3.75A2.625 2.625 0 0 0 16.5 17.625V7.5M10 10.5v6m4-6v6"/>
                  </svg>
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
       @empty
      <div class="text-sm text-slate-500">No hay costos registrados aún.</div>
    @endforelse
  </div>

  {{-- CUENTAS POR PAGAR --}}
  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
      <div>
        <h2 class="text-base font-extrabold text-slate-900">Cuentas por pagar</h2>
        <p class="text-sm text-slate-500 mt-1">Control de saldos pendientes del proyecto</p>
      </div>

      <details class="group">
        <summary class="list-none cursor-pointer inline-flex items-center justify-center rounded-xl px-4 h-11 text-sm font-semibold bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm">
          + Agregar cuenta
        </summary>

        <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
          <form method="POST" action="{{ route('admin.cuentas.store') }}">
            @csrf
            <input type="hidden" name="proyecto_id" value="{{ $proyecto->id }}">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
              <input type="text" name="proveedor" placeholder="Proveedor / acreedor"
                class="h-11 rounded-xl border border-slate-300 px-3" required>

              <input type="number" step="0.01" name="monto_total" placeholder="Monto total"
                class="h-11 rounded-xl border border-slate-300 px-3" required>

              <input type="date" name="fecha"
                class="h-11 rounded-xl border border-slate-300 px-3">

              <input type="date" name="fecha_vencimiento"
                class="h-11 rounded-xl border border-slate-300 px-3">

              <textarea name="descripcion" rows="3" placeholder="Descripción"
                class="lg:col-span-3 w-full rounded-xl border border-slate-300 px-3 py-3"></textarea>

              <button class="bg-indigo-600 text-white rounded-xl h-11 font-semibold px-4">
                Guardar cuenta
              </button>
            </div>
          </form>
        </div>
      </details>
    </div>

    @forelse(($proyecto->cuentasPorPagar ?? collect()) as $c)
      @php
        $estadoCxP = (string)($c->estado ?? 'pendiente');

        $badgeCxP = match($estadoCxP) {
          'pagado'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
          'parcial' => 'bg-amber-50 text-amber-700 border-amber-200',
          default   => 'bg-rose-50 text-rose-700 border-rose-200',
        };
      @endphp

      <div class="border border-slate-200 rounded-2xl p-4 mb-4 last:mb-0">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
          <div class="min-w-0">
            <div class="font-bold text-slate-900">{{ $c->proveedor }}</div>

            <div class="mt-2 flex flex-wrap gap-2">
              <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold border {{ $badgeCxP }}">
                {{ ucfirst($estadoCxP) }}
              </span>
            </div>

            <div class="text-sm text-slate-500 mt-2">
              Registro: {{ $c->fecha?->format('Y-m-d') ?? '—' }} · Vence: {{ $c->fecha_vencimiento?->format('Y-m-d') ?? '—' }}
            </div>

            @if(!empty($c->descripcion))
              <div class="mt-3 text-sm text-slate-600">
                {{ $c->descripcion }}
              </div>
            @endif
          </div>

          <div class="text-right min-w-[180px]">
            <div class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Saldo</div>
            <div class="text-lg font-extrabold text-rose-700">
              $ {{ number_format((float)$c->saldo, 2, '.', ',') }}
            </div>

            <div class="mt-2 text-xs text-slate-500">
              Total: $ {{ number_format((float)$c->monto_total, 2, '.', ',') }}
            </div>
            <div class="text-xs text-slate-500">
              Pagado: $ {{ number_format((float)$c->monto_pagado, 2, '.', ',') }}
            </div>
          </div>
        </div>

        <form method="POST" action="{{ route('admin.cuentas.pagar', $c->id) }}" class="mt-4 flex flex-col sm:flex-row gap-2">
          @csrf
          <input type="number" step="0.01" name="monto" placeholder="Monto pago"
                 class="h-10 rounded-xl border border-slate-300 px-3">
          <button class="inline-flex items-center justify-center h-10 px-4 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 shadow-sm">
            Registrar pago
          </button>
        </form>
      </div>
    @empty
      <div class="text-sm text-slate-500">No hay cuentas por pagar.</div>
    @endforelse
  </div>

  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
      <div>
        <h2 class="text-base font-extrabold text-slate-900">Fases del Proyecto</h2>
        <p class="text-sm text-slate-500 mt-1">Control por etapas</p>
      </div>

      <details class="group">
        <summary class="list-none cursor-pointer inline-flex items-center justify-center rounded-xl px-4 h-11 text-sm font-semibold bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm">
          + Agregar fase
        </summary>

        <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
          <form method="POST" action="{{ route('admin.proyectos.fases.store') }}">
            @csrf
            <input type="hidden" name="proyecto_id" value="{{ $proyecto->id }}">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
              <input type="text" name="nombre" placeholder="Nombre de la fase"
                class="h-11 rounded-xl border border-slate-300 px-3">

              <input type="text" inputmode="numeric" name="orden" placeholder="Orden"
                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                class="h-11 rounded-xl border border-slate-300 px-3">

              <input type="text" inputmode="decimal" name="porcentaje" placeholder="%"
                oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                class="h-11 rounded-xl border border-slate-300 px-3">

              <button class="bg-indigo-600 text-white rounded-xl h-11 font-semibold px-4">
                Guardar fase
              </button>
            </div>
          </form>
        </div>
      </details>
    </div>

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
          <div class="flex items-center gap-2">
            <div class="text-sm font-extrabold {{ $faseText }} mr-2">
              {{ number_format($porcentaje, 2, '.', ',') }}%
            </div>

            <details class="relative group">
              <summary title="Editar fase"
                class="list-none cursor-pointer inline-flex items-center justify-center h-9 w-9 rounded-xl border border-slate-200 bg-white text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                  <path d="M4 20h4l10-10-4-4L4 16v4z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                </svg>
              </summary>

              <div class="absolute right-0 z-30 mt-2 w-[320px] rounded-2xl border border-slate-200 bg-white p-4 shadow-xl">
                <form method="POST" action="{{ route('admin.proyectos.fases.update', $fase->id) }}" class="space-y-3">
                  @csrf
                  @method('PUT')

                  <div>
                    <label class="text-xs font-bold text-slate-500">Nombre</label>
                    <input type="text" name="nombre" value="{{ $fase->nombre }}"
                      class="mt-1 h-10 w-full rounded-xl border border-slate-300 px-3 text-sm" required>
                  </div>

                  <div class="grid grid-cols-2 gap-3">
                    <div>
                      <label class="text-xs font-bold text-slate-500">Orden</label>
                      <input type="text" inputmode="numeric" name="orden" value="{{ $fase->orden ?? 0 }}"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        class="mt-1 h-10 w-full rounded-xl border border-slate-300 px-3 text-sm">
                    </div>

                    <div>
                      <label class="text-xs font-bold text-slate-500">%</label>
                      <input type="text" inputmode="decimal" name="porcentaje" value="{{ number_format($porcentaje, 2, '.', '') }}"
                        oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                        class="mt-1 h-10 w-full rounded-xl border border-slate-300 px-3 text-sm">
                    </div>
                  </div>

                  <div class="flex justify-end gap-2 pt-1">
                    <button type="submit"
                      class="inline-flex items-center justify-center h-10 px-4 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700">
                      Guardar
                    </button>
                  </div>
                </form>
              </div>
            </details>

            <form method="POST" action="{{ route('admin.proyectos.fases.destroy', $fase->id) }}"
              onsubmit="return confirm('¿Seguro que deseas eliminar esta fase? Las tareas quedarán sin fase.');">
              @csrf
              @method('DELETE')
              <button type="submit" title="Eliminar fase"
                class="inline-flex items-center justify-center h-9 w-9 rounded-xl border border-slate-200 bg-white text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                  <path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
              </button>
            </form>
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

  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
      <div>
        <h2 class="text-base font-extrabold text-slate-900">Tareas del Proyecto</h2>
        <p class="text-sm text-slate-500 mt-1">Seguimiento operativo</p>
      </div>

      @if(isset($usuarios) && isset($nameField))
      <details class="group">
        <summary class="list-none cursor-pointer inline-flex items-center justify-center rounded-xl px-4 h-11 text-sm font-semibold bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm">
          + Agregar tarea
        </summary>

        <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
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
              <input type="text" inputmode="decimal" name="porcentaje" placeholder="% avance"
                oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
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
      </details>
      @endif
    </div>

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

              <input type="text"
                     inputmode="decimal"
                     name="porcentaje"
                     value="{{ number_format($porcentajeT, 2, '.', '') }}"
                     oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
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
               title="Editar tarea"
               class="inline-flex items-center justify-center h-10 w-10 rounded-xl bg-white border border-slate-300 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M4 20h4l10-10-4-4L4 16v4z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
              </svg>
            </a>

            <form method="POST" action="{{ route('admin.proyectos.tareas.destroy', $tarea->id) }}"
              onsubmit="return confirm('¿Seguro que deseas eliminar esta tarea?');">
              @csrf
              @method('DELETE')
              <button type="submit" title="Eliminar tarea"
                class="inline-flex items-center justify-center h-10 w-10 rounded-xl bg-white border border-slate-300 text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                  <path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
              </button>
            </form>
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