@extends('layouts.base')
@section('title','Nuevo Movimiento')

@section('content')
@php
  $icon = function($n){
    if($n==='back') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 12h12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($n==='save') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" stroke="currentColor" stroke-width="2"/><path d="M17 21v-8H7v8" stroke="currentColor" stroke-width="2"/><path d="M7 3v5h8" stroke="currentColor" stroke-width="2"/></svg>';
    if($n==='alert') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" stroke="currentColor" stroke-width="2"/><path d="M12 9v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M12 17h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>';
    return '';
  };

  $tipo = old('tipo','entrada');
@endphp

<div class="max-w-5xl mx-auto">

  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-xl font-extrabold tracking-tight">Nuevo Movimiento</h1>
      <p class="text-sm text-slate-500 mt-1">Entradas, salidas, traslados y ajustes de inventario.</p>
    </div>

    <a class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold
              bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm"
       href="{{ route('inventario.movimientos') }}">
      {!! $icon('back') !!} Volver
    </a>
  </div>

  @if (session('ok'))
    <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
      ✅ {{ session('ok') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
      <div class="flex items-center gap-2 font-semibold">
        <span>{!! $icon('alert') !!}</span>
        <span>Hay errores</span>
      </div>
      <ul class="list-disc ml-6 mt-2 text-sm">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="mt-5 rounded-2xl border border-slate-900/10 bg-white shadow-sm">
    <form method="POST" action="{{ route('inventario.movimientos.store') }}" class="p-5">
      @csrf

      <div class="grid grid-cols-1 md:grid-cols-12 gap-4">

        {{-- Fecha --}}
        <div class="md:col-span-3">
          <label class="text-xs font-semibold text-slate-500">Fecha</label>
          <input class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3
                        focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none"
                 type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required>
        </div>

        {{-- Tipo --}}
        <div class="md:col-span-3">
          <label class="text-xs font-semibold text-slate-500">Tipo</label>
          <select class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3 bg-white
                         focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none"
                  name="tipo" id="tipo" required>
            <option value="entrada"  @selected($tipo==='entrada')>Entrada</option>
            <option value="salida"   @selected($tipo==='salida')>Salida</option>
            <option value="traslado" @selected($tipo==='traslado')>Traslado</option>
            <option value="ajuste"   @selected($tipo==='ajuste')>Ajuste</option>
          </select>
          <div class="text-xs text-slate-400 mt-1">
            Entrada/Ajuste = usa Destino · Salida = usa Origen · Traslado = ambos
          </div>
        </div>

        {{-- Material --}}
        <div class="md:col-span-6">
          <label class="text-xs font-semibold text-slate-500">Material</label>
          <select class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3 bg-white
                         focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none"
                  name="material_id" required>
            <option value="">— Seleccione —</option>
            @foreach($materiales as $m)
              <option value="{{ $m->id }}" @selected((string)old('material_id')===(string)$m->id)>
                {{ $m->codigo ?? $m->sku }} — {{ $m->descripcion }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Origen --}}
        <div class="md:col-span-6" id="box-origen">
          <label class="text-xs font-semibold text-slate-500">Almacén origen</label>
          <select class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3 bg-white
                         focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none"
                  name="almacen_origen_id" id="almacen_origen_id">
            <option value="">— Seleccione —</option>
            @foreach($almacenes as $a)
              <option value="{{ $a->id }}" @selected((string)old('almacen_origen_id')===(string)$a->id)>
                {{ $a->codigo }} — {{ $a->nombre }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Destino --}}
        <div class="md:col-span-6" id="box-destino">
          <label class="text-xs font-semibold text-slate-500">Almacén destino</label>
          <select class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3 bg-white
                         focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none"
                  name="almacen_destino_id" id="almacen_destino_id">
            <option value="">— Seleccione —</option>
            @foreach($almacenes as $a)
              <option value="{{ $a->id }}" @selected((string)old('almacen_destino_id')===(string)$a->id)>
                {{ $a->codigo }} — {{ $a->nombre }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Cantidad --}}
        <div class="md:col-span-3">
          <label class="text-xs font-semibold text-slate-500">Cantidad</label>
          <input class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3
                        focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none"
                 type="number" step="1" name="cantidad" value="{{ old('cantidad') }}" required>
        </div>

        {{-- Costo unitario --}}
        <div class="md:col-span-3">
          <label class="text-xs font-semibold text-slate-500">Costo unitario</label>
          <input class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3
                        focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none"
                 type="number" step="0.01" name="costo_unitario" value="{{ old('costo_unitario') }}">
        </div>

        {{-- Referencia --}}
        <div class="md:col-span-6">
          <label class="text-xs font-semibold text-slate-500">Referencia</label>
          <input class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3
                        focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none"
                 name="referencia" value="{{ old('referencia') }}" placeholder="Factura, orden, ajuste…">
        </div>

      </div>

      <div class="mt-6 flex justify-end">
        <button class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold
                       bg-blue-600 text-white hover:bg-blue-700 shadow-sm"
                type="submit">
          {!! $icon('save') !!} Guardar movimiento
        </button>
      </div>
    </form>
  </div>

</div>

@push('scripts')
<script>
(function(){
  function syncTipo(){
    const tipo = document.getElementById('tipo');
    const oBox = document.getElementById('box-origen');
    const dBox = document.getElementById('box-destino');
    const oSel = document.getElementById('almacen_origen_id');
    const dSel = document.getElementById('almacen_destino_id');

    if(!tipo || !oBox || !dBox || !oSel || !dSel) return;

    const t = (tipo.value || '').trim();

    // reset
    oBox.style.display = 'none';
    dBox.style.display = 'none';
    oSel.required = false;
    dSel.required = false;

    if(t === 'entrada' || t === 'ajuste'){
      dBox.style.display = '';
      dSel.required = true;
    } else if(t === 'salida'){
      oBox.style.display = '';
      oSel.required = true;
    } else if(t === 'traslado'){
      oBox.style.display = '';
      dBox.style.display = '';
      oSel.required = true;
      dSel.required = true;
    } else {
      // fallback: mostrar ambos para evitar “no aparece”
      oBox.style.display = '';
      dBox.style.display = '';
    }
  }

  document.addEventListener('DOMContentLoaded', function(){
    const tipo = document.getElementById('tipo');
    if(tipo){
      tipo.addEventListener('change', syncTipo);
      syncTipo();
    }
  });
})();
</script>
@endpush

@endsection
