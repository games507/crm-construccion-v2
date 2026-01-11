@extends('layouts.base')
@section('title','Empresas')

@section('content')
@php
  $q = $q ?? request('q','');
@endphp

<div class="max-w-6xl mx-auto space-y-4">

  {{-- Header --}}
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-xl font-extrabold tracking-tight text-slate-900">Empresas</h1>
      <p class="text-sm text-slate-500 mt-1">Multiempresa: crea, edita y administra compañías.</p>
    </div>

    <div class="flex flex-wrap items-center gap-2">
      <form method="GET" action="{{ route('admin.empresas') }}" class="flex items-center gap-2 flex-wrap">
        <div class="relative">
          <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-black">Q</span>
          <input
            name="q"
            value="{{ $q }}"
            placeholder="Buscar por nombre, ruc o correo..."
            class="h-11 w-[280px] rounded-xl border border-slate-900/10 bg-white pl-10 pr-3 text-sm font-semibold
                   shadow-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300"
          >
        </div>
        <button type="submit"
          class="inline-flex items-center justify-center rounded-xl px-4 h-11 text-sm font-semibold
                 bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm">
          Buscar
        </button>
      </form>

      {{-- Botón Nueva Empresa (SIN @can, porque este módulo es solo SuperAdmin) --}}
      <a href="{{ route('admin.empresas.create') }}"
         class="inline-flex items-center justify-center rounded-xl px-4 h-11 text-sm font-semibold
                bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm">
        + Nueva Empresa
      </a>
    </div>
  </div>

  {{-- Alerts --}}
  @if(session('ok'))
    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900 font-semibold">
      {{ session('ok') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-900 font-semibold">
      {{ $errors->first() }}
    </div>
  @endif

  {{-- Table --}}
  <div class="overflow-hidden rounded-2xl border border-slate-900/10 bg-white shadow-sm">
    <div class="overflow-auto">
      <table class="min-w-[980px] w-full border-collapse">
        <thead class="bg-slate-50">
          <tr class="text-left text-xs font-black uppercase tracking-wide text-slate-600">
            <th class="px-4 py-3 border-b border-slate-900/10">Empresa</th>
            <th class="px-4 py-3 border-b border-slate-900/10">RUC</th>
            <th class="px-4 py-3 border-b border-slate-900/10">Contacto</th>
            <th class="px-4 py-3 border-b border-slate-900/10">Estado</th>
            <th class="px-4 py-3 border-b border-slate-900/10 w-[240px]">Acciones</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-900/5">
          @forelse($empresas as $e)
            <tr class="hover:bg-slate-50/60">
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <div class="h-9 w-9 rounded-2xl bg-indigo-50 text-indigo-700 grid place-items-center font-black">
                    {{ strtoupper(substr($e->nombre,0,1)) }}
                  </div>
                  <div class="min-w-0">
                    <div class="font-extrabold text-slate-900 truncate">{{ $e->nombre }}</div>
                    <div class="text-xs font-semibold text-slate-500">ID: {{ $e->id }}</div>
                  </div>
                </div>
              </td>

              <td class="px-4 py-3">
                <span class="font-extrabold text-slate-900">{{ $e->ruc ?: '—' }}</span>
              </td>

              <td class="px-4 py-3">
                <div class="font-semibold text-slate-900">{{ $e->email ?: '—' }}</div>
                <div class="text-xs font-semibold text-slate-500">{{ $e->telefono ?: '' }}</div>
              </td>

              <td class="px-4 py-3">
                @if((int)$e->activa === 1)
                  <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-black
                               bg-emerald-50 border border-emerald-200 text-emerald-800">
                    ACTIVA
                  </span>
                @else
                  <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-black
                               bg-rose-50 border border-rose-200 text-rose-800">
                    INACTIVA
                  </span>
                @endif
              </td>

              {{-- Acciones (SIN @can) --}}
              <td class="px-4 py-3">
                <div class="flex flex-wrap gap-2">
                  <a href="{{ route('admin.empresas.edit',$e) }}"
                     class="inline-flex items-center justify-center rounded-xl px-3 h-10 text-sm font-semibold
                            bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm">
                    Editar
                  </a>

                  <form method="POST" action="{{ route('admin.empresas.destroy',$e) }}"
                        onsubmit="return confirm('¿Eliminar esta empresa?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                      class="inline-flex items-center justify-center rounded-xl px-3 h-10 text-sm font-semibold
                             bg-white border border-rose-200 text-rose-700 hover:border-rose-300 shadow-sm">
                      Eliminar
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-4 py-6 text-slate-500 font-semibold">
                No hay empresas.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    @if(method_exists($empresas,'links'))
      <div class="px-4 py-3 border-t border-slate-900/10">
        {{ $empresas->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
