@extends('layouts.base')
@section('title','Usuarios')

@section('content')
@php
  $qVal = trim((string)($q ?? request('q','')));

  $baseResumen = method_exists($usuarios, 'getCollection')
      ? $usuarios->getCollection()
      : collect($usuarios);

  $totalUsuarios = method_exists($usuarios, 'total') ? $usuarios->total() : $baseResumen->count();
  $activos = $baseResumen->where('activo', 1)->count();
  $inactivos = $baseResumen->where('activo', '!=', 1)->count();
  $empresasCount = $baseResumen->pluck('empresa_id')->filter()->unique()->count();

  $icon = function($name){
    if($name==='plus') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 5v14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='edit') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M4 20h4l10-10-4-4L4 16v4Z" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='trash') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M3 6h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M8 6V4h8v2" stroke="currentColor" stroke-width="2"/><path d="M6 6l1 14h10l1-14" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='user') return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/><path d="M4 21a8 8 0 0 1 16 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    return '';
  };
@endphp

<style>
.vs-wrap{max-width:1450px;margin:0 auto;padding:18px}
.vs-head{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:18px}
.vs-title{font-size:30px;font-weight:950;color:#0f172a;line-height:1}
.vs-sub{margin-top:7px;font-size:13px;color:#64748b;font-weight:700}
.vs-actions{display:flex;gap:10px;flex-wrap:wrap}

.vs-btn{height:40px;border:none;border-radius:16px;padding:0 18px;display:inline-flex;align-items:center;justify-content:center;gap:10px;font-weight:900;text-decoration:none;cursor:pointer;transition:.2s ease}
.vs-btn:hover{transform:translateY(-2px)}
.vs-btn-primary{background:linear-gradient(135deg,#0f172a,#0b4f7d);color:white;box-shadow:0 12px 30px rgba(15,23,42,.15)}
.vs-btn-primary:hover{color:white}
.vs-btn-light{background:#f1f5f9;color:#334155;border:1px solid #e2e8f0}
.vs-btn-light:hover{background:#e2e8f0;color:#334155}

.kpi-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:16px}
@media(max-width:1000px){.kpi-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:620px){.kpi-grid{grid-template-columns:1fr}}

.kpi{background:white;border-radius:24px;border:1px solid #e2e8f0;padding:16px;box-shadow:0 14px 40px rgba(15,23,42,.06)}
.kpi-label{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#64748b;font-weight:950}
.kpi-value{margin-top:8px;font-size:26px;font-weight:950;color:#0f172a}

.vs-card{background:white;border-radius:28px;border:1px solid #e2e8f0;box-shadow:0 18px 50px rgba(15,23,42,.07);overflow:hidden}
.vs-card-body{padding:14px 18px}

.filter-grid{display:flex;gap:8px;flex-wrap:wrap;align-items:end}
.input-vs{height:40px;border:1px solid #dbe2ea;border-radius:16px;padding:0 14px;font-weight:700;outline:none;background:white;color:#0f172a}
.input-vs:focus{border-color:#38bdf8;box-shadow:0 0 0 4px rgba(14,165,233,.12)}

.user-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
@media(max-width:1180px){.user-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:720px){.user-grid{grid-template-columns:1fr}}

.user-card{background:white;border:1px solid #e2e8f0;border-radius:28px;padding:18px;box-shadow:0 16px 42px rgba(15,23,42,.07);transition:.2s ease}
.user-card:hover{transform:translateY(-2px);box-shadow:0 22px 55px rgba(15,23,42,.10)}
.avatar{height:52px;width:52px;border-radius:999px;background:linear-gradient(135deg,#0f172a,#0b4f7d);color:white;display:flex;align-items:center;justify-content:center;font-weight:950;font-size:15px;box-shadow:0 12px 25px rgba(15,23,42,.18);flex-shrink:0}
.user-name{font-size:17px;font-weight:950;color:#0f172a;line-height:1.25}
.user-email{margin-top:4px;font-size:13px;color:#64748b;font-weight:700}
.user-meta{margin-top:12px;display:flex;gap:8px;flex-wrap:wrap}

.vs-badge{display:inline-flex;align-items:center;padding:7px 12px;border-radius:999px;font-size:11px;font-weight:950;border:1px solid transparent}
.vs-badge-ok{background:#dcfce7;color:#166534;border-color:#bbf7d0}
.vs-badge-danger{background:#fee2e2;color:#991b1b;border-color:#fecaca}
.vs-badge-indigo{background:#eef2ff;color:#4338ca;border-color:#c7d2fe}
.vs-badge-slate{background:#f1f5f9;color:#334155;border-color:#e2e8f0}

.action-btn{height:32px;width:32px;border-radius:12px;border:1px solid #e2e8f0;background:white;display:inline-flex;align-items:center;justify-content:center;color:#64748b;transition:.2s ease}
.action-btn:hover{background:#f8fafc;transform:translateY(-1px)}
</style>

<div class="vs-wrap">

  <div class="vs-head">
    <div>
      <div class="vs-title">Usuarios</div>
      <div class="vs-sub">Centro de gestión de usuarios, roles, empresas y accesos.</div>
    </div>

    <div class="vs-actions">
      @can('usuarios.crear')
        <a href="{{ route('admin.usuarios.create') }}" class="vs-btn vs-btn-primary">
          {!! $icon('plus') !!} Nuevo usuario
        </a>
      @endcan
    </div>
  </div>

  @if (session('ok'))
    <div class="mb-4 rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 font-black">
      {{ session('ok') }}
    </div>
  @endif

  @if (session('err'))
    <div class="mb-4 rounded-3xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 font-black">
      {{ session('err') }}
    </div>
  @endif

  <div class="kpi-grid">
    <div class="kpi">
      <div class="kpi-label">Total usuarios</div>
      <div class="kpi-value">{{ $totalUsuarios }}</div>
    </div>

    <div class="kpi">
      <div class="kpi-label">Activos</div>
      <div class="kpi-value text-emerald-700">{{ $activos }}</div>
    </div>

    <div class="kpi">
      <div class="kpi-label">Inactivos</div>
      <div class="kpi-value text-rose-700">{{ $inactivos }}</div>
    </div>

    <div class="kpi">
      <div class="kpi-label">Empresas</div>
      <div class="kpi-value text-indigo-700">{{ $empresasCount }}</div>
    </div>
  </div>

  <div class="vs-card mb-4">
    <div class="vs-card-body">
      <form method="GET" action="{{ route('admin.usuarios') }}" class="filter-grid">
        <div>
          <label class="text-xs font-black text-slate-500 uppercase">Buscar</label>
          <input name="q"
                 value="{{ $qVal }}"
                 placeholder="Nombre, correo o empresa..."
                 class="input-vs mt-1"
                 style="width:320px;max-width:100%;">
        </div>

        <button type="submit" class="vs-btn vs-btn-primary">Buscar</button>

        @if($qVal !== '')
          <a href="{{ route('admin.usuarios') }}" class="vs-btn vs-btn-light">
            Limpiar
          </a>
        @endif

        <div class="ml-auto text-right">
          <div class="text-[11px] uppercase tracking-wide text-slate-500 font-black">Registros</div>
          <div class="text-sm font-black text-slate-900">{{ method_exists($usuarios, 'total') ? $usuarios->total() : $baseResumen->count() }}</div>
        </div>
      </form>
    </div>
  </div>

  <div class="user-grid">
    @forelse($usuarios as $u)
      @php
        $nombre = trim((string)($u->name ?? $u->nombre ?? $u->nombre_completo ?? 'Usuario'));
        $partes = preg_split('/\s+/', $nombre, -1, PREG_SPLIT_NO_EMPTY);
        $iniciales = 'U';

        if (!empty($partes[0]) && !empty($partes[1])) {
          $iniciales = strtoupper(substr($partes[0],0,1) . substr($partes[1],0,1));
        } elseif (!empty($partes[0])) {
          $iniciales = strtoupper(substr($partes[0],0,1));
        }

        $rolNombre = method_exists($u, 'getRoleNames')
          ? ($u->getRoleNames()->first() ?? 'Sin rol')
          : ($u->rol ?? 'Sin rol');

        $empresaNombre = $u->empresa->nombre ?? 'Sin empresa';
        $activo = (int)($u->activo ?? 1) === 1;
      @endphp

      <div class="user-card">
        <div class="flex items-start justify-between gap-3">
          <div class="flex items-start gap-3 min-w-0">
            <div class="avatar">
              {{ $iniciales }}
            </div>

            <div class="min-w-0">
              <div class="user-name truncate">{{ $nombre }}</div>
              <div class="user-email truncate">{{ $u->email ?? 'Sin correo' }}</div>

              <div class="user-meta">
                <span class="vs-badge vs-badge-indigo">{{ $rolNombre }}</span>

                @if($activo)
                  <span class="vs-badge vs-badge-ok">Activo</span>
                @else
                  <span class="vs-badge vs-badge-danger">Inactivo</span>
                @endif
              </div>
            </div>
          </div>

          <div class="text-slate-400">
            {!! $icon('user') !!}
          </div>
        </div>

        <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
          <div class="text-[11px] uppercase tracking-wide text-slate-500 font-black">
            Empresa asignada
          </div>
          <div class="mt-1 text-sm font-black text-slate-800 truncate">
            {{ $empresaNombre }}
          </div>
        </div>

        <div class="mt-5 flex items-center justify-between gap-3">
          <div class="text-xs font-black text-slate-400 uppercase tracking-wide">
            Usuario
          </div>

          <div class="flex items-center gap-2">
            @can('usuarios.editar')
              <a href="{{ route('admin.usuarios.edit', $u->id) }}"
                 title="Editar usuario"
                 class="action-btn hover:text-indigo-600">
                {!! $icon('edit') !!}
              </a>
            @endcan

            @can('usuarios.eliminar')
              <form action="{{ route('admin.usuarios.destroy', $u->id) }}"
                    method="POST"
                    onsubmit="return confirm('¿Eliminar este usuario?');">
                @csrf
                @method('DELETE')

                <button type="submit"
                        title="Eliminar usuario"
                        class="action-btn hover:text-rose-600">
                  {!! $icon('trash') !!}
                </button>
              </form>
            @endcan
          </div>
        </div>
      </div>
    @empty
      <div class="vs-card">
        <div class="vs-card-body text-center text-sm font-bold text-slate-500">
          No hay usuarios para los filtros seleccionados.
        </div>
      </div>
    @endforelse
  </div>

  <div class="mt-4">
    {{ $usuarios->links() }}
  </div>

</div>
@endsection