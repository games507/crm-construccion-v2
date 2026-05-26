@extends('layouts.base')
@section('title','Cuentas por cobrar')

@section('content')

@php
$totalFacturado = (float) $cuentas->sum('monto_total');
$totalCobrado = (float) $cuentas->sum('monto_cobrado');
$saldoPendiente = (float) $cuentas->sum('saldo');

$cuentasVencidas = $cuentas->filter(function($c){
    return $c->fecha_vencimiento
        && $c->fecha_vencimiento->lt(now()->startOfDay())
        && (float)$c->saldo > 0;
});

$totalVencido = (float)$cuentasVencidas->sum('saldo');
$cantidadVencidas = $cuentasVencidas->count();
@endphp

<div
x-data="{
    openModal:null,
    cuenta:null
}"
class="space-y-6"
>

    {{-- HEADER --}}
    <div class="flex items-center justify-between flex-wrap gap-3">

        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">
                Cuentas por cobrar
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Gestión de clientes, cobros y saldos pendientes
            </p>
        </div>

        <button
            @click="openModal='crear'"
            class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-4 h-10 text-sm font-bold text-indigo-700 hover:bg-indigo-100 transition"
        >
            + Nueva cuenta
        </button>

    </div>

    {{-- ALERTA --}}
    @if($cantidadVencidas > 0)
    <div class="rounded-3xl border border-rose-200 bg-rose-50 p-4 shadow-sm flex items-center justify-between gap-3">

        <div>
            <div class="font-black text-rose-800">
                {{ $cantidadVencidas }} cuentas vencidas
            </div>

            <div class="text-sm text-rose-700 mt-1">
                Saldo vencido:
                <strong>
                    $ {{ number_format($totalVencido,2) }}
                </strong>
            </div>
        </div>

        <div class="h-11 px-4 rounded-2xl bg-rose-600 text-white flex items-center justify-center font-black text-sm">
            Atención
        </div>

    </div>
    @endif

{{-- DASHBOARD --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs uppercase tracking-wide text-slate-500 font-black">
                    Total facturado
                </div>
                <div class="mt-2 text-2xl font-black text-slate-900">
                    $ {{ number_format($totalFacturado,2) }}
                </div>
            </div>

            <div class="h-10 w-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M4 5h16v14H4V5Z" stroke="currentColor" stroke-width="2"/>
                    <path d="M8 9h8M8 13h5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-emerald-200 bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs uppercase tracking-wide text-emerald-600 font-black">
                    Total cobrado
                </div>
                <div class="mt-2 text-2xl font-black text-emerald-700">
                    $ {{ number_format($totalCobrado,2) }}
                </div>
            </div>

            <div class="h-10 w-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-amber-200 bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs uppercase tracking-wide text-amber-600 font-black">
                    Pendiente
                </div>
                <div class="mt-2 text-2xl font-black text-amber-700">
                    $ {{ number_format($saldoPendiente,2) }}
                </div>
            </div>

            <div class="h-10 w-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-rose-200 bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs uppercase tracking-wide text-rose-600 font-black">
                    Vencidas
                </div>
                <div class="mt-2 text-2xl font-black text-rose-700">
                    {{ $cantidadVencidas }}
                </div>
            </div>

            <div class="h-10 w-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M12 9v4m0 4h.01M10.3 4.4 2.6 18a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 4.4a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
    </div>

</div>
    {{-- TABLA --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50 text-slate-600">

                    <tr>
                        <th class="px-4 py-3 text-left">Cliente</th>
                        <th class="px-4 py-3 text-left">Proyecto</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-right">Cobrado</th>
                        <th class="px-4 py-3 text-right">Saldo</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>

                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse($cuentas as $c)

                    @php
                    $badge = match($c->estado){
                        'cobrado' => 'bg-emerald-100 text-emerald-700',
                        'parcial' => 'bg-amber-100 text-amber-700',
                        default => 'bg-rose-100 text-rose-700',
                    };
                    @endphp

                    <tr class="hover:bg-slate-50">

                        <td class="px-4 py-4">
                            <div class="font-black text-slate-900">
                                {{ $c->cliente }}
                            </div>

                            <div class="text-xs text-slate-500 mt-1">
                                {{ $c->descripcion }}
                            </div>
                        </td>

                        <td class="px-4 py-4 text-slate-600">
                            {{ $c->proyecto->nombre ?? '-' }}
                        </td>

                        <td class="px-4 py-4 text-right font-semibold">
                            $ {{ number_format($c->monto_total,2) }}
                        </td>

                        <td class="px-4 py-4 text-right font-bold text-emerald-700">
                            $ {{ number_format($c->monto_cobrado,2) }}
                        </td>

                        <td class="px-4 py-4 text-right font-black text-rose-700">
                            $ {{ number_format($c->saldo,2) }}
                        </td>

                        <td class="px-4 py-4 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-black {{ $badge }}">
                                {{ ucfirst($c->estado) }}
                            </span>
                        </td>

                        <td class="px-4 py-4">

                         
<div class="flex justify-center gap-3">

  {{-- DETALLE --}}
    <button
        type="button"
        title="Detalle"
        @click="openModal='detalle'; cuenta={{ Js::from($c) }}"
        class="text-slate-500 hover:text-indigo-600 transition"
    >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"
                  stroke="currentColor"
                  stroke-width="2"/>
            <circle cx="12" cy="12" r="3"
                    stroke="currentColor"
                    stroke-width="2"/>
        </svg>
    </button>

    {{-- REGISTRAR COBRO --}}
    @if((float)$c->saldo > 0)
    <button
        type="button"
        title="Registrar cobro"
        @click="openModal='cobrar'; cuenta={{ Js::from($c) }}"
        class="text-slate-500 hover:text-emerald-600 transition"
    >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
            <rect x="3" y="6" width="18" height="12" rx="2"
                  stroke="currentColor"
                  stroke-width="2"/>
            <circle cx="12" cy="12" r="2.5"
                    stroke="currentColor"
                    stroke-width="2"/>
        </svg>
    </button>
    @endif

    {{-- EDITAR --}}
    <button
        type="button"
        title="Editar"
        @click="openModal='editar'; cuenta={{ Js::from($c) }}"
        class="text-slate-500 hover:text-blue-600 transition"
    >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
            <path d="M4 20h4l10-10-4-4L4 16v4z"
                  stroke="currentColor"
                  stroke-width="2"/>
        </svg>
    </button>

</div>
                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="7" class="text-center py-10 text-slate-500">
                            No hay cuentas por cobrar registradas
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

            {{-- MODAL DETALLE --}}
<div x-show="openModal === 'detalle'"
     x-cloak
     class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">

  <div @click.outside="openModal=null"
       class="bg-white rounded-2xl p-6 w-full max-w-lg shadow-2xl">

    <div class="flex items-start justify-between gap-3 mb-4">
      <div>
        <h2 class="text-lg font-extrabold text-slate-900">Detalle de cobros</h2>
        <p class="text-sm text-slate-500" x-text="cuenta?.cliente"></p>
      </div>

      <button type="button" @click="openModal=null" class="text-slate-400 hover:text-slate-700">
        ✕
      </button>
    </div>

    <template x-if="cuenta">
      <div>
        <div class="grid grid-cols-3 gap-3 mb-4">
          <div class="rounded-xl bg-slate-50 border border-slate-200 p-3">
            <div class="text-xs text-slate-500 font-bold">Total</div>
            <div class="font-extrabold text-slate-900">$<span x-text="Number(cuenta.monto_total || 0).toFixed(2)"></span></div>
          </div>

          <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-3">
            <div class="text-xs text-emerald-700 font-bold">Cobrado</div>
            <div class="font-extrabold text-emerald-800">$<span x-text="Number(cuenta.monto_cobrado || 0).toFixed(2)"></span></div>
          </div>

          <div class="rounded-xl bg-rose-50 border border-rose-200 p-3">
            <div class="text-xs text-rose-700 font-bold">Saldo</div>
            <div class="font-extrabold text-rose-800">$<span x-text="Number(cuenta.saldo || 0).toFixed(2)"></span></div>
          </div>
        </div>

        <div class="rounded-xl border border-slate-200 overflow-hidden">
          <div class="px-3 py-2 bg-slate-50 text-xs font-bold text-slate-500 uppercase">
            Historial
          </div>

          <template x-if="cuenta.cobros && cuenta.cobros.length">
            <div class="divide-y divide-slate-100">
              <template x-for="p in cuenta.cobros" :key="p.id">
                <div class="px-3 py-3 flex items-start justify-between gap-3">
                  <div>
                    <div class="font-bold text-slate-800">
                      $<span x-text="Number(p.monto || 0).toFixed(2)"></span>
                    </div>
                    <div class="text-xs text-slate-500" x-text="p.observacion || 'Cobro registrado'"></div>
                  </div>
                  <div class="text-xs font-bold text-slate-500" x-text="p.fecha || '—'"></div>
                </div>
              </template>
            </div>
          </template>

          <template x-if="!cuenta.cobros || !cuenta.cobros.length">
            <div class="px-3 py-6 text-center text-sm text-slate-500">
              No hay cobros registrados.
            </div>
          </template>
        </div>
      </div>
    </template>

    <div class="text-right mt-5">
      <button type="button"
              @click="openModal=null"
              class="px-4 h-10 rounded-xl bg-slate-900 text-white text-sm font-bold hover:bg-slate-800">
        Cerrar
      </button>
    </div>
  </div>
</div>
{{-- MODAL CREAR --}}
<div x-show="openModal === 'crear'"
     x-cloak
     class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">

  <div @click.outside="openModal=null"
       class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">

    <div class="p-5 border-b flex items-start justify-between">
      <div>
        <h2 class="text-lg font-extrabold text-slate-900">Nueva cuenta por cobrar</h2>
        <p class="text-sm text-slate-500">Registra una cuenta pendiente de cobro</p>
      </div>

      <button type="button" @click="openModal=null" class="text-slate-400 hover:text-slate-700">
        ✕
      </button>
    </div>

    <form method="POST" action="{{ route('admin.cobros.store') }}" class="p-5">
      @csrf

      <label class="text-xs font-bold text-slate-500">Proyecto</label>
      <select name="proyecto_id"
              class="mt-1 w-full border border-slate-300 rounded-xl px-3 h-11 text-sm outline-none"
              required>
        <option value="">Selecciona un proyecto</option>
        @foreach($proyectos as $p)
          <option value="{{ $p->id }}">{{ $p->nombre }}</option>
        @endforeach
      </select>

      <label class="block mt-3 text-xs font-bold text-slate-500">Cliente</label>
      <input type="text" name="cliente"
             class="mt-1 w-full border border-slate-300 rounded-xl px-3 h-11 text-sm outline-none"
             required>

      <label class="block mt-3 text-xs font-bold text-slate-500">Descripción</label>
      <input type="text" name="descripcion"
             class="mt-1 w-full border border-slate-300 rounded-xl px-3 h-11 text-sm outline-none">

      <label class="block mt-3 text-xs font-bold text-slate-500">Monto total</label>
      <input type="text" name="monto_total"
             inputmode="decimal"
             oninput="this.value=this.value.replace(/[^0-9.]/g,'')"
             class="mt-1 w-full border border-slate-300 rounded-xl px-3 h-11 text-sm outline-none"
             required>

      <div class="grid grid-cols-2 gap-3 mt-3">
        <div>
          <label class="text-xs font-bold text-slate-500">Fecha</label>
          <input type="date" name="fecha"
                 class="mt-1 w-full border border-slate-300 rounded-xl px-3 h-11 text-sm outline-none">
        </div>

        <div>
          <label class="text-xs font-bold text-slate-500">Vencimiento</label>
          <input type="date" name="fecha_vencimiento"
                 class="mt-1 w-full border border-slate-300 rounded-xl px-3 h-11 text-sm outline-none">
        </div>
      </div>

      <div class="flex justify-end gap-2 mt-5">
        <button type="button"
                @click="openModal=null"
                class="px-4 h-10 rounded-xl bg-white border border-slate-200 text-sm font-bold">
          Cancelar
        </button>

        <button type="submit"
                class="px-4 h-10 rounded-xl bg-indigo-600 text-white text-sm font-extrabold hover:bg-indigo-700">
          Guardar
        </button>
      </div>
    </form>
  </div>
</div>
{{-- MODAL COBRAR --}}
<div x-show="openModal === 'cobrar'"
     x-cloak
     class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">

  <div @click.outside="openModal=null"
       class="bg-white rounded-2xl w-full max-w-sm shadow-2xl overflow-hidden">

    <div class="p-5 border-b flex items-start justify-between">
      <div>
        <h2 class="text-lg font-extrabold text-slate-900">
          Registrar cobro
        </h2>

        <p class="text-sm text-slate-500"
           x-text="cuenta?.cliente">
        </p>
      </div>

      <button type="button"
              @click="openModal=null"
              class="text-slate-400 hover:text-slate-700">
        ✕
      </button>
    </div>

<form method="POST"
      :action="'{{ url('/app/admin/cobros') }}/' + cuenta.id + '/cobrar'"
      
      class="p-4 space-y-3">

      @csrf

      <label class="text-xs font-bold text-slate-500">
        Monto
      </label>

      <input type="text"
             name="monto"
             inputmode="decimal"
             oninput="this.value=this.value.replace(/[^0-9.]/g,'')"
             class="mt-1 w-full border border-slate-300 rounded-xl px-3 h-11 text-sm outline-none"
             required>

      <label class="block mt-3 text-xs font-bold text-slate-500">
        Observación
      </label>

      <input type="text"
             name="observacion"
             class="mt-1 w-full border border-slate-300 rounded-xl px-3 h-11 text-sm outline-none">

      <div class="flex justify-end gap-2 mt-5">

        <button type="button"
                @click="openModal=null"
                class="px-4 h-10 rounded-xl bg-white border border-slate-200 text-sm font-bold">
          Cancelar
        </button>
<button type="submit"
        style="background-color:#059669;color:white;"
        class="inline-flex items-center justify-center px-4 h-10 rounded-xl text-sm font-extrabold shadow-sm hover:opacity-90 transition">
  Guardar cobro
</button>

      </div>

    </form>

  </div>

</div>
@endsection
