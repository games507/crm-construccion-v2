@extends('layouts.base')
@section('title','Almacenes')

@section('content')
@php
  $qVal = trim((string)($q ?? ''));
@endphp

<div class="max-w-7xl mx-auto">

  {{-- Header --}}
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-xl font-extrabold tracking-tight text-slate-900">Almacenes</h1>
      <p class="text-sm text-slate-500 mt-1">
        Administra los almacenes de tu empresa (código, nombre, ubicación y estado).
      </p>
    </div>

    <div class="flex gap-2 flex-wrap">
      @can('almacenes.crear')
        <a href="{{ route('inventario.almacenes.create') }}"
           class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold
                  bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm">
          + Nuevo almacén
        </a>
      @endcan
    </div>
  </div>

  {{-- Alerts --}}
  @if (session('ok'))
    <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
      ✅ {{ session('ok') }}
    </div>
  @endif

  @if (session('err'))
    <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
      ❌ {{ session('err') }}
    </div>
  @endif

  {{-- Filtros --}}
  <form method="GET" action="{{ route('inventario.almacenes') }}"
        class="mt-5 rounded-2xl border border-slate-900/10 bg-white shadow-sm p-4">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-3">

      <div class="md:col-span-8">
        <label class="text-xs font-semibold text-slate-500">Buscar</label>
        <input name="q" value="{{ $qVal }}" placeholder="Código, nombre o ubicación…"
               class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3 bg-white
                      focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none">
      </div>

      <div class="md:col-span-4 flex items-end gap-2 justify-end">
        <button type="submit"
                class="inline-flex items-center justify-center rounded-xl px-4 h-11 text-sm font-semibold
                       bg-slate-900 text-white hover:bg-slate-800">
          Buscar
        </button>

        @if($qVal !== '')
          <a href="{{ route('inventario.almacenes') }}"
             class="inline-flex items-center justify-center rounded-xl px-4 h-11 text-sm font-semibold
                    bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm">
            Limpiar
          </a>
        @endif
      </div>

      <div class="md:col-span-12 text-xs text-slate-500 flex justify-end">
        <div class="text-right">
          <div class="font-semibold text-slate-700">Registros</div>
          <div>{{ $almacenes->total() }}</div>
        </div>
      </div>

    </div>
  </form>

  {{-- Tabla --}}
  <div class="mt-5 rounded-2xl border border-slate-900/10 bg-white shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr class="text-left">
            <th class="px-4 py-3 font-semibold">Código</th>
            <th class="px-4 py-3 font-semibold">Nombre</th>
            <th class="px-4 py-3 font-semibold">Ubicación</th>
            <th class="px-4 py-3 font-semibold">Estado</th>
            <th class="px-4 py-3 font-semibold text-right">Acciones</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
          @forelse($almacenes as $a)
            <tr class="hover:bg-slate-50/60">
              <td class="px-4 py-3 font-extrabold text-slate-900">{{ $a->codigo }}</td>

              <td class="px-4 py-3">
                <div class="font-semibold text-slate-900">{{ $a->nombre }}</div>
              </td>

              <td class="px-4 py-3 text-slate-700">{{ $a->ubicacion ?? '—' }}</td>

              <td class="px-4 py-3">
                @if((int)$a->activo === 1)
                  <span class="inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 px-3 py-1 text-xs font-bold border border-emerald-200">
                    Activo
                  </span>
                @else
                  <span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-3 py-1 text-xs font-bold border border-slate-200">
                    Inactivo
                  </span>
                @endif
              </td>

              <td class="px-4 py-3 text-right whitespace-nowrap">
                <div class="inline-flex items-center gap-1">

                  @can('almacenes.editar')
                    <a href="{{ route('inventario.almacenes.edit', $a->id) }}"
                       title="Editar almacén"
                       class="inline-flex items-center justify-center h-9 w-9 rounded-lg
                              border border-slate-900/10 bg-white
                              text-slate-600 hover:text-indigo-700 hover:border-indigo-200
                              hover:bg-indigo-50 transition">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                           fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16.862 4.487a2.25 2.25 0 1 1 3.182 3.182L7.5 20.213 3 21l.787-4.5L16.862 4.487Z"/>
                      </svg>
                    </a>
                  @endcan

                  @can('almacenes.eliminar')
                    <form action="{{ route('inventario.almacenes.destroy', $a->id) }}"
                          method="POST"
                          onsubmit="return confirm('¿Eliminar este almacén? Si tiene inventario, se marcará como INACTIVO.');">
                      @csrf
                      @method('DELETE')

                      <button type="submit"
                              title="Eliminar almacén"
                              class="inline-flex items-center justify-center h-9 w-9 rounded-lg
                                     border border-red-200 bg-red-50
                                     text-red-600 hover:bg-red-100 hover:border-red-300 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                          <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6 7.5h12m-9 3v6m6-6v6M9 3.75h6a1.5 1.5 0 0 1 1.5 1.5V7.5h-9V5.25A1.5 1.5 0 0 1 9 3.75Z"/>
                        </svg>
                      </button>
                    </form>
                  @endcan

                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td class="px-4 py-10 text-center text-slate-500" colspan="5">
                No hay almacenes para los filtros seleccionados.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Paginación --}}
  <div class="mt-4">
    {{ $almacenes->links() }}
  </div>

</div>
@endsection
