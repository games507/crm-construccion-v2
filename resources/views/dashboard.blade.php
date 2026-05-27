@extends('layouts.base')
@section('title','Dashboard Ejecutivo')

@section('content')
@php
  use App\Support\EmpresaScope;
  use App\Models\Empresa;

  $k = $kpis ?? [];
  $user = auth()->user();

  $isSuperAdmin = false;
  if ($user) {
    if (method_exists($user, 'hasRole')) {
      $isSuperAdmin = $user->hasRole('SuperAdmin') || $user->hasRole('Super Admin');
    } elseif (isset($user->is_superadmin)) {
      $isSuperAdmin = (bool) $user->is_superadmin;
    }
  }

  $empresaId = $isSuperAdmin ? EmpresaScope::getId() : ($user->empresa_id ?? null);
  $empresaObj = $empresaId ? Empresa::select('id','nombre')->find($empresaId) : null;
  $empresaNombre = $empresaObj?->nombre ?? ($user?->empresa?->nombre ?? 'Sin empresa seleccionada');

  $money = fn($v) => '$' . number_format((float)$v, 2);
  $num = fn($v) => number_format((float)$v, 0);

  $avancePromedio = max(0, min(100, (float)($k['avance_promedio'] ?? 0)));
  $valorInventario = (float)($k['valor_inventario'] ?? 0);
  $presupuestoTotal = (float)($k['presupuesto_total'] ?? 0);
  $costosTotal = (float)($k['costos_total'] ?? 0);
  $saldoPagar = (float)($k['cuentas_por_pagar_saldo'] ?? 0);
  $saldoCobrar = (float)($k['cuentas_por_cobrar_saldo'] ?? 0);

  $proyectosRecientes = $proyectosRecientes ?? collect();
  $ultimosMovs = $ultimosMovs ?? collect();
  $topMateriales = $topMateriales ?? collect();
  $equipoActivo = $equipoActivo ?? collect();

  $icon = function($name) {
    $attrs = 'xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';

    return match($name) {
      'building' => '<svg '.$attrs.'><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/><path d="M9 9h1"/><path d="M9 13h1"/><path d="M9 17h1"/></svg>',
      'trend' => '<svg '.$attrs.'><path d="M3 3v18h18"/><path d="m7 14 4-4 3 3 5-7"/><path d="M15 6h4v4"/></svg>',
      'box' => '<svg '.$attrs.'><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>',
      'clock' => '<svg '.$attrs.'><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>',
      'wallet' => '<svg '.$attrs.'><path d="M20 12V8H6a2 2 0 0 1 0-4h12v4"/><path d="M4 6v14a2 2 0 0 0 2 2h14v-6"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/></svg>',
      'receipt' => '<svg '.$attrs.'><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2Z"/><path d="M8 7h8"/><path d="M8 12h8"/><path d="M8 17h5"/></svg>',
      'credit' => '<svg '.$attrs.'><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M6 15h2"/><path d="M10 15h4"/></svg>',
      'coin' => '<svg '.$attrs.'><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/><path d="M12 8v8"/></svg>',
      default => '<svg '.$attrs.'><circle cx="12" cy="12" r="10"/></svg>',
    };
  };
@endphp

<style>
  .vs-dash{max-width:1420px;margin:0 auto;padding:18px}
  .vs-hero{position:relative;overflow:hidden;border-radius:30px;padding:24px;background:radial-gradient(circle at 15% 15%, rgba(34,211,238,.30), transparent 32%),radial-gradient(circle at 88% 20%, rgba(59,130,246,.28), transparent 34%),linear-gradient(135deg,#07172e 0%,#0b2f54 52%,#061425 100%);color:white;box-shadow:0 24px 70px rgba(2,6,23,.22)}
  .vs-hero:before{content:"";position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.06) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.06) 1px,transparent 1px);background-size:38px 38px;opacity:.22}
  .vs-hero-inner{position:relative;z-index:1;display:flex;justify-content:space-between;gap:18px;flex-wrap:wrap}
  .vs-eyebrow{font-size:11px;font-weight:900;letter-spacing:.18em;text-transform:uppercase;color:#93c5fd}
  .vs-title{font-size:28px;line-height:1.1;font-weight:950;margin-top:8px}
  .vs-sub{margin-top:8px;color:#cbd5e1;font-size:14px;max-width:720px}
  .vs-actions{display:flex;gap:10px;flex-wrap:wrap;align-items:flex-start}
  .vs-btn{height:42px;padding:0 14px;border-radius:15px;display:inline-flex;align-items:center;gap:9px;text-decoration:none;font-size:13px;font-weight:900;border:1px solid rgba(255,255,255,.20);color:#fff;background:rgba(255,255,255,.10);backdrop-filter:blur(10px)}
  .vs-btn:hover{background:rgba(255,255,255,.16);color:#fff}

  .vs-grid{display:grid;grid-template-columns:repeat(12,1fr);gap:16px;margin-top:16px}
  .s3{grid-column:span 3}.s5{grid-column:span 5}.s6{grid-column:span 6}.s7{grid-column:span 7}.s12{grid-column:span 12}
  @media(max-width:1100px){.s3{grid-column:span 6}.s5,.s6,.s7,.s12{grid-column:span 12}}
  @media(max-width:620px){.vs-dash{padding:12px}.s3{grid-column:span 12}.vs-title{font-size:23px}}

  .kpi{border-radius:24px;background:rgba(255,255,255,.94);border:1px solid rgba(226,232,240,.9);box-shadow:0 18px 45px rgba(15,23,42,.08);padding:18px;overflow:hidden;transition:.22s ease}
  .kpi:hover{transform:translateY(-3px);box-shadow:0 24px 55px rgba(15,23,42,.12)}
  .kpi-top{display:flex;align-items:center;justify-content:space-between;gap:12px}
  .ico{width:46px;height:46px;border-radius:18px;display:flex;align-items:center;justify-content:center;box-shadow:inset 0 0 0 1px rgba(255,255,255,.55)}
  .i1{background:#dbeafe;color:#1d4ed8}.i2{background:#dcfce7;color:#047857}.i3{background:#fef3c7;color:#b45309}.i4{background:#ede9fe;color:#6d28d9}
  .i5{background:#fee2e2;color:#dc2626}.i6{background:#cffafe;color:#0891b2}.i7{background:#e0e7ff;color:#4338ca}.i8{background:#fce7f3;color:#be185d}
  .kpi-label{font-size:12px;color:#64748b;font-weight:900;text-transform:uppercase;letter-spacing:.08em}
  .kpi-value{font-size:27px;font-weight:950;color:#0f172a;margin-top:10px;line-height:1}
  .kpi-note{font-size:12px;color:#64748b;margin-top:8px;font-weight:700}

  .panel{height:100%;border-radius:26px;background:rgba(255,255,255,.94);border:1px solid rgba(226,232,240,.9);box-shadow:0 18px 45px rgba(15,23,42,.08);padding:18px}
  .panel-title{font-weight:950;color:#0f172a;font-size:16px}
  .panel-sub{font-size:12px;color:#64748b;margin-top:4px;font-weight:700}
  .progress{height:10px;border-radius:999px;background:#e2e8f0;overflow:hidden}
  .progress span{display:block;height:100%;border-radius:999px;background:linear-gradient(90deg,#0ea5e9,#2563eb,#06b6d4)}
  .item{padding:13px;border-radius:18px;border:1px solid rgba(226,232,240,.95);background:#f8fafc;margin-top:10px}
  .row{display:flex;align-items:flex-start;justify-content:space-between;gap:12px}
  .strong{font-weight:950;color:#0f172a}.muted{font-size:12px;color:#64748b;font-weight:700}
  .badge{display:inline-flex;padding:5px 10px;border-radius:999px;font-size:11px;font-weight:950;white-space:nowrap}
  .b-green{background:#dcfce7;color:#166534}.b-yellow{background:#fef3c7;color:#92400e}.b-red{background:#fee2e2;color:#991b1b}.b-blue{background:#dbeafe;color:#1d4ed8}.b-slate{background:#e2e8f0;color:#334155}
  .alert{padding:13px;border-radius:18px;margin-top:10px;font-size:13px;font-weight:900}
  .alert-red{background:#fef2f2;color:#991b1b;border:1px solid #fecaca}
  .alert-yellow{background:#fffbeb;color:#92400e;border:1px solid #fde68a}
  .alert-green{background:#ecfdf5;color:#065f46;border:1px solid #bbf7d0}

  .panel-scroll{max-height:360px;overflow-y:auto;padding-right:4px;margin-top:12px}
  .panel-scroll::-webkit-scrollbar{width:8px}
  .panel-scroll::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:999px}
  .panel-scroll::-webkit-scrollbar-track{background:transparent}

  .team-list{display:grid;grid-template-columns:1fr;gap:12px}
  .team-card{display:flex;align-items:center;gap:12px;border:1px solid #e2e8f0;border-radius:20px;padding:12px;background:#f8fafc;min-width:0}
  .team-avatar{width:48px;height:48px;border-radius:999px;object-fit:cover;flex-shrink:0;border:2px solid #dbeafe}
  .team-info{min-width:0;flex:1}
  .truncate{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
</style>

<div class="vs-dash">

  <section class="vs-hero">
    <div class="vs-hero-inner">
      <div>
        <div class="vs-eyebrow">VerticeSoft ERP / CRM</div>
        <div class="vs-title">Dashboard Ejecutivo</div>
        <div class="vs-sub">
          Vista general de proyectos, inventario, tareas, costos y flujo financiero
          <strong>{{ $empresaNombre }}</strong>.
        </div>
      </div>

      <div class="vs-actions">
        <a class="vs-btn" href="{{ route('admin.proyectos') }}">Proyectos</a>
        <a class="vs-btn" href="{{ route('inventario.existencias') }}">Inventario</a>
        <a class="vs-btn" href="{{ route('admin.cuentas.index') }}">Cuentas por pagar</a>
      </div>
    </div>
  </section>

  <div class="vs-grid">
    <div class="s3"><div class="kpi"><div class="kpi-top"><div><div class="kpi-label">Proyectos</div><div class="kpi-value">{{ $num($k['proyectos'] ?? 0) }}</div></div><div class="ico i1">{!! $icon('building') !!}</div></div><div class="kpi-note">{{ $num($k['proyectos_activos'] ?? 0) }} activos</div></div></div>
    <div class="s3"><div class="kpi"><div class="kpi-top"><div><div class="kpi-label">Avance promedio</div><div class="kpi-value">{{ number_format($avancePromedio,0) }}%</div></div><div class="ico i2">{!! $icon('trend') !!}</div></div><div class="kpi-note"><div class="progress"><span style="width:{{ $avancePromedio }}%"></span></div></div></div></div>
    <div class="s3"><div class="kpi"><div class="kpi-top"><div><div class="kpi-label">Inventario</div><div class="kpi-value">{{ $money($valorInventario) }}</div></div><div class="ico i3">{!! $icon('box') !!}</div></div><div class="kpi-note">{{ $num($k['materiales'] ?? 0) }} materiales</div></div></div>
    <div class="s3"><div class="kpi"><div class="kpi-top"><div><div class="kpi-label">Tareas vencidas</div><div class="kpi-value" style="color:#dc2626">{{ $num($k['tareas_vencidas'] ?? 0) }}</div></div><div class="ico i5">{!! $icon('clock') !!}</div></div><div class="kpi-note">{{ $num($k['tareas_pendientes'] ?? 0) }} pendientes</div></div></div>
    <div class="s3"><div class="kpi"><div class="kpi-top"><div><div class="kpi-label">Presupuesto</div><div class="kpi-value">{{ $money($presupuestoTotal) }}</div></div><div class="ico i4">{!! $icon('wallet') !!}</div></div><div class="kpi-note">Presupuesto global</div></div></div>
    <div class="s3"><div class="kpi"><div class="kpi-top"><div><div class="kpi-label">Costos</div><div class="kpi-value">{{ $money($costosTotal) }}</div></div><div class="ico i6">{!! $icon('receipt') !!}</div></div><div class="kpi-note">Costos registrados</div></div></div>
    <div class="s3"><div class="kpi"><div class="kpi-top"><div><div class="kpi-label">Por pagar</div><div class="kpi-value">{{ $money($saldoPagar) }}</div></div><div class="ico i7">{!! $icon('credit') !!}</div></div><div class="kpi-note">{{ $num($k['cuentas_por_pagar_vencidas'] ?? 0) }} vencidas</div></div></div>
    <div class="s3"><div class="kpi"><div class="kpi-top"><div><div class="kpi-label">Por cobrar</div><div class="kpi-value">{{ $money($saldoCobrar) }}</div></div><div class="ico i8">{!! $icon('coin') !!}</div></div><div class="kpi-note">{{ $num($k['cuentas_por_cobrar_vencidas'] ?? 0) }} vencidas</div></div></div>
  </div>

  <div class="vs-grid">
    <div class="s7">
      <div class="panel">
        <div class="row">
          <div>
            <div class="panel-title">Proyectos recientes</div>
            <div class="panel-sub">Seguimiento ejecutivo de avance y estado</div>
          </div>
          <a href="{{ route('admin.proyectos') }}" class="badge b-blue" style="text-decoration:none">Ver todo</a>
        </div>

        <div class="panel-scroll">
          @forelse($proyectosRecientes as $p)
            @php
              $estado = (string)($p->estado ?? 'planeado');
              $porcentaje = max(0, min(100, (float)($p->porcentaje ?? 0)));
              $badgeClass = match($estado) {
                'finalizado' => 'b-green',
                'en_ejecucion' => 'b-yellow',
                'pausado' => 'b-red',
                'planeado' => 'b-blue',
                default => 'b-slate',
              };
            @endphp

            <div class="item">
              <div class="row">
                <div style="flex:1;min-width:0">
                  <div class="strong truncate">{{ $p->nombre ?? 'Proyecto' }}</div>
                  <div class="muted truncate">{{ $p->codigo ?? 'Sin código' }} · {{ $p->responsable->name ?? $p->responsable->nombre ?? 'Sin responsable' }}</div>
                  <div class="progress" style="margin-top:10px"><span style="width:{{ $porcentaje }}%"></span></div>
                </div>
                <div style="text-align:right">
                  <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_',' ', $estado)) }}</span>
                  <div class="strong" style="margin-top:8px">{{ number_format($porcentaje,0) }}%</div>
                </div>
              </div>
            </div>
          @empty
            <div class="item muted">No hay proyectos registrados todavía.</div>
          @endforelse
        </div>
      </div>
    </div>

    <div class="s5">
      <div class="panel">
        <div class="panel-title">Alertas inteligentes</div>
        <div class="panel-sub">Puntos críticos del sistema</div>

        <div class="panel-scroll">
          @if(($k['tareas_vencidas'] ?? 0) > 0)
            <div class="alert alert-red">Tienes {{ $num($k['tareas_vencidas']) }} tarea(s) vencida(s).</div>
          @endif

          @if(($k['cuentas_por_pagar_vencidas'] ?? 0) > 0)
            <div class="alert alert-red">Hay {{ $num($k['cuentas_por_pagar_vencidas']) }} cuenta(s) por pagar vencida(s).</div>
          @endif

          @if(($k['cuentas_por_cobrar_vencidas'] ?? 0) > 0)
            <div class="alert alert-yellow">Hay {{ $num($k['cuentas_por_cobrar_vencidas']) }} cuenta(s) por cobrar vencida(s).</div>
          @endif

          @if(($k['proyectos_activos'] ?? 0) > 0)
            <div class="alert alert-green">{{ $num($k['proyectos_activos']) }} proyecto(s) activos en seguimiento.</div>
          @endif

          @if(($k['tareas_vencidas'] ?? 0) <= 0 && ($k['cuentas_por_pagar_vencidas'] ?? 0) <= 0 && ($k['cuentas_por_cobrar_vencidas'] ?? 0) <= 0)
            <div class="alert alert-green">Todo bajo control por ahora.</div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="vs-grid">
    <div class="s7">
      <div class="panel">
        <div class="row">
          <div>
            <div class="panel-title">Movimientos recientes</div>
            <div class="panel-sub">Actividad operativa del sistema</div>
          </div>
          <a href="{{ route('inventario.movimientos') }}" class="badge b-blue" style="text-decoration:none">Ver todo</a>
        </div>

        <div class="panel-scroll">
          @forelse($ultimosMovs as $m)
            <div class="item">
              <div class="row">
                <div style="min-width:0">
                  <div class="strong truncate">{{ $m->material->descripcion ?? 'Material' }}</div>
                  <div class="muted">
                    {{ ucfirst($m->tipo ?? 'Movimiento') }}
                    @if(!empty($m->fecha))
                      · {{ \Illuminate\Support\Carbon::parse($m->fecha)->format('d/m/Y') }}
                    @endif
                  </div>
                </div>

                <div style="text-align:right">
                  <span class="badge b-green">Confirmado</span>
                  <div class="muted" style="margin-top:6px">
                    {{ number_format((float)($m->cantidad ?? 0),2) }}
                  </div>
                </div>
              </div>
            </div>
          @empty
            <div class="item muted">No hay movimientos recientes.</div>
          @endforelse
        </div>
      </div>
    </div>

    <div class="s5">
      <div class="panel">
        <div class="row">
          <div>
            <div class="panel-title">Equipo de supervisión activo</div>
            <div class="panel-sub">Usuarios asignados a {{ $empresaNombre }}</div>
          </div>
          <a href="{{ route('admin.usuarios') }}" class="badge b-blue" style="text-decoration:none">Ver todo</a>
        </div>

        <div class="panel-scroll">
          <div class="team-list">
            @forelse($equipoActivo as $u)
              <div class="team-card">
                <img
                  class="team-avatar"
                  src="https://ui-avatars.com/api/?name={{ urlencode($u->name ?? $u->nombre ?? 'Usuario') }}&background=0f172a&color=fff"
                  alt="{{ $u->name ?? $u->nombre ?? 'Usuario' }}"
                >

                <div class="team-info">
                  <div class="strong truncate">{{ $u->name ?? $u->nombre ?? 'Usuario' }}</div>
                  <div class="muted truncate">{{ $u->email }}</div>
                </div>

                <span class="badge b-green">Activo</span>
              </div>
            @empty
              <div class="item muted">No hay usuarios asignados a esta empresa.</div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="vs-grid">
    <div class="s12">
      <div class="panel">
        <div class="row">
          <div>
            <div class="panel-title">Top materiales por valor</div>
            <div class="panel-sub">Materiales con mayor impacto económico</div>
          </div>
          <a href="{{ route('inventario.existencias') }}" class="badge b-blue" style="text-decoration:none">Ver todo</a>
        </div>

        <div class="panel-scroll">
          @forelse($topMateriales as $t)
            <div class="item">
              <div class="row">
                <div>
                  <div class="strong">{{ $t->codigo ?? 'MAT' }}</div>
                  <div class="muted">{{ $t->descripcion ?? 'Material' }}</div>
                </div>
                <div style="text-align:right">
                  <div class="strong">{{ $money($t->valor ?? 0) }}</div>
                  <div class="muted">Stock: {{ number_format((float)($t->stock ?? 0), 4) }}</div>
                </div>
              </div>
            </div>
          @empty
            <div class="item muted">No hay existencias valoradas.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>

</div>
@endsection