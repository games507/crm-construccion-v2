@extends('layouts.base')
@section('title','Mis tareas')

@section('content')
@php
  $estadoOptions = [
    '' => 'Todos los estados',
    'pendiente' => 'Pendiente',
    'en_proceso' => 'En proceso',
    'finalizada' => 'Finalizada',
    'pausada' => 'Pausada',
  ];
@endphp

<div class="space-y-6">

  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">Mis tareas</h1>
      <p class="text-sm text-slate-500 mt-1">
        Todas tus tareas asignadas centralizadas por proyecto, fase, estado y vencimiento.
      </p>
    </div>

    <a href="{{ route('admin.proyectos') }}"
       class="inline-flex items-center justify-center h-10 px-4 rounded-xl bg-white border border-slate-200 text-sm font-bold text-slate-700 hover:bg-slate-50 shadow-sm">
      Ver proyectos
    </a>
  </div>

  @if(session('ok'))
    <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-2 font-semibold">
      {{ session('ok') }}
    </div>
  @endif

  @if($errors->any())
    <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-700 px-4 py-2 font-semibold">
      {{ $errors->first() }}
    </div>
  @endif

  {{-- RESUMEN --}}
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
      <div class="text-xs uppercase tracking-wide text-slate-500 font-black">Total</div>
      <div class="mt-2 text-2xl font-black text-slate-900">{{ $stats['total'] }}</div>
    </div>

    <div class="rounded-3xl border border-rose-200 bg-rose-50 p-5 shadow-sm">
      <div class="text-xs uppercase tracking-wide text-rose-700 font-black">Pendientes</div>
      <div class="mt-2 text-2xl font-black text-rose-800">{{ $stats['pendientes'] }}</div>
    </div>

    <div class="rounded-3xl border border-blue-200 bg-blue-50 p-5 shadow-sm">
      <div class="text-xs uppercase tracking-wide text-blue-700 font-black">En proceso</div>
      <div class="mt-2 text-2xl font-black text-blue-800">{{ $stats['en_proceso'] }}</div>
    </div>

    <div class="rounded-3xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
      <div class="text-xs uppercase tracking-wide text-amber-700 font-black">Vencidas</div>
      <div class="mt-2 text-2xl font-black text-amber-800">{{ $stats['vencidas'] }}</div>
    </div>

    <div class="rounded-3xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
      <div class="text-xs uppercase tracking-wide text-emerald-700 font-black">Finalizadas</div>
      <div class="mt-2 text-2xl font-black text-emerald-800">{{ $stats['finalizadas'] }}</div>
    </div>
  </div>

  {{-- FILTROS --}}
  <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
    <form method="GET" action="{{ route('admin.proyectos.mis_tareas') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
      <input type="text" name="q" value="{{ $q }}"
             placeholder="Buscar tarea, proyecto o fase"
             class="h-11 rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">

      <select name="proyecto_id" class="h-11 rounded-xl border border-slate-300 px-3 text-sm bg-white">
        <option value="">Todos los proyectos</option>
        @foreach($proyectos as $p)
          <option value="{{ $p->id }}" @selected((string)$proyectoId === (string)$p->id)>
            {{ $p->nombre }}
          </option>
        @endforeach
      </select>

      <select name="estado" class="h-11 rounded-xl border border-slate-300 px-3 text-sm bg-white">
        @foreach($estadoOptions as $value => $label)
          <option value="{{ $value }}" @selected($estado === $value)>{{ $label }}</option>
        @endforeach
      </select>

      <label class="h-11 rounded-xl border border-slate-300 px-3 text-sm bg-white flex items-center gap-2 font-bold text-slate-700">
        <input type="checkbox" name="vencidas" value="1" @checked($vencidas) class="rounded border-slate-300">
        Solo vencidas
      </label>

      <div class="flex gap-2">
        <button type="submit"
                class="flex-1 h-11 rounded-xl bg-indigo-600 text-white text-sm font-extrabold hover:bg-indigo-700 shadow-sm">
          Filtrar
        </button>
        <a href="{{ route('admin.proyectos.mis_tareas') }}"
           class="h-11 px-3 rounded-xl bg-slate-100 text-slate-700 text-sm font-bold hover:bg-slate-200 flex items-center">
          Limpiar
        </a>
      </div>
    </form>
  </div>

  {{-- LISTADO --}}
  <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="px-4 py-3 text-left">Tarea</th>
            <th class="px-4 py-3 text-left">Proyecto / fase</th>
            <th class="px-4 py-3 text-center">Estado</th>
            <th class="px-4 py-3 text-center">Avance</th>
            <th class="px-4 py-3 text-center">Vence</th>
            <th class="px-4 py-3 text-center">Actualizar</th>
            <th class="px-4 py-3 text-center">Acción</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
          @forelse($tareas as $tarea)
            @php
              $vencida = $tarea->esta_vencida;
              $badge = match($tarea->estado) {
                'finalizada' => 'bg-emerald-100 text-emerald-700',
                'en_proceso' => 'bg-blue-100 text-blue-700',
                'pausada' => 'bg-amber-100 text-amber-700',
                default => 'bg-rose-100 text-rose-700',
              };
            @endphp

            <tr class="{{ $vencida ? 'bg-rose-50/80 border-l-4 border-rose-500' : 'hover:bg-slate-50' }} transition">
              <td class="px-4 py-4">
                <div class="font-black text-slate-900">{{ $tarea->nombre }}</div>
                @if($tarea->descripcion)
                  <div class="text-xs text-slate-500 mt-1 max-w-[280px] truncate">{{ $tarea->descripcion }}</div>
                @endif
              </td>

              <td class="px-4 py-4">
                <div class="font-bold text-slate-800">{{ $tarea->proyecto->nombre ?? '—' }}</div>
                <div class="text-xs text-slate-500">{{ $tarea->fase->nombre ?? 'Sin fase' }}</div>
              </td>

              <td class="px-4 py-4 text-center">
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-black {{ $badge }}">
                  {{ $tarea->estado_label }}
                </span>
              </td>

              <td class="px-4 py-4 text-center">
                <div class="font-black text-slate-900">{{ number_format((float)$tarea->porcentaje, 0) }}%</div>
                <div class="mt-1 h-2 rounded-full bg-slate-100 overflow-hidden w-28 mx-auto">
                  <div class="h-2 rounded-full bg-indigo-600" style="width: {{ min((float)$tarea->porcentaje, 100) }}%"></div>
                </div>
              </td>

              <td class="px-4 py-4 text-center">
                <div class="font-bold {{ $vencida ? 'text-rose-700' : 'text-slate-700' }}">
                  {{ $tarea->fecha_fin?->format('Y-m-d') ?? '—' }}
                </div>
                @if($vencida)
                  <div class="mt-1 inline-flex px-2 py-0.5 rounded-full bg-rose-600 text-white text-[11px] font-bold">
                    Vencida
                  </div>
                @endif
              </td>

              <td class="px-4 py-4">
                <form method="POST" action="{{ route('admin.proyectos.mis_tareas.update', $tarea->id) }}" class="flex items-center justify-center gap-2">
                  @csrf
                  @method('PUT')

                  <select name="estado" class="h-9 rounded-xl border border-slate-300 px-2 text-xs bg-white">
                    <option value="pendiente" @selected($tarea->estado === 'pendiente')>Pendiente</option>
                    <option value="en_proceso" @selected($tarea->estado === 'en_proceso')>En proceso</option>
                    <option value="finalizada" @selected($tarea->estado === 'finalizada')>Finalizada</option>
                    <option value="pausada" @selected($tarea->estado === 'pausada')>Pausada</option>
                  </select>

                  <input type="text" name="porcentaje" inputmode="decimal"
                         value="{{ old('porcentaje', (float)$tarea->porcentaje) }}"
                         oninput="this.value=this.value.replace(/[^0-9.]/g,'')"
                         class="h-9 w-20 rounded-xl border border-slate-300 px-2 text-xs text-center"
                         placeholder="%">

                  <button class="h-9 px-3 rounded-xl bg-emerald-600 text-white text-xs font-black hover:bg-emerald-700">
                    Guardar
                  </button>
                </form>
              </td>

              <td class="px-4 py-4 text-center">
                <a href="{{ route('admin.proyectos.show', $tarea->proyecto_id) }}"
                   class="inline-flex h-9 px-3 items-center rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800">
                  Ver proyecto
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-8 text-slate-500">
                No tienes tareas asignadas.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
