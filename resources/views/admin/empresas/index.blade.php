@extends('layouts.base')
@section('title','Empresas')

@section('content')
@php
  $q = $q ?? request('q','');
@endphp

<div class="card" style="max-width:1100px;margin:0 auto;">

  <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:flex-start;">
    <div>
      <h2 style="margin:0 0 6px 0;">Empresas</h2>
      <div style="color:#64748b;font-size:13px;">Multiempresa: crea, edita y administra compañías.</div>
    </div>

    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
      <form method="GET" action="{{ route('admin.empresas') }}" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <div class="input-wrap" style="min-width:260px;">
          <div class="input-ico">Q</div>
          <input class="input" name="q" value="{{ $q }}" placeholder="Buscar por nombre, ruc o correo...">
        </div>
        <button class="btn btn-outline" type="submit">Buscar</button>
      </form>

      @can('empresas.crear')
        <a class="btn" href="{{ route('admin.empresas.create') }}">+ Nueva Empresa</a>
      @endcan
    </div>
  </div>

  @if(session('ok'))
    <div class="alert" style="margin-top:14px;border-color:rgba(34,197,94,.25);background:rgba(34,197,94,.06);color:#14532d;">
      {{ session('ok') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="alert" style="margin-top:14px;">
      {{ $errors->first() }}
    </div>
  @endif

  <div style="margin-top:14px;overflow:auto;border:1px solid rgba(15,23,42,.08);border-radius:14px;">
    <table width="100%" cellpadding="10" style="border-collapse:collapse;min-width:980px;">
      <thead>
        <tr style="background:rgba(2,6,23,.03);text-align:left;">
          <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);">Empresa</th>
          <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);">RUC</th>
          <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);">Contacto</th>
          <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);">Estado</th>
          <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);width:220px;">Acciones</th>
        </tr>
      </thead>

      <tbody>
        @forelse($empresas as $e)
          <tr style="border-bottom:1px solid rgba(15,23,42,.06);">
            <td style="padding:12px;">
              <div style="font-weight:900;display:flex;align-items:center;gap:10px;">
                <div style="width:34px;height:34px;border-radius:12px;background:rgba(37,99,235,.12);display:grid;place-items:center;font-weight:900;color:#1d4ed8;">
                  {{ strtoupper(substr($e->nombre,0,1)) }}
                </div>
                <div>
                  <div style="font-weight:900;">{{ $e->nombre }}</div>
                  <div style="color:#64748b;font-size:12px;">ID: {{ $e->id }}</div>
                </div>
              </div>
            </td>

            <td style="padding:12px;">
              <span style="font-weight:800;color:#0f172a;">{{ $e->ruc ?: '—' }}</span>
            </td>

            <td style="padding:12px;">
              <div style="font-weight:800;">{{ $e->email ?: '—' }}</div>
              <div style="color:#64748b;font-size:12px;">{{ $e->telefono ?: '' }}</div>
            </td>

            <td style="padding:12px;">
              @if((int)$e->activa === 1)
                <span style="display:inline-flex;padding:6px 10px;border-radius:999px;font-weight:900;font-size:12px;background:rgba(34,197,94,.10);border:1px solid rgba(34,197,94,.18);color:#14532d;">
                  ACTIVA
                </span>
              @else
                <span style="display:inline-flex;padding:6px 10px;border-radius:999px;font-weight:900;font-size:12px;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.18);color:#991b1b;">
                  INACTIVA
                </span>
              @endif
            </td>

            <td style="padding:12px;">
              <div style="display:flex;gap:8px;flex-wrap:wrap;">
                @can('empresas.editar')
                  <a class="btn btn-outline" href="{{ route('admin.empresas.edit',$e) }}">Editar</a>
                @endcan

                @can('empresas.eliminar')
                  <form method="POST" action="{{ route('admin.empresas.destroy',$e) }}" onsubmit="return confirm('¿Eliminar esta empresa?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline" type="submit" style="border-color:rgba(239,68,68,.25);color:#991b1b;">
                      Eliminar
                    </button>
                  </form>
                @endcan

                @if(!auth()->user()->can('empresas.editar') && !auth()->user()->can('empresas.eliminar'))
                  <span style="color:#94a3b8;">—</span>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" style="padding:14px;color:#64748b;">No hay empresas.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:14px;">
    @if(method_exists($empresas,'links'))
      {{ $empresas->links() }}
    @endif
  </div>

</div>
@endsection
