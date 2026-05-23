@extends('layouts.base')
@section('title','Detalle de cuenta')

@section('content')

<div class="space-y-6">

  {{-- HEADER --}}
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">
        {{ $cuenta->proveedor }}
      </h1>
      <p class="text-sm text-slate-500 mt-1">
        {{ $cuenta->descripcion ?: 'Detalle de cuenta por pagar' }}
      </p>
    </div>

    <a href="{{ route('admin.cuentas.index') }}"
       class="inline-flex items-center justify-center h-10 px-4 rounded-xl bg-white border border-slate-200 text-sm font-bold text-slate-700 hover:bg-slate-50 shadow-sm">
      Volver
    </a>
  </div>

  {{-- RESUMEN --}}
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
      <div class="text-xs uppercase tracking-wide text-slate-500 font-black">Total</div>
      <div class="mt-2 text-2xl font-black text-slate-900">
        $ {{ number_format((float) $cuenta->monto_total, 2, '.', ',') }}
      </div>
    </div>

    <div class="bg-emerald-50 p-5 rounded-2xl border border-emerald-200 shadow-sm">
      <div class="text-xs uppercase tracking-wide text-emerald-700 font-black">Pagado</div>
      <div class="mt-2 text-2xl font-black text-emerald-800">
        $ {{ number_format((float) $cuenta->monto_pagado, 2, '.', ',') }}
      </div>
    </div>

    <div class="bg-rose-50 p-5 rounded-2xl border border-rose-200 shadow-sm">
      <div class="text-xs uppercase tracking-wide text-rose-700 font-black">Saldo</div>
      <div class="mt-2 text-2xl font-black text-rose-800">
        $ {{ number_format((float) $cuenta->saldo, 2, '.', ',') }}
      </div>
    </div>

  </div>

  {{-- INFO --}}
  <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">

    <div>
      <span class="text-xs uppercase tracking-wide text-slate-500 font-black">Proyecto</span><br>
      <strong class="text-slate-900">{{ $cuenta->proyecto->nombre ?? '-' }}</strong>
    </div>

    <div>
      <span class="text-xs uppercase tracking-wide text-slate-500 font-black">Estado</span><br>
      @php
        $badge = match($cuenta->estado) {
          'pagado'  => 'bg-emerald-100 text-emerald-700',
          'parcial' => 'bg-amber-100 text-amber-700',
          default   => 'bg-rose-100 text-rose-700',
        };
      @endphp
      <span class="inline-flex mt-1 px-2 py-1 rounded-full text-xs font-black {{ $badge }}">
        {{ ucfirst($cuenta->estado) }}
      </span>
    </div>

    <div>
      <span class="text-xs uppercase tracking-wide text-slate-500 font-black">Fecha</span><br>
      <strong class="text-slate-900">
        {{ $cuenta->fecha ? $cuenta->fecha->format('d/m/Y') : '-' }}
      </strong>
    </div>

    <div>
      <span class="text-xs uppercase tracking-wide text-slate-500 font-black">Vencimiento</span><br>
      <strong class="text-slate-900">
        {{ $cuenta->fecha_vencimiento ? $cuenta->fecha_vencimiento->format('d/m/Y') : '-' }}
      </strong>
    </div>

  </div>

  {{-- BOTÓN PAGAR --}}
  @if((float) $cuenta->saldo > 0)
    <div class="flex justify-end">
      <button type="button"
              onclick="document.getElementById('modalPago').classList.remove('hidden')"
              class="inline-flex items-center justify-center h-10 px-4 rounded-xl bg-emerald-600 text-white text-sm font-extrabold hover:bg-emerald-700 shadow-sm">
        Registrar pago
      </button>
    </div>
  @endif

  {{-- HISTORIAL DE PAGOS --}}
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
      <div>
        <h2 class="text-lg font-extrabold text-slate-900">
          Historial de pagos
        </h2>
        <p class="text-xs text-slate-500">
          Fecha, hora y usuario que registró cada pago
        </p>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="px-4 py-3 text-left">Fecha y hora</th>
            <th class="px-4 py-3 text-right">Monto</th>
            <th class="px-4 py-3 text-left">Usuario</th>
            <th class="px-4 py-3 text-left">Observación</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
          @forelse($cuenta->pagos as $p)
            <tr class="hover:bg-slate-50">
              <td class="px-4 py-3 font-bold text-slate-800">
                {{ $p->fecha ? \Carbon\Carbon::parse($p->fecha)->format('d/m/Y h:i A') : '-' }}
              </td>

              <td class="px-4 py-3 text-right text-emerald-700 font-black">
                $ {{ number_format((float) $p->monto, 2, '.', ',') }}
              </td>

              <td class="px-4 py-3 text-slate-700 font-semibold">
                {{ $p->user->name ?? $p->user->nombre ?? 'Sistema' }}
              </td>

              <td class="px-4 py-3 text-slate-500">
                {{ $p->observacion ?: 'Pago registrado' }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center text-slate-500 py-8">
                No hay pagos registrados.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>

{{-- MODAL PAGO --}}
<div id="modalPago" class="hidden fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">

  <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">

    <div class="p-5 border-b border-slate-100 flex items-start justify-between">
      <div>
        <h2 class="text-lg font-extrabold text-slate-900">
          Registrar pago
        </h2>
        <p class="text-sm text-slate-500">
          {{ $cuenta->proveedor }}
        </p>
      </div>

      <button type="button"
              onclick="document.getElementById('modalPago').classList.add('hidden')"
              class="text-slate-400 hover:text-slate-700">
        ✕
      </button>
    </div>

    <form method="POST" action="{{ route('admin.cuentas.pagar', $cuenta->id) }}" class="p-5">
      @csrf

      <label class="text-xs font-bold text-slate-500">Monto</label>
      <input type="text"
             name="monto"
             inputmode="decimal"
             class="mt-1 w-full border border-slate-300 rounded-xl px-3 h-11 text-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 outline-none"
             placeholder="Monto a pagar"
             oninput="this.value=this.value.replace(/[^0-9.]/g,'')"
             autocomplete="off"
             required>

      <label class="block mt-3 text-xs font-bold text-slate-500">Fecha</label>
      <input type="date"
             name="fecha"
             class="mt-1 w-full border border-slate-300 rounded-xl px-3 h-11 text-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 outline-none">

      <label class="block mt-3 text-xs font-bold text-slate-500">Observación</label>
      <input type="text"
             name="observacion"
             class="mt-1 w-full border border-slate-300 rounded-xl px-3 h-11 text-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 outline-none"
             placeholder="Ej: Abono inicial">

      <div class="flex justify-end gap-2 mt-5">
        <button type="button"
                onclick="document.getElementById('modalPago').classList.add('hidden')"
                class="inline-flex items-center justify-center h-10 px-4 rounded-xl bg-white border border-slate-200 text-sm font-bold text-slate-700 hover:bg-slate-50">
          Cancelar
        </button>

        <button type="submit"
                class="inline-flex items-center justify-center h-10 px-4 rounded-xl bg-emerald-600 text-white text-sm font-extrabold hover:bg-emerald-700 shadow-sm">
          Guardar pago
        </button>
      </div>

    </form>

  </div>

</div>

@endsection