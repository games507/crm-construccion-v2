@extends('layouts.base')
@section('title','Cuentas por cobrar')

@section('content')

@php
  $baseResumen = $cuentasResumen ?? $cuentas;

  $totalFacturado = (float) $baseResumen->sum('monto_total');
  $totalCobrado = (float) $baseResumen->sum('monto_cobrado');
  $saldoPendiente = (float) $baseResumen->sum('saldo');

  $cuentasVencidas = $baseResumen->filter(function($c){
      return $c->fecha_vencimiento
          && $c->fecha_vencimiento->lt(now()->startOfDay())
          && (float)$c->saldo > 0;
  });

  $totalVencido = (float)$cuentasVencidas->sum('saldo');
  $cantidadVencidas = $cuentasVencidas->count();

  $clientes = $baseResumen->pluck('cliente')->filter()->unique()->sort()->values();
  $proyectosFiltro = $baseResumen->pluck('proyecto.nombre')->filter()->unique()->sort()->values();

  $icon = function($name){
    if($name==='plus') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 5v14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='eye') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='money') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><rect x="3" y="6" width="18" height="12" rx="2" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='edit') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M4 20h4l10-10-4-4L4 16v4Z" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='alert') return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M12 9v4m0 4h.01M10.3 4.4 2.6 18a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 4.4a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    return '';
  };
@endphp

<style>
.vs-wrap{max-width:1450px;margin:0 auto;padding:18px}
.vs-head{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:18px}
.vs-title{font-size:30px;font-weight:950;color:#0f172a;line-height:1}
.vs-sub{margin-top:7px;font-size:13px;color:#64748b;font-weight:700}

.vs-btn{height:40px;border:none;border-radius:16px;padding:0 18px;display:inline-flex;align-items:center;justify-content:center;gap:10px;font-weight:900;text-decoration:none;cursor:pointer;transition:.2s ease}
.vs-btn:hover{transform:translateY(-2px)}
.vs-btn-primary{background:linear-gradient(135deg,#0f172a,#0b4f7d);color:white;box-shadow:0 12px 30px rgba(15,23,42,.15)}
.vs-btn-primary:hover{color:white}
.vs-btn-light{background:#f1f5f9;color:#334155;border:1px solid #e2e8f0}
.vs-btn-light:hover{background:#e2e8f0;color:#334155}

.vs-alert{border-radius:28px;border:1px solid #fecdd3;background:linear-gradient(135deg,#fff,#fff1f2);padding:16px;box-shadow:0 14px 40px rgba(15,23,42,.06)}
.vs-alert-row{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap}
.vs-alert-icon{height:44px;width:44px;border-radius:16px;display:flex;align-items:center;justify-content:center;background:#fff1f2;color:#e11d48;flex-shrink:0}

.vs-grid{display:grid;grid-template-columns:300px minmax(0,1fr);gap:18px;max-width:100%;overflow:hidden}
@media(max-width:1100px){.vs-grid{grid-template-columns:1fr}}

.kpi-grid{display:grid;grid-template-columns:1fr;gap:14px}
.kpi{background:white;border-radius:24px;border:1px solid #e2e8f0;padding:18px;box-shadow:0 14px 40px rgba(15,23,42,.06)}
.kpi-label{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#64748b;font-weight:950}
.kpi-value{margin-top:10px;font-size:26px;font-weight:950;color:#0f172a}
.green{color:#047857}.rose{color:#b91c1c}.amber{color:#b45309}

.vs-card{background:white;border-radius:28px;border:1px solid #e2e8f0;box-shadow:0 18px 50px rgba(15,23,42,.07);overflow:hidden;max-width:100%}
.vs-card-head{padding:18px;border-bottom:1px solid #e2e8f0}
.vs-card-title{font-weight:950;color:#0f172a;font-size:16px}
.vs-card-sub{font-size:12px;color:#64748b;margin-top:4px;font-weight:700}
.vs-card-body{padding:14px 18px}

.filter-grid{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
.input-vs,.select-vs{height:40px;border:1px solid #dbe2ea;border-radius:16px;padding:0 14px;font-weight:700;outline:none;background:white;color:#0f172a}
.input-vs{width:100%}
.input-vs:focus,.select-vs:focus{border-color:#38bdf8;box-shadow:0 0 0 4px rgba(14,165,233,.12)}

.vs-table-wrap{overflow:auto;max-height:520px;width:100%}
.vs-table-wrap::-webkit-scrollbar{width:8px;height:8px}
.vs-table-wrap::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:999px}
table{width:100%;min-width:1150px;border-collapse:collapse}
thead{background:#f8fafc;position:sticky;top:0;z-index:10}
th{padding:15px 14px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#64748b;font-weight:950;white-space:nowrap}
td{padding:14px;border-top:1px solid #edf2f7;vertical-align:middle;font-size:13px}
tr:hover{background:#fafcff}

.vs-badge{display:inline-flex;align-items:center;padding:7px 12px;border-radius:999px;font-size:11px;font-weight:950}
.vs-badge-ok{background:#dcfce7;color:#166534}
.vs-badge-warn{background:#fef3c7;color:#92400e}
.vs-badge-danger{background:#fee2e2;color:#991b1b}

.action-btn{height:32px;width:32px;border-radius:12px;border:1px solid #e2e8f0;background:white;display:inline-flex;align-items:center;justify-content:center;color:#64748b;transition:.2s ease}
.action-btn:hover{background:#f8fafc;transform:translateY(-1px)}

.modal-bg{position:fixed;inset:0;z-index:9999;background:rgba(15,23,42,.62);backdrop-filter:blur(6px);display:flex;align-items:center;justify-content:center;padding:16px}
.modal-card{background:white;border-radius:28px;width:min(100%,560px);box-shadow:0 25px 80px rgba(15,23,42,.28);overflow:hidden;max-height:92vh}
.modal-head{padding:20px;border-bottom:1px solid #e2e8f0;display:flex;align-items:flex-start;justify-content:space-between;gap:12px}
.modal-body{padding:20px;overflow:auto}
</style>

<div
  x-data="{
    openModal:null,
    cuenta:null,
    buscar:'',
    estado:'',
    cliente:'',
    proyecto:'',
    vencidas:false
  }"
  class="vs-wrap"
>

  <div class="vs-head">
    <div>
      <div class="vs-title">Cuentas por cobrar</div>
      <div class="vs-sub">Gestión de clientes, cobros y saldos pendientes.</div>
    </div>

    <button type="button" @click="openModal='crear'" class="vs-btn vs-btn-primary">
      {!! $icon('plus') !!} Nueva cuenta
    </button>
  </div>

  @if(session('ok'))
    <div class="mb-4 rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 font-black">
      {{ session('ok') }}
    </div>
  @endif

  @if($errors->any())
    <div class="mb-4 rounded-3xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 font-black">
      @foreach($errors->all() as $e)
        <div>{{ $e }}</div>
      @endforeach
    </div>
  @endif

  @if($cantidadVencidas > 0)
    <div class="mb-4 vs-alert">
      <div class="vs-alert-row">
        <div class="flex items-center gap-3">
          <div class="vs-alert-icon">
            {!! $icon('alert') !!}
          </div>

          <div>
            <div class="text-[11px] uppercase tracking-wide text-rose-600 font-black">
              Tienes {{ $cantidadVencidas }} cuenta{{ $cantidadVencidas > 1 ? 's' : '' }} vencida{{ $cantidadVencidas > 1 ? 's' : '' }}
            </div>

            <div class="text-sm text-rose-700 mt-1">
              Saldo vencido total:
              <strong>$ {{ number_format($totalVencido, 2, '.', ',') }}</strong>
            </div>
          </div>
        </div>

        <button type="button" @click="vencidas=true" class="vs-btn" style="background:#e11d48;color:white;">
          Ver vencidas
        </button>
      </div>
    </div>
  @endif

  <div class="vs-grid">

    <div class="space-y-4">
      <div class="kpi-grid">
        <div class="kpi">
          <div class="kpi-label">Total facturado</div>
          <div class="kpi-value">$ {{ number_format($totalFacturado,2) }}</div>
        </div>

        <div class="kpi">
          <div class="kpi-label">Total cobrado</div>
          <div class="kpi-value green">$ {{ number_format($totalCobrado,2) }}</div>
        </div>

        <div class="kpi">
          <div class="kpi-label">Saldo pendiente</div>
          <div class="kpi-value amber">$ {{ number_format($saldoPendiente,2) }}</div>
        </div>

        <div class="kpi">
          <div class="kpi-label">Vencidas</div>
          <div class="kpi-value rose">{{ $cantidadVencidas }}</div>
          <div class="mt-1 text-xs font-black text-rose-600">
            $ {{ number_format($totalVencido, 2, '.', ',') }}
          </div>

          <button type="button" @click="vencidas=true" class="mt-3 w-full vs-btn vs-btn-light">
            Ver vencidas
          </button>
        </div>
      </div>
    </div>

    <div class="space-y-4">

      <div class="vs-card">
        <div class="vs-card-body">
          <div class="filter-grid">
            <input x-model="buscar" type="text" placeholder="Buscar cliente o descripción" class="input-vs" style="width:260px;max-width:100%;">

            <select x-model="cliente" class="select-vs">
              <option value="">Cliente</option>
              @foreach($clientes as $cl)
                <option value="{{ $cl }}">{{ $cl }}</option>
              @endforeach
            </select>

            <select x-model="proyecto" class="select-vs">
              <option value="">Proyecto</option>
              @foreach($proyectosFiltro as $p)
                <option value="{{ $p }}">{{ $p }}</option>
              @endforeach
            </select>

            <select x-model="estado" class="select-vs">
              <option value="">Estado</option>
              <option value="pendiente">Pendiente</option>
              <option value="parcial">Parcial</option>
              <option value="cobrado">Cobrado</option>
            </select>

            <button type="button" @click="vencidas = !vencidas" class="vs-btn" :class="vencidas ? 'vs-btn-primary' : 'vs-btn-light'">
              Vencidas
            </button>

            <button type="button" @click="buscar=''; estado=''; cliente=''; proyecto=''; vencidas=false" class="vs-btn vs-btn-light">
              Limpiar
            </button>
          </div>
        </div>
      </div>

      <div class="vs-card">
        <div class="vs-card-head">
          <div>
            <div class="vs-card-title">Detalle de cuentas por cobrar</div>
            <div class="vs-card-sub">Listado de clientes, proyectos, saldos y estados.</div>
          </div>
        </div>

        <div class="vs-table-wrap">
          <table>
            <thead>
              <tr>
                <th>Cliente</th>
                <th>Proyecto</th>
                <th style="text-align:right">Total</th>
                <th style="text-align:right">Cobrado</th>
                <th style="text-align:right">Saldo</th>
                <th style="text-align:center">Estado</th>
                <th style="text-align:center">Acciones</th>
              </tr>
            </thead>

            <tbody>
              @forelse($cuentas as $c)
                @php
                  $estadoCuenta = (string)($c->estado ?? 'pendiente');
                  $proyectoNombre = $c->proyecto->nombre ?? '-';
                  $textoBusqueda = strtolower(($c->cliente ?? '') . ' ' . ($c->descripcion ?? ''));
                  $vencida = $c->fecha_vencimiento
                    && $c->fecha_vencimiento->lt(now()->startOfDay())
                    && (float)$c->saldo > 0;

                  $badge = match($estadoCuenta){
                      'cobrado' => 'vs-badge-ok',
                      'parcial' => 'vs-badge-warn',
                      default => 'vs-badge-danger',
                  };
                @endphp

                <tr
                  class="{{ $vencida ? 'bg-rose-50/80' : '' }}"
                  x-show="
                    (!buscar || {{ Js::from($textoBusqueda) }}.includes(buscar.toLowerCase()))
                    && (!cliente || cliente === {{ Js::from($c->cliente) }})
                    && (!proyecto || proyecto === {{ Js::from($proyectoNombre) }})
                    && (!estado || estado === {{ Js::from($estadoCuenta) }})
                    && (!vencidas || {{ $vencida ? 'true' : 'false' }})
                  "
                >
                  <td>
                    <div class="font-black text-slate-900">{{ $c->cliente }}</div>
                    <div class="text-xs text-slate-500 mt-1 max-w-[260px] truncate">{{ $c->descripcion }}</div>
                  </td>

                  <td class="font-bold text-slate-600">{{ $proyectoNombre }}</td>

                  <td style="text-align:right" class="font-black text-slate-800">
                    $ {{ number_format($c->monto_total,2) }}
                  </td>

                  <td style="text-align:right" class="font-black text-emerald-700">
                    $ {{ number_format($c->monto_cobrado,2) }}
                  </td>

                  <td style="text-align:right" class="font-black text-rose-700">
                    $ {{ number_format($c->saldo,2) }}
                  </td>

                  <td style="text-align:center">
                    <span class="vs-badge {{ $badge }}">
                      {{ ucfirst($estadoCuenta) }}
                    </span>
                  </td>

                  <td>
                    <div class="flex justify-center gap-2">
                      <button type="button" title="Detalle"
                        @click="openModal='detalle'; cuenta={{ Js::from($c) }}"
                        class="action-btn hover:text-indigo-600">
                        {!! $icon('eye') !!}
                      </button>

                      @if((float)$c->saldo > 0)
                        <button type="button" title="Registrar cobro"
                          @click="openModal='cobrar'; cuenta={{ Js::from($c) }}"
                          class="action-btn hover:text-emerald-600">
                          {!! $icon('money') !!}
                        </button>
                      @endif

                      <button type="button" title="Editar"
                        @click="openModal='editar'; cuenta={{ Js::from($c) }}"
                        class="action-btn hover:text-blue-600">
                        {!! $icon('edit') !!}
                      </button>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center py-14 text-slate-500 font-bold">
                    No hay cuentas por cobrar registradas.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if(method_exists($cuentas, 'links'))
          <div class="px-5 py-4 border-t border-slate-200 bg-white">
            {{ $cuentas->links() }}
          </div>
        @endif
      </div>

    </div>
  </div>

  {{-- MODAL DETALLE --}}
  <div x-show="openModal === 'detalle'" x-cloak x-transition.opacity class="modal-bg" style="display:none;">
    <div @click.outside="openModal=null" class="modal-card">
      <div class="modal-head">
        <div>
          <h2 class="text-lg font-black text-slate-900">Detalle de cobros</h2>
          <p class="text-sm text-slate-500 font-bold" x-text="cuenta?.cliente"></p>
        </div>
        <button type="button" @click="openModal=null" class="text-slate-400 hover:text-slate-700">✕</button>
      </div>

      <template x-if="cuenta">
        <div class="modal-body">
          <div class="grid grid-cols-3 gap-3 mb-4">
            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-3">
              <div class="text-xs text-slate-500 font-bold">Total</div>
              <div class="font-black text-slate-900">$<span x-text="Number(cuenta.monto_total || 0).toFixed(2)"></span></div>
            </div>

            <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-3">
              <div class="text-xs text-emerald-700 font-bold">Cobrado</div>
              <div class="font-black text-emerald-800">$<span x-text="Number(cuenta.monto_cobrado || 0).toFixed(2)"></span></div>
            </div>

            <div class="rounded-2xl bg-rose-50 border border-rose-200 p-3">
              <div class="text-xs text-rose-700 font-bold">Saldo</div>
              <div class="font-black text-rose-800">$<span x-text="Number(cuenta.saldo || 0).toFixed(2)"></span></div>
            </div>
          </div>

          <div class="rounded-2xl border border-slate-200 overflow-hidden">
            <div class="px-3 py-2 bg-slate-50 text-xs font-black text-slate-500 uppercase">Historial</div>

            <template x-if="cuenta.cobros && cuenta.cobros.length">
              <div class="divide-y divide-slate-100">
                <template x-for="p in cuenta.cobros" :key="p.id">
                  <div class="px-3 py-3 flex items-start justify-between gap-3">
                    <div>
                      <div class="font-black text-slate-800">$<span x-text="Number(p.monto || 0).toFixed(2)"></span></div>
                      <div class="text-xs text-slate-500" x-text="p.observacion || 'Cobro registrado'"></div>
                    </div>
                    <div class="text-xs font-bold text-slate-500" x-text="p.fecha || '—'"></div>
                  </div>
                </template>
              </div>
            </template>

            <template x-if="!cuenta.cobros || !cuenta.cobros.length">
              <div class="px-3 py-6 text-center text-sm text-slate-500">No hay cobros registrados.</div>
            </template>
          </div>

          <div class="text-right mt-5">
            <button type="button" @click="openModal=null" class="vs-btn vs-btn-primary">Cerrar</button>
          </div>
        </div>
      </template>
    </div>
  </div>

  {{-- MODAL CREAR --}}
  <div x-show="openModal === 'crear'" x-cloak x-transition.opacity class="modal-bg" style="display:none;">
    <div @click.outside="openModal=null" class="modal-card">
      <div class="modal-head">
        <div>
          <h2 class="text-lg font-black text-slate-900">Nueva cuenta por cobrar</h2>
          <p class="text-sm text-slate-500">Registra una cuenta pendiente de cobro</p>
        </div>
        <button type="button" @click="openModal=null" class="text-slate-400 hover:text-slate-700">✕</button>
      </div>

      <div class="modal-body">
        <form method="POST" action="{{ route('admin.cobros.store') }}" class="space-y-3">
          @csrf

          <div>
            <label class="text-xs font-black text-slate-500 uppercase">Proyecto</label>
            <select name="proyecto_id" class="input-vs mt-1" required>
              <option value="">Selecciona un proyecto</option>
              @foreach($proyectos as $p)
                <option value="{{ $p->id }}">{{ $p->nombre }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="text-xs font-black text-slate-500 uppercase">Cliente</label>
            <input type="text" name="cliente" class="input-vs mt-1" required>
          </div>

          <div>
            <label class="text-xs font-black text-slate-500 uppercase">Descripción</label>
            <input type="text" name="descripcion" class="input-vs mt-1">
          </div>

          <div>
            <label class="text-xs font-black text-slate-500 uppercase">Monto total</label>
            <input type="text" name="monto_total" inputmode="decimal" oninput="this.value=this.value.replace(/[^0-9.]/g,'')" class="input-vs mt-1" required>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="text-xs font-black text-slate-500 uppercase">Fecha</label>
              <input type="date" name="fecha" class="input-vs mt-1">
            </div>

            <div>
              <label class="text-xs font-black text-slate-500 uppercase">Vencimiento</label>
              <input type="date" name="fecha_vencimiento" class="input-vs mt-1">
            </div>
          </div>

          <div class="flex justify-end gap-2 pt-3">
            <button type="button" @click="openModal=null" class="vs-btn vs-btn-light">Cancelar</button>
            <button type="submit" class="vs-btn vs-btn-primary">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- MODAL COBRAR --}}
  <div x-show="openModal === 'cobrar'" x-cloak x-transition.opacity class="modal-bg" style="display:none;">
    <div @click.outside="openModal=null" class="modal-card">
      <div class="modal-head">
        <div>
          <h2 class="text-lg font-black text-slate-900">Registrar cobro</h2>
          <p class="text-sm text-slate-500" x-text="cuenta?.cliente"></p>
        </div>
        <button type="button" @click="openModal=null" class="text-slate-400 hover:text-slate-700">✕</button>
      </div>

      <div class="modal-body">
        <form method="POST" :action="'{{ url('/app/admin/cobros') }}/' + cuenta.id + '/cobrar'" class="space-y-3">
          @csrf

          <div>
            <label class="text-xs font-black text-slate-500 uppercase">Monto</label>
            <input type="text" name="monto" inputmode="decimal" oninput="this.value=this.value.replace(/[^0-9.]/g,'')" class="input-vs mt-1" required>
          </div>

          <div>
            <label class="text-xs font-black text-slate-500 uppercase">Observación</label>
            <input type="text" name="observacion" class="input-vs mt-1">
          </div>

          <div class="flex justify-end gap-2 pt-3">
            <button type="button" @click="openModal=null" class="vs-btn vs-btn-light">Cancelar</button>
            <button type="submit" class="vs-btn vs-btn-primary">Guardar cobro</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- MODAL EDITAR --}}
  <div x-show="openModal === 'editar'" x-cloak x-transition.opacity class="modal-bg" style="display:none;">
    <div @click.outside="openModal=null" class="modal-card">
      <div class="modal-head">
        <div>
          <h2 class="text-lg font-black text-slate-900">Editar cuenta</h2>
          <p class="text-sm text-slate-500" x-text="cuenta?.cliente"></p>
        </div>
        <button type="button" @click="openModal=null" class="text-slate-400 hover:text-slate-700">✕</button>
      </div>

      <div class="modal-body">
        <form method="POST" :action="'{{ url('/app/admin/cobros') }}/' + cuenta.id" class="space-y-3">
          @csrf
          @method('PUT')

          <div>
            <label class="text-xs font-black text-slate-500 uppercase">Cliente</label>
            <input type="text" name="cliente" x-model="cuenta.cliente" class="input-vs mt-1" required>
          </div>

          <div>
            <label class="text-xs font-black text-slate-500 uppercase">Descripción</label>
            <input type="text" name="descripcion" x-model="cuenta.descripcion" class="input-vs mt-1">
          </div>

          <div>
            <label class="text-xs font-black text-slate-500 uppercase">Fecha</label>
            <input type="date" name="fecha" x-model="cuenta.fecha" class="input-vs mt-1">
          </div>

          <div>
            <label class="text-xs font-black text-slate-500 uppercase">Fecha vencimiento</label>
            <input type="date" name="fecha_vencimiento" x-model="cuenta.fecha_vencimiento" class="input-vs mt-1">
          </div>

          <div class="flex justify-end gap-2 pt-3">
            <button type="button" @click="openModal=null" class="vs-btn vs-btn-light">Cancelar</button>
            <button type="submit" class="vs-btn vs-btn-primary">Guardar cambios</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>

@endsection