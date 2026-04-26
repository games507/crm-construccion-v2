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

  $estadoBadge = [
    'pendiente' => 'bg-rose-50 text-rose-700 border-rose-200',
    'en_proceso' => 'bg-blue-50 text-blue-700 border-blue-200',
    'finalizada' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    'pausada' => 'bg-amber-50 text-amber-700 border-amber-200',
  ];

  $estadoLabel = [
    'pendiente' => 'Pendiente',
    'en_proceso' => 'En proceso',
    'finalizada' => 'Finalizada',
    'pausada' => 'Pausada',
  ];
@endphp

<div class="space-y-5">

  {{-- HEADER --}}
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-xl font-extrabold text-slate-900">Mis tareas</h1>
      <p class="text-xs text-slate-500 mt-1">Control centralizado de tus tareas asignadas</p>
    </div>

    <a href="{{ route('admin.proyectos') }}"
       class="inline-flex h-9 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3 text-xs font-extrabold text-slate-700 shadow-sm transition hover:bg-slate-50">
      Ver proyectos
    </a>
  </div>

  @if(session('ok'))
    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700">
      {{ session('ok') }}
    </div>
  @endif

  @if($errors->any())
    <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-bold text-rose-700">
      {{ $errors->first() }}
    </div>
  @endif

  {{-- RESUMEN PREMIUM COMPACTO --}}
  <div class="grid grid-cols-2 gap-3 md:grid-cols-5">
    <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
      <div class="text-[10px] font-black uppercase tracking-wide text-slate-500">Total</div>
      <div class="mt-1 text-xl font-black text-slate-900">{{ $stats['total'] }}</div>
    </div>

    <div class="rounded-2xl border border-rose-200 bg-rose-50 p-3 shadow-sm">
      <div class="text-[10px] font-black uppercase tracking-wide text-rose-700">Pendientes</div>
      <div class="mt-1 text-xl font-black text-rose-800">{{ $stats['pendientes'] }}</div>
    </div>

    <div class="rounded-2xl border border-blue-200 bg-blue-50 p-3 shadow-sm">
      <div class="text-[10px] font-black uppercase tracking-wide text-blue-700">Proceso</div>
      <div class="mt-1 text-xl font-black text-blue-800">{{ $stats['en_proceso'] }}</div>
    </div>

    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-3 shadow-sm">
      <div class="text-[10px] font-black uppercase tracking-wide text-amber-700">Vencidas</div>
      <div class="mt-1 text-xl font-black text-amber-800">{{ $stats['vencidas'] }}</div>
    </div>

    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-3 shadow-sm">
      <div class="text-[10px] font-black uppercase tracking-wide text-emerald-700">Finalizadas</div>
      <div class="mt-1 text-xl font-black text-emerald-800">{{ $stats['finalizadas'] }}</div>
    </div>
  </div>

  {{-- FILTROS PREMIUM --}}
  <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
    <form method="GET" action="{{ route('admin.proyectos.mis_tareas') }}"
          class="flex flex-wrap items-center gap-2">

      <input type="text"
             name="q"
             value="{{ $q }}"
             placeholder="Buscar tarea, proyecto o fase..."
             class="h-9 min-w-[220px] flex-1 rounded-xl border border-slate-300 px-3 text-xs outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">

      <select name="proyecto_id"
              class="h-9 min-w-[150px] rounded-xl border border-slate-300 bg-white px-2 text-xs outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
        <option value="">Proyecto</option>
        @foreach($proyectos as $p)
          <option value="{{ $p->id }}" @selected((string)$proyectoId === (string)$p->id)>
            {{ $p->nombre }}
          </option>
        @endforeach
      </select>

      <select name="estado"
              class="h-9 min-w-[130px] rounded-xl border border-slate-300 bg-white px-2 text-xs outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
        @foreach($estadoOptions as $value => $label)
          <option value="{{ $value }}" @selected($estado === $value)>{{ $label }}</option>
        @endforeach
      </select>

      <label class="inline-flex h-9 items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs font-bold text-slate-700">
        <input type="checkbox"
               name="vencidas"
               value="1"
               @checked($vencidas)
               class="rounded border-slate-300">
        Vencidas
      </label>

      <button type="submit"
              class="inline-flex h-9 items-center justify-center rounded-xl bg-indigo-600 px-4 text-xs font-extrabold text-white shadow-sm transition hover:bg-indigo-700">
        Filtrar
      </button>

      <a href="{{ route('admin.proyectos.mis_tareas') }}"
         class="inline-flex h-9 items-center justify-center rounded-xl bg-slate-100 px-3 text-xs font-bold text-slate-700 transition hover:bg-slate-200">
        Limpiar
      </a>
    </form>
  </div>

  {{-- TABLA PREMIUM SIN SCROLL --}}
  <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full table-fixed text-xs">
      <thead class="bg-slate-50 text-slate-500">
        <tr>
          <th class="w-[27%] px-3 py-3 text-left font-black uppercase tracking-wide">Tarea</th>
          <th class="w-[19%] px-3 py-3 text-left font-black uppercase tracking-wide">Proyecto / fase</th>
          <th class="w-[12%] px-3 py-3 text-center font-black uppercase tracking-wide">Estado</th>
          <th class="w-[10%] px-3 py-3 text-center font-black uppercase tracking-wide">Avance</th>
          <th class="w-[12%] px-3 py-3 text-center font-black uppercase tracking-wide">Vence</th>
          <th class="w-[15%] px-3 py-3 text-center font-black uppercase tracking-wide">Actualizar</th>
          <th class="w-[5%] px-3 py-3 text-center font-black uppercase tracking-wide"></th>
        </tr>
      </thead>

      <tbody class="divide-y divide-slate-100">
        @forelse($tareas as $t)
          @php
            $vencida = $t->esta_vencida ?? false;
            $badgeClass = $estadoBadge[$t->estado] ?? 'bg-slate-50 text-slate-700 border-slate-200';
            $label = $estadoLabel[$t->estado] ?? ucfirst((string)$t->estado);
          @endphp

          <tr class="{{ $vencida ? 'bg-rose-50/70' : 'hover:bg-slate-50' }} transition">
            <td class="px-3 py-3 align-middle">
              <div class="truncate font-black text-slate-900" title="{{ $t->nombre }}">
                {{ $t->nombre }}
              </div>
              @if($t->descripcion)
                <div class="mt-0.5 truncate text-[11px] text-slate-400" title="{{ $t->descripcion }}">
                  {{ $t->descripcion }}
                </div>
              @endif
            </td>

            <td class="px-3 py-3 align-middle">
              <div class="truncate font-bold text-slate-800" title="{{ $t->proyecto->nombre ?? '—' }}">
                {{ $t->proyecto->nombre ?? '—' }}
              </div>
              <div class="truncate text-[11px] text-slate-400" title="{{ $t->fase->nombre ?? 'Sin fase' }}">
                {{ $t->fase->nombre ?? 'Sin fase' }}
              </div>
            </td>

            <td class="px-3 py-3 text-center align-middle">
              <span class="inline-flex items-center justify-center rounded-full border px-2 py-1 text-[10px] font-black {{ $badgeClass }}">
                {{ $label }}
              </span>
            </td>

            <td class="px-3 py-3 text-center align-middle">
              <div class="font-black text-slate-900">{{ number_format((float)$t->porcentaje, 0) }}%</div>
              <div class="mx-auto mt-1 h-1.5 w-16 overflow-hidden rounded-full bg-slate-100">
                <div class="h-1.5 rounded-full bg-indigo-600" style="width: {{ min((float)$t->porcentaje, 100) }}%"></div>
              </div>
            </td>

            <td class="px-3 py-3 text-center align-middle">
              <div class="font-bold {{ $vencida ? 'text-rose-700' : 'text-slate-700' }}">
                {{ $t->fecha_fin?->format('Y-m-d') ?? '—' }}
              </div>
              @if($vencida)
                <span class="mt-1 inline-flex rounded-full bg-rose-600 px-2 py-0.5 text-[10px] font-black text-white">
                  Vencida
                </span>
              @endif
            </td>

            <td class="px-3 py-3 align-middle">
              <form method="POST"
                    action="{{ route('admin.proyectos.mis_tareas.update', $t->id) }}"
                    class="flex items-center justify-center gap-1">
                @csrf
                @method('PUT')

                <select name="estado"
                        class="h-8 w-24 rounded-xl border border-slate-300 bg-white px-2 text-[11px] outline-none">
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
                       class="h-8 w-12 rounded-xl border border-slate-300 px-1 text-center text-[11px] outline-none"
                       placeholder="%">

                <button type="submit"
                        title="Guardar avance"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-sm transition hover:bg-emerald-700">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                    <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </button>
              </form>
            </td>

            <td class="px-3 py-3 text-center align-middle">
              <a href="{{ route('admin.proyectos.show', $t->proyecto_id) }}"
                 title="Ver proyecto"
                 class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-white text-slate-500 ring-1 ring-slate-200 transition hover:bg-indigo-50 hover:text-indigo-700 hover:ring-indigo-200">

                <svg
                  width="16"
                  height="16"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>

              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="px-4 py-10 text-center text-sm font-semibold text-slate-500">
              No tienes tareas asignadas.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

</div>

@endsection