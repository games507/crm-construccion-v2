@extends('layouts.base')
@section('title','Detalle de cuenta')

@section('content')

<div class="space-y-6">

  {{-- HEADER --}}
  <div>
    <h1 class="text-2xl font-extrabold text-slate-900">
      {{ $cuenta->proveedor }}
    </h1>
    <p class="text-sm text-slate-500">
      {{ $cuenta->descripcion }}
    </p>
  </div>

  {{-- RESUMEN --}}
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

    <div class="bg-white p-5 rounded-xl border">
      <div class="text-xs text-slate-500">Total</div>
      <div class="text-xl font-bold">
        $ {{ number_format($cuenta->monto_total,2) }}
      </div>
    </div>

    <div class="bg-emerald-50 p-5 rounded-xl border border-emerald-200">
      <div class="text-xs text-emerald-700">Pagado</div>
      <div class="text-xl font-bold text-emerald-800">
        $ {{ number_format($cuenta->monto_pagado,2) }}
      </div>
    </div>

    <div class="bg-rose-50 p-5 rounded-xl border border-rose-200">
      <div class="text-xs text-rose-700">Saldo</div>
      <div class="text-xl font-bold text-rose-800">
        $ {{ number_format($cuenta->saldo,2) }}
      </div>
    </div>

  </div>

  {{-- INFO --}}
  <div class="bg-white p-5 rounded-xl border grid grid-cols-2 gap-4 text-sm">

    <div>
      <span class="text-slate-500">Proyecto:</span><br>
      <strong>{{ $cuenta->proyecto->nombre ?? '-' }}</strong>
    </div>

    <div>
      <span class="text-slate-500">Estado:</span><br>
      <strong>{{ ucfirst($cuenta->estado) }}</strong>
    </div>

    <div>
      <span class="text-slate-500">Fecha:</span><br>
      <strong>{{ $cuenta->fecha }}</strong>
    </div>

    <div>
      <span class="text-slate-500">Vencimiento:</span><br>
      <strong>{{ $cuenta->fecha_vencimiento }}</strong>
    </div>

  </div>

  {{-- BOTÓN PAGAR --}}
  @if($cuenta->saldo > 0)
  <div class="flex justify-end">
    <button onclick="document.getElementById('modalPago').classList.remove('hidden')"
      class="bg-emerald-600 text-white px-4 py-2 rounded-xl font-bold">
      Registrar pago
    </button>
  </div>
  @endif

  {{-- HISTORIAL --}}
  <div class="bg-white rounded-xl border">

    <div class="p-4 border-b font-bold">
      Historial de pagos
    </div>

    @forelse($cuenta->pagos as $p)
    <div class="flex justify-between p-4 border-b text-sm">

      <div>
        <div class="font-bold text-emerald-700">
          $ {{ number_format($p->monto,2) }}
        </div>
        <div class="text-slate-500">
          {{ $p->observacion }}
        </div>
      </div>

      <div class="text-slate-500">
        {{ $p->fecha }}
      </div>

    </div>
    @empty
    <div class="p-4 text-center text-slate-500">
      No hay pagos
    </div>
    @endforelse

  </div>

</div>

{{-- MODAL PAGO --}}
<div id="modalPago" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center">

  <div class="bg-white p-6 rounded-xl w-full max-w-md">

    <h2 class="font-bold mb-3">Registrar pago</h2>

    <form method="POST" action="{{ url('/app/admin/cuentas/'.$cuenta->id.'/pagar') }}">
      @csrf

      <input type="number" step="0.01" name="monto"
        class="w-full border rounded p-2 mb-2"
        placeholder="Monto">

      <input type="date" name="fecha"
        class="w-full border rounded p-2 mb-2">

      <input type="text" name="observacion"
        class="w-full border rounded p-2 mb-3"
        placeholder="Observación">

      <div class="flex justify-end gap-2">
        <button type="button"
          onclick="document.getElementById('modalPago').classList.add('hidden')">
          Cancelar
        </button>

        <button class="bg-emerald-600 text-white px-3 py-2 rounded">
          Guardar
        </button>
      </div>

    </form>

  </div>

</div>

@endsection