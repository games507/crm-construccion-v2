@extends('layouts.base')
@section('title','Empresas')

@section('content')
@php
  $qVal = trim((string)($q ?? request('q','')));

  $baseResumen = method_exists($empresas, 'getCollection')
      ? $empresas->getCollection()
      : collect($empresas);

  $totalEmpresas = method_exists($empresas, 'total')
      ? $empresas->total()
      : $baseResumen->count();

  $activas = $baseResumen->where('activa', 1)->count();
  $inactivas = $baseResumen->where('activa', '!=', 1)->count();
  $conCorreo = $baseResumen->filter(fn($e) => !empty($e->email) || !empty($e->correo))->count();

  $icon = function($name){
    if($name==='plus') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 5v14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='edit') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M4 20h4l10-10-4-4L4 16v4Z" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='trash') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M3 6h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M8 6V4h8v2" stroke="currentColor" stroke-width="2"/><path d="M6 6l1 14h10l1-14" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='building') return '<svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 9h1M9 13h1M9 17h1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='mail') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="2"/><path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    if($name==='phone') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.4 19.4 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.7.6 2.5a2 2 0 0 1-.4 2.1L8 9.6a16 16 0 0 0 6.4 6.4l1.3-1.3a2 2 0 0 1 2.1-.4c.8.3 1.6.5 2.5.6A2 2 0 0 1 22 16.9Z" stroke="currentColor" stroke-width="2"/></svg>';
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

.vs-hero{position:relative;overflow:hidden;border-radius:30px;padding:24px;background:radial-gradient(circle at 12% 15%, rgba(34,211,238,.28), transparent 30%),radial-gradient(circle at 88% 18%, rgba(59,130,246,.28), transparent 32%),linear-gradient(135deg,#07172e,#0b2f54,#061425);color:white;box-shadow:0 24px 70px rgba(2,6,23,.22);margin-bottom:18px}
.vs-hero-inner{display:flex;align-items:center;justify-content:space-between;gap:28px;flex-wrap:wrap;position:relative;z-index:1}
.vs-hero-icon{height:82px;width:82px;border-radius:24px;background:white;color:#0b4f7d;display:flex;align-items:center;justify-content:center;box-shadow:0 14px 34px rgba(0,0,0,.18);flex-shrink:0}
.vs-hero-title{font-size:26px;font-weight:950;line-height:1.1}
.vs-hero-sub{margin-top:7px;color:#cbd5e1;font-weight:700;font-size:13px;max-width:680px}

.kpi-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:18px}
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

.company-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
@media(max-width:1180px){.company-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:720px){.company-grid{grid-template-columns:1fr}}

.company-card{background:white;border:1px solid #e2e8f0;border-radius:28px;padding:18px;box-shadow:0 16px 42px rgba(15,23,42,.07);transition:.2s ease}
.company-card:hover{transform:translateY(-2px);box-shadow:0 22px 55px rgba(15,23,42,.10)}
.company-logo{height:58px;width:58px;border-radius:20px;background:linear-gradient(135deg,#0f172a,#0b4f7d);color:white;display:flex;align-items:center;justify-content:center;font-weight:950;font-size:17px;box-shadow:0 12px 25px rgba(15,23,42,.18);flex-shrink:0;overflow:hidden}
.company-logo img{width:100%;height:100%;object-fit:contain;background:white;padding:6px}
.company-name{font-size:17px;font-weight:950;color:#0f172a;line-height:1.25}
.company-id{margin-top:4px;font-size:12px;color:#64748b;font-weight:800}
.company-info{margin-top:14px;border-radius:22px;border:1px solid #e2e8f0;background:#f8fafc;padding:14px;display:grid;gap:10px}
.info-row{display:flex;justify-content:space-between;gap:12px;font-size:13px}
.info-label{color:#64748b;font-weight:900}
.info-value{color:#0f172a;font-weight:950;text-align:right;min-width:0}

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
      <div class="vs-title">Empresas</div>
      <div class="vs-sub">Centro multiempresa para administrar compañías, accesos y operación centralizada.</div>
    </div>

    <div class="vs-actions">
      <a href="{{ route('admin.empresas.create') }}" class="vs-btn vs-btn-primary">
        {!! $icon('plus') !!} Nueva Empresa
      </a>
    </div>
  </div>

  @if(session('ok'))
    <div class="mb-4 rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 font-black">
      {{ session('ok') }}
    </div>
  @endif

  @if($errors->any())
    <div class="mb-4 rounded-3xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 font-black">
      {{ $errors->first() }}
    </div>
  @endif

  <div class="vs-hero">
    <div class="vs-hero-inner">
      <div class="flex items-center gap-6 flex-wrap">
        <div class="vs-hero-icon">
          {!! $icon('building') !!}
        </div>

        <div>
          <div class="vs-hero-title">Centro Multiempresa</div>
          <div class="vs-hero-sub">
            Administra empresas, identidad corporativa y estado operativo desde una sola consola VerticeSoft.
          </div>
        </div>
      </div>

      <div class="text-right">
        <div class="text-[11px] uppercase tracking-[.18em] text-blue-200 font-black">Multiempresa</div>
        <div class="mt-2 text-lg font-black">{{ $totalEmpresas }} compañías</div>
      </div>
    </div>
  </div>

  <div class="kpi-grid">
    <div class="kpi">
      <div class="kpi-label">Total empresas</div>
      <div class="kpi-value">{{ $totalEmpresas }}</div>
    </div>

    <div class="kpi">
      <div class="kpi-label">Activas</div>
      <div class="kpi-value text-emerald-700">{{ $activas }}</div>
    </div>

    <div class="kpi">
      <div class="kpi-label">Inactivas</div>
      <div class="kpi-value text-rose-700">{{ $inactivas }}</div>
    </div>

    <div class="kpi">
      <div class="kpi-label">Con correo</div>
      <div class="kpi-value text-indigo-700">{{ $conCorreo }}</div>
    </div>
  </div>

  <div class="vs-card mb-4">
    <div class="vs-card-body">
      <form method="GET" action="{{ route('admin.empresas') }}" class="filter-grid">
        <div>
          <label class="text-xs font-black text-slate-500 uppercase">Buscar</label>
          <input name="q"
                 value="{{ $qVal }}"
                 placeholder="Nombre, RUC o correo..."
                 class="input-vs mt-1"
                 style="width:340px;max-width:100%;">
        </div>

        <button type="submit" class="vs-btn vs-btn-primary">Buscar</button>

        @if($qVal !== '')
          <a href="{{ route('admin.empresas') }}" class="vs-btn vs-btn-light">
            Limpiar
          </a>
        @endif

        <div class="ml-auto text-right">
          <div class="text-[11px] uppercase tracking-wide text-slate-500 font-black">Registros</div>
          <div class="text-sm font-black text-slate-900">
            {{ method_exists($empresas, 'total') ? $empresas->total() : $baseResumen->count() }}
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="company-grid">
    @forelse($empresas as $e)
      @php
        $logo = $e->logo_path ?? $e->logo ?? null;
        $logoUrl = $logo ? asset('storage/' . ltrim($logo, '/')) : null;

        $nombre = trim((string)($e->nombre ?? 'Empresa'));
        $partes = preg_split('/\s+/', $nombre, -1, PREG_SPLIT_NO_EMPTY);
        $iniciales = 'E';

        if (!empty($partes[0]) && !empty($partes[1])) {
          $iniciales = strtoupper(substr($partes[0],0,1) . substr($partes[1],0,1));
        } elseif (!empty($partes[0])) {
          $iniciales = strtoupper(substr($partes[0],0,1));
        }

        $correo = $e->email ?? $e->correo ?? null;
        $telefono = $e->telefono ?? null;
        $activa = (int)($e->activa ?? 1) === 1;
      @endphp

      <div class="company-card">
        <div class="flex items-start justify-between gap-3">
          <div class="flex items-start gap-3 min-w-0">
            <div class="company-logo">
              @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="Logo {{ $nombre }}">
              @else
                {{ $iniciales }}
              @endif
            </div>

            <div class="min-w-0">
              <div class="company-name truncate">{{ $nombre }}</div>
              <div class="company-id">ID: {{ $e->id }}</div>

              <div class="mt-3 flex flex-wrap gap-2">
                @if($activa)
                  <span class="vs-badge vs-badge-ok">Activa</span>
                @else
                  <span class="vs-badge vs-badge-danger">Inactiva</span>
                @endif

                <span class="vs-badge vs-badge-indigo">Multiempresa</span>
              </div>
            </div>
          </div>

          <div class="text-slate-400">
            {!! $icon('building') !!}
          </div>
        </div>

        <div class="company-info">
          <div class="info-row">
            <span class="info-label">RUC</span>
            <span class="info-value truncate">{{ $e->ruc ?: '—' }}</span>
          </div>

          <div class="info-row">
            <span class="info-label">Correo</span>
            <span class="info-value truncate">{{ $correo ?: '—' }}</span>
          </div>

          <div class="info-row">
            <span class="info-label">Teléfono</span>
            <span class="info-value">{{ $telefono ?: '—' }}</span>
          </div>
        </div>

        <div class="mt-5 flex items-center justify-between gap-3">
          <div class="text-xs font-black text-slate-400 uppercase tracking-wide">
            Empresa
          </div>

          <div class="flex items-center gap-2">
            <a href="{{ route('admin.empresas.edit',$e) }}"
               title="Editar empresa"
               class="action-btn hover:text-indigo-600">
              {!! $icon('edit') !!}
            </a>

            <form method="POST"
                  action="{{ route('admin.empresas.destroy',$e) }}"
                  onsubmit="return confirm('¿Eliminar esta empresa?');">
              @csrf
              @method('DELETE')

              <button type="submit"
                      title="Eliminar empresa"
                      class="action-btn hover:text-rose-600">
                {!! $icon('trash') !!}
              </button>
            </form>
          </div>
        </div>
      </div>
    @empty
      <div class="vs-card">
        <div class="vs-card-body text-center text-sm font-bold text-slate-500">
          No hay empresas registradas.
        </div>
      </div>
    @endforelse
  </div>

  @if(method_exists($empresas,'links'))
    <div class="mt-4">
      {{ $empresas->links() }}
    </div>
  @endif

</div>
@endsection