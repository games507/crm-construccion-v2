@extends('layouts.base')
@section('title','Cuentas por pagar')

@section('content')
@php
  $totalGenerado = (float) $cuentas->sum('monto_total');
  $totalPagado = (float) $cuentas->sum('monto_pagado');
  $saldoPendiente = (float) $cuentas->sum('saldo');

  $cuentasVencidas = $cuentas->filter(function($c) {
    return $c->fecha_vencimiento
      && $c->fecha_vencimiento->lt(now()->startOfDay())
      && (float) $c->saldo > 0;
  });

  $cuentasPorVencer = $cuentas->filter(function($c) {
    return $c->fecha_vencimiento
      && $c->fecha_vencimiento->gte(now()->startOfDay())
      && $c->fecha_vencimiento->lte(now()->addDays(7)->endOfDay())
      && (float) $c->saldo > 0;
  });

  $totalVencido = (float) $cuentasVencidas->sum('saldo');
  $cantidadVencidas = $cuentasVencidas->count();
  $cantidadPorVencer = $cuentasPorVencer->count();

  $porcentajePagado = $totalGenerado > 0
    ? round(($totalPagado / $totalGenerado) * 100, 2)
    : 0;

  $proveedores = $cuentas->pluck('proveedor')->filter()->unique()->sort()->values();
  $proyectos = $cuentas->pluck('proyecto.nombre')->filter()->unique()->sort()->values();
@endphp

<div
  x-data="{
    openModal:null,
    cuenta:null,
    buscar:'',
    estado:'',
    proveedor:'',
    proyecto:'',
    vencidas:false
  }"
  class="space-y-6"
>

  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">Cuentas por pagar</h1>
      <p class="text-sm text-slate-500 mt-1">Dashboard financiero y control de deudas por proveedor</p>
    </div>

    <div class="flex flex-wrap gap-2">
      <a href="{{ route('admin.cuentas.exportar') }}"
         class="inline-flex items-center gap-2 px-4 h-10 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 shadow">
        Exportar Excel
      </a>

      <a href="{{ route('admin.cuentas.reporte.proveedores') }}"
         class="inline-flex items-center gap-2 px-4 h-10 rounded-xl bg-slate-900 text-white text-sm font-bold hover:bg-slate-800 shadow">
        Reporte proveedores
      </a>

      <a href="{{ route('admin.cuentas.flujo') }}"
         class="inline-flex items-center gap-2 px-4 h-10 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700">
        Flujo de caja
      </a>
    </div>
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

  {{-- ALERTAS --}}
  @if($cantidadVencidas > 0)
    <div class="rounded-3xl border border-rose-200 bg-rose-50 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-3">
      <div>
        <div class="font-black text-rose-800">
          ⚠️ Tienes {{ $cantidadVencidas }} cuenta{{ $cantidadVencidas > 1 ? 's' : '' }} vencida{{ $cantidadVencidas > 1 ? 's' : '' }}
        </div>
        <div class="text-sm text-rose-700 mt-1">
          Saldo vencido total: <strong>$ {{ number_format($totalVencido, 2, '.', ',') }}</strong>
        </div>
      </div>

      <button type="button"
              @click="vencidas=true"
              class="inline-flex items-center justify-center h-10 px-4 rounded-xl bg-rose-600 text-white text-sm font-extrabold hover:bg-rose-700 shadow-sm">
        Ver vencidas
      </button>
    </div>
  @endif

  @if($cantidadPorVencer > 0)
    <div class="rounded-3xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
      <div class="font-black text-amber-800">
        ⏳ {{ $cantidadPorVencer }} cuenta{{ $cantidadPorVencer > 1 ? 's' : '' }} por vencer en los próximos 7 días
      </div>
      <div class="text-sm text-amber-700 mt-1">
        Revisa los vencimientos para evitar atrasos.
      </div>
    </div>
  @endif

  {{-- DASHBOARD FINANCIERO --}}
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
      <div class="text-xs uppercase tracking-wide text-slate-500 font-black">Total generado</div>
      <div class="mt-2 text-2xl font-black text-slate-900">
        $ {{ number_format($totalGenerado, 2, '.', ',') }}
      </div>
      <div class="mt-3 h-2 rounded-full bg-slate-100 overflow-hidden">
        <div class="h-2 rounded-full bg-slate-700" style="width:100%"></div>
      </div>
    </div>

    <div class="rounded-3xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
      <div class="text-xs uppercase tracking-wide text-emerald-700 font-black">Total pagado</div>
      <div class="mt-2 text-2xl font-black text-emerald-800">
        $ {{ number_format($totalPagado, 2, '.', ',') }}
      </div>
      <div class="mt-3 h-2 rounded-full bg-emerald-100 overflow-hidden">
        <div class="h-2 rounded-full bg-emerald-600" style="width: {{ min($porcentajePagado, 100) }}%"></div>
      </div>
      <div class="mt-2 text-xs font-bold text-emerald-700">{{ $porcentajePagado }}% pagado</div>
    </div>

    <div class="rounded-3xl border border-rose-200 bg-rose-50 p-5 shadow-sm">
      <div class="text-xs uppercase tracking-wide text-rose-700 font-black">Saldo pendiente</div>
      <div class="mt-2 text-2xl font-black text-rose-800">
        $ {{ number_format($saldoPendiente, 2, '.', ',') }}
      </div>
      <div class="mt-2 text-xs font-bold text-rose-700">Por pagar</div>
    </div>

    <div class="rounded-3xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
      <div class="text-xs uppercase tracking-wide text-amber-700 font-black">Saldo vencido</div>
      <div class="mt-2 text-2xl font-black text-amber-800">
        $ {{ number_format($totalVencido, 2, '.', ',') }}
      </div>
      <div class="mt-2 text-xs font-bold text-amber-700">
        {{ $cantidadVencidas }} cuenta{{ $cantidadVencidas !== 1 ? 's' : '' }} vencida{{ $cantidadVencidas !== 1 ? 's' : '' }}
      </div>
    </div>

  </div>

  {{-- FILTROS --}}
  <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">

      <input
        x-model="buscar"
        type="text"
        placeholder="Buscar proveedor o descripción"
        class="h-11 rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
      >

      <select x-model="proveedor" class="h-11 rounded-xl border border-slate-300 px-3 text-sm bg-white">
        <option value="">Todos los proveedores</option>
        @foreach($proveedores as $p)
          <option value="{{ $p }}">{{ $p }}</option>
        @endforeach
      </select>

      <select x-model="proyecto" class="h-11 rounded-xl border border-slate-300 px-3 text-sm bg-white">
        <option value="">Todos los proyectos</option>
        @foreach($proyectos as $p)
          <option value="{{ $p }}">{{ $p }}</option>
        @endforeach
      </select>

      <select x-model="estado" class="h-11 rounded-xl border border-slate-300 px-3 text-sm bg-white">
        <option value="">Todos los estados</option>
        <option value="pendiente">Pendiente</option>
        <option value="parcial">Parcial</option>
        <option value="pagado">Pagado</option>
      </select>

      <button
        type="button"
        @click="vencidas = !vencidas"
        :class="vencidas ? 'bg-rose-600 text-white' : 'bg-white text-slate-700 border border-slate-300'"
        class="h-11 rounded-xl px-3 text-sm font-bold shadow-sm"
      >
        Solo vencidas
      </button>

    </div>

    <div class="mt-3 flex justify-end">
      <button
        type="button"
        @click="buscar=''; estado=''; proveedor=''; proyecto=''; vencidas=false"
        class="h-9 px-3 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200"
      >
        Limpiar filtros
      </button>
    </div>
  </div>

  {{-- TABLA --}}
  <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="px-4 py-3 text-left">Proveedor</th>
            <th class="px-4 py-3 text-left">Proyecto</th>
            <th class="px-4 py-3 text-right">Total</th>
            <th class="px-4 py-3 text-right">Pagado</th>
            <th class="px-4 py-3 text-right">Saldo</th>
            <th class="px-4 py-3 text-center">Vencimiento</th>
            <th class="px-4 py-3 text-center">Estado</th>
            <th class="px-4 py-3 text-center">Acciones</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
          @forelse($cuentas as $c)
            @php
              $estadoCuenta = (string)($c->estado ?? 'pendiente');
              $proyectoNombre = $c->proyecto->nombre ?? '-';
              $textoBusqueda = strtolower(($c->proveedor ?? '') . ' ' . ($c->descripcion ?? ''));

              $vencida = $c->fecha_vencimiento
                && $c->fecha_vencimiento->lt(now()->startOfDay())
                && (float)$c->saldo > 0;

              $porVencer = $c->fecha_vencimiento
                && $c->fecha_vencimiento->gte(now()->startOfDay())
                && $c->fecha_vencimiento->lte(now()->addDays(7)->endOfDay())
                && (float)$c->saldo > 0;

              $badge = match($estadoCuenta) {
                'pagado' => 'bg-emerald-100 text-emerald-700',
                'parcial' => 'bg-amber-100 text-amber-700',
                default => 'bg-rose-100 text-rose-700',
              };
            @endphp

            <tr
              class="{{ $vencida ? 'bg-rose-50/80 border-l-4 border-rose-500' : ($porVencer ? 'bg-amber-50/60 border-l-4 border-amber-400' : 'hover:bg-slate-50') }} transition"
              x-show="
                (!buscar || {{ Js::from($textoBusqueda) }}.includes(buscar.toLowerCase()))
                && (!proveedor || proveedor === {{ Js::from($c->proveedor) }})
                && (!proyecto || proyecto === {{ Js::from($proyectoNombre) }})
                && (!estado || estado === {{ Js::from($estadoCuenta) }})
                && (!vencidas || {{ $vencida ? 'true' : 'false' }})
              "
            >
              <td class="px-4 py-4">
                <div class="font-black text-slate-900">{{ $c->proveedor }}</div>
                @if(!empty($c->descripcion))
                  <div class="text-xs text-slate-500 mt-1 max-w-[260px] truncate">{{ $c->descripcion }}</div>
                @endif
              </td>

              <td class="px-4 py-4 text-slate-600">{{ $proyectoNombre }}</td>

              <td class="px-4 py-4 text-right font-semibold">
                $ {{ number_format((float)$c->monto_total, 2, '.', ',') }}
              </td>

              <td class="px-4 py-4 text-right text-emerald-700 font-bold">
                $ {{ number_format((float)$c->monto_pagado, 2, '.', ',') }}
              </td>

              <td class="px-4 py-4 text-right text-rose-700 font-black">
                $ {{ number_format((float)$c->saldo, 2, '.', ',') }}
              </td>

              <td class="px-4 py-4 text-center">
                <div class="font-bold text-slate-700">
                  {{ $c->fecha_vencimiento?->format('Y-m-d') ?? '—' }}
                </div>

                @if($vencida)
                  <div class="mt-1 inline-flex px-2 py-0.5 rounded-full bg-rose-600 text-white text-[11px] font-bold">
                    Vencida
                  </div>
                @elseif($porVencer)
                  <div class="mt-1 inline-flex px-2 py-0.5 rounded-full bg-amber-500 text-white text-[11px] font-bold">
                    Por vencer
                  </div>
                @endif
              </td>

              <td class="px-4 py-4 text-center">
                <span class="px-2 py-1 rounded-full text-xs font-black {{ $badge }}">
                  {{ ucfirst($estadoCuenta) }}
                </span>
              </td>

              <td class="px-4 py-4">
                <div class="flex justify-center gap-3">

                  <button type="button" title="Detalle"
                    @click="openModal='detalle'; cuenta={{ Js::from($c) }}"
                    class="text-slate-500 hover:text-indigo-600 transition">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2"/>
                      <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                    </svg>
                  </button>

                  @if((float)$c->saldo > 0)
                    <button type="button" title="Registrar pago"
                      @click="openModal='pagar'; cuenta={{ Js::from($c) }}"
                      class="text-slate-500 hover:text-emerald-600 transition">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="6" width="18" height="12" rx="2" stroke="currentColor" stroke-width="2"/>
                        <circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="2"/>
                      </svg>
                    </button>
                  @endif

                  <button type="button" title="Editar"
                    @click="openModal='editar'; cuenta={{ Js::from($c) }}"
                    class="text-slate-500 hover:text-blue-600 transition">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                      <path d="M4 20h4l10-10-4-4L4 16v4z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                  </button>

                  <button type="button" title="Eliminar"
                    @click="openModal='eliminar'; cuenta={{ Js::from($c) }}"
                    class="text-slate-400 hover:text-rose-600 transition">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                      <path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14" stroke="currentColor" stroke-width="2"/>
                    </svg>
                  </button>

                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center py-8 text-slate-500">
                No hay cuentas por pagar registradas.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- MODAL PAGAR --}}
  <div x-show="openModal === 'pagar'"
       x-cloak
       class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">

    <div @click.outside="openModal=null"
         class="bg-white rounded-2xl w-full max-w-md shadow-2xl max-h-[90vh] flex flex-col overflow-hidden">

      <div class="flex items-start justify-between gap-3 p-6 border-b">
        <div>
          <h2 class="text-lg font-extrabold text-slate-900">Registrar pago</h2>
          <p class="text-sm text-slate-500" x-text="cuenta?.proveedor"></p>
        </div>

        <button type="button" @click="openModal=null" class="text-slate-400 hover:text-slate-700">
          ✕
        </button>
      </div>

      <div class="p-6 overflow-y-auto flex-1">
        <form method="POST" :action="'{{ url('/app/admin/cuentas') }}/' + cuenta.id + '/pagar'">
          @csrf

          <label class="text-xs font-bold text-slate-500">Monto</label>
          <input type="text"
                 name="monto"
                 inputmode="decimal"
                 class="mt-1 w-full border border-slate-300 rounded-xl px-3 h-11 text-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 outline-none"
                 placeholder="Monto a pagar"
                 oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                 autocomplete="off"
                 required>

          <label class="block mt-3 text-xs font-bold text-slate-500">Fecha</label>
          <input type="date" name="fecha"
                 class="mt-1 w-full border border-slate-300 rounded-xl px-3 h-11 text-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 outline-none">

          <label class="block mt-3 text-xs font-bold text-slate-500">Observación</label>
          <input type="text" name="observacion"
                 class="mt-1 w-full border border-slate-300 rounded-xl px-3 h-11 text-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 outline-none"
                 placeholder="Ej: Abono inicial">

          <div class="flex items-center justify-end gap-2 mt-6">
            <button type="button"
                    @click="openModal=null"
                    class="px-4 h-10 rounded-xl bg-white border border-slate-200 text-sm font-bold text-slate-700 hover:bg-slate-50">
              Cancelar
            </button>

            <button type="submit"
                    class="px-4 h-10 rounded-xl text-white text-sm font-extrabold shadow-md"
                    style="background-color:#059669;"
                    onmouseover="this.style.backgroundColor='#047857'"
                    onmouseout="this.style.backgroundColor='#059669'">
              Guardar pago
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>

  {{-- MODAL DETALLE --}}
  <div x-show="openModal === 'detalle'"
       x-cloak
       class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">

    <div @click.outside="openModal=null"
         class="bg-white rounded-2xl p-6 w-full max-w-lg shadow-2xl">

      <div class="flex items-start justify-between gap-3 mb-4">
        <div>
          <h2 class="text-lg font-extrabold text-slate-900">Detalle de pagos</h2>
          <p class="text-sm text-slate-500" x-text="cuenta?.proveedor"></p>
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
              <div class="text-xs text-emerald-700 font-bold">Pagado</div>
              <div class="font-extrabold text-emerald-800">$<span x-text="Number(cuenta.monto_pagado || 0).toFixed(2)"></span></div>
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

            <template x-if="cuenta.pagos && cuenta.pagos.length">
              <div class="divide-y divide-slate-100">
                <template x-for="p in cuenta.pagos" :key="p.id">
                  <div class="px-3 py-3 flex items-start justify-between gap-3">
                    <div>
                      <div class="font-bold text-slate-800">
                        $<span x-text="Number(p.monto || 0).toFixed(2)"></span>
                      </div>
                      <div class="text-xs text-slate-500" x-text="p.observacion || 'Pago registrado'"></div>
                    </div>
                    <div class="text-xs font-bold text-slate-500" x-text="p.fecha || '—'"></div>
                  </div>
                </template>
              </div>
            </template>

            <template x-if="!cuenta.pagos || !cuenta.pagos.length">
              <div class="px-3 py-6 text-center text-sm text-slate-500">
                No hay pagos registrados.
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

  {{-- MODAL EDITAR --}}
  <div x-show="openModal === 'editar'"
       x-cloak
       class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">

    <div @click.outside="openModal=null"
         class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl">

      <div class="flex items-start justify-between gap-3 mb-4">
        <div>
          <h2 class="text-lg font-extrabold text-slate-900">Editar cuenta</h2>
          <p class="text-sm text-slate-500" x-text="cuenta?.proveedor"></p>
        </div>

        <button type="button" @click="openModal=null" class="text-slate-400 hover:text-slate-700">
          ✕
        </button>
      </div>

      <form method="POST" :action="'{{ url('/app/admin/cuentas') }}/' + cuenta.id">
        @csrf
        @method('PUT')

        <label class="text-xs font-bold text-slate-500">Proveedor</label>
        <input type="text" name="proveedor" x-model="cuenta.proveedor"
               class="mt-1 w-full border border-slate-300 rounded-xl px-3 h-11 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none"
               required>

        <label class="block mt-3 text-xs font-bold text-slate-500">Descripción</label>
        <input type="text" name="descripcion" x-model="cuenta.descripcion"
               class="mt-1 w-full border border-slate-300 rounded-xl px-3 h-11 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none">

        <label class="block mt-3 text-xs font-bold text-slate-500">Fecha</label>
        <input type="date" name="fecha" x-model="cuenta.fecha"
               class="mt-1 w-full border border-slate-300 rounded-xl px-3 h-11 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none">

        <label class="block mt-3 text-xs font-bold text-slate-500">Fecha vencimiento</label>
        <input type="date" name="fecha_vencimiento" x-model="cuenta.fecha_vencimiento"
               class="mt-1 w-full border border-slate-300 rounded-xl px-3 h-11 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none">

        <div class="flex justify-end gap-2 mt-5">
          <button type="button"
                  @click="openModal=null"
                  class="px-4 h-10 rounded-xl bg-white border border-slate-200 text-sm font-bold hover:bg-slate-50">
            Cancelar
          </button>

          <button type="submit"
                  class="px-4 h-10 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700">
            Guardar cambios
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- MODAL ELIMINAR --}}
  <div x-show="openModal === 'eliminar'"
       x-cloak
       class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">

    <div @click.outside="openModal=null"
         class="bg-white rounded-2xl w-full max-w-md shadow-2xl">

      <div class="p-5 border-b">
        <h2 class="text-lg font-extrabold text-slate-900">
          Eliminar cuenta
        </h2>
        <p class="text-sm text-slate-500 mt-1">
          Esta acción no se puede deshacer.
        </p>
      </div>

      <div class="p-5 text-sm text-slate-600">
        ¿Seguro que deseas eliminar la cuenta de:
        <span class="font-bold text-slate-900" x-text="cuenta?.proveedor"></span>?
      </div>

      <div class="p-5 flex justify-end gap-2 border-t">

        <button type="button"
                @click="openModal=null"
                class="px-4 h-10 rounded-xl bg-white border border-slate-200 text-sm font-bold hover:bg-slate-50">
          Cancelar
        </button>

        <form method="POST" :action="'{{ url('/app/admin/cuentas') }}/' + cuenta.id">
          @csrf
          @method('DELETE')

          <button type="submit"
                  class="px-4 h-10 rounded-xl bg-rose-600 text-white text-sm font-extrabold hover:bg-rose-700 shadow-md">
            Eliminar
          </button>
        </form>

      </div>

    </div>
  </div>

</div>
@endsection