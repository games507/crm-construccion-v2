@extends('layouts.base')
@section('title','Proyectos')

@section('content')
@php
  $q = $q ?? request('q','');
  $empresaId = $empresaId ?? request('empresa_id','');
  $estado = $estado ?? request('estado','');
  $soloActivos = $soloActivos ?? request('solo_activos','');
@endphp

<div class="card" style="max-width:1100px;margin:0 auto;">

  <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:flex-start;">
    <div>
      <h2 style="margin:0 0 6px 0;">Proyectos</h2>
      <div style="color:#64748b;font-size:13px;">Gestión de proyectos por empresa, estado y vigencia.</div>
    </div>

    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
      <form method="GET" action="{{ route('admin.proyectos') }}" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <div class="input-wrap" style="min-width:260px;">
          <div class="input-ico">Q</div>
          <input class="input" name="q" value="{{ $q }}" placeholder="Buscar por código, nombre, ubicación, estado">
        </div>

        @can('empresas.ver')
          <div class="select-wrap" style="min-width:240px;">
            <div class="select-icon">E</div>
            <select name="empresa_id">
              <option value="">— Todas las empresas —</option>
              @foreach($empresas as $e)
                <option value="{{ $e->id }}" @selected((string)$empresaId === (string)$e->id)>{{ $e->nombre }}</option>
              @endforeach
            </select>
          </div>
        @endcan

        <div class="select-wrap" style="min-width:210px;">
          <div class="select-icon">S</div>
          <select name="estado">
            <option value="">— Todos los estados —</option>
            @foreach(['Planificado','En Progreso','En Pausa','Finalizado','Cancelado'] as $st)
              <option value="{{ $st }}" @selected((string)$estado === (string)$st)>{{ $st }}</option>
            @endforeach
          </select>
        </div>

        <label style="display:flex;align-items:center;gap:8px;padding:10px 12px;border:1px solid rgba(15,23,42,.12);border-radius:12px;background:#fff;box-shadow:0 10px 24px rgba(2,6,23,.06);height:44px;">
          <input type="checkbox" name="solo_activos" value="1" {{ $soloActivos ? 'checked' : '' }}>
          <span style="font-weight:700;font-size:13px;">Solo activos</span>
        </label>

        <button class="btn btn-outline" type="submit">Filtrar</button>
      </form>

      @can('proyectos.crear')
        <a class="btn" href="{{ route('admin.proyectos.create') }}">+ Nuevo Proyecto</a>
      @endcan
    </div>
  </div>

  @if(session('ok'))
    <div class="alert" style="margin-top:14px;border-color:rgba(34,197,94,.25);background:rgba(34,197,94,.06);color:#14532d;">
      {{ session('ok') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="alert" style="margin-top:14px;">{{ $errors->first() }}</div>
  @endif

  <div style="margin-top:14px;overflow:auto;border:1px solid rgba(15,23,42,.08);border-radius:14px;">
    <table width="100%" cellpadding="10" style="border-collapse:collapse;min-width:1050px;">
      <thead>
        <tr style="background:rgba(2,6,23,.03);text-align:left;">
          <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);">Proyecto</th>
          <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);">Empresa</th>
          <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);">Estado</th>
          <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);">Fechas</th>
          <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);">Activo</th>
          <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);">Acciones</th>
        </tr>
      </thead>

      <tbody>
        @forelse($proyectos as $p)
          @php
            $badgeBg = match($p->estado){
              'Planificado' => 'rgba(99,102,241,.10)',
              'En Progreso' => 'rgba(34,197,94,.10)',
              'En Pausa'    => 'rgba(234,179,8,.10)',
              'Finalizado'  => 'rgba(14,165,233,.10)',
              'Cancelado'   => 'rgba(239,68,68,.10)',
              default       => 'rgba(148,163,184,.12)'
            };
          @endphp
          <tr style="border-bottom:1px solid rgba(15,23,42,.06);">
            <td style="padding:12px;">
              <div style="font-weight:900;">
                {{ $p->codigo ? $p->codigo.' — ' : '' }}{{ $p->nombre }}
              </div>
              <div style="color:#64748b;font-size:12px;">{{ $p->ubicacion ?: '—' }}</div>
            </td>

            <td style="padding:12px;">
              {{ $p->empresa?->nombre ?? '—' }}
            </td>

            <td style="padding:12px;">
              <span style="display:inline-flex;padding:6px 10px;border-radius:999px;font-weight:900;font-size:12px;
                background:{{ $badgeBg }};border:1px solid rgba(15,23,42,.10);">
                {{ $p->estado }}
              </span>
            </td>

            <td style="padding:12px;color:#64748b;">
              <div><b>Inicio:</b> {{ $p->fecha_inicio?->format('Y-m-d') ?? '—' }}</div>
              <div><b>Fin:</b> {{ $p->fecha_fin?->format('Y-m-d') ?? '—' }}</div>
            </td>

            <td style="padding:12px;">
              <span style="display:inline-flex;padding:6px 10px;border-radius:999px;font-weight:900;font-size:12px;
                background:{{ $p->activo ? 'rgba(34,197,94,.10)' : 'rgba(239,68,68,.10)' }};
                border:1px solid rgba(15,23,42,.10);">
                {{ $p->activo ? 'ACTIVO' : 'INACTIVO' }}
              </span>
            </td>

            <td style="padding:12px;">
              @can('proyectos.editar')
                <a class="btn btn-outline" href="{{ route('admin.proyectos.edit',$p) }}">Editar</a>
              @else
                —
              @endcan
            </td>
          </tr>
        @empty
          <tr><td colspan="6" style="padding:14px;color:#64748b;">No hay proyectos.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:14px;">
    @if(method_exists($proyectos,'links'))
      {{ $proyectos->links() }}
    @endif
  </div>

</div>
@endsection
