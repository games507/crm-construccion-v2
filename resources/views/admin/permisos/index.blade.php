@extends('layouts.base')
@section('title','Permisos')

@section('content')
@php
  $q = (string) ($q ?? request('q',''));

  // ✅ Detecta paginator real (LengthAwarePaginator / Paginator)
  $isPaginator = ($permisos ?? null) instanceof \Illuminate\Contracts\Pagination\Paginator
              || ($permisos ?? null) instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;

  // ✅ Total seguro (si es paginator usa total(), si no count())
  $totalReg = (is_object($permisos ?? null) && method_exists($permisos,'total'))
    ? (int) $permisos->total()
    : (is_iterable($permisos ?? []) ? count($permisos) : 0);

  // ✅ Helper para mantener q en paginación (cuando uses ->url(), ->nextPageUrl(), etc.)
  $withQ = function (?string $url) use ($q) {
    if (!$url) return null;
    if ($q === '') return $url;

    $sep = str_contains($url, '?') ? '&' : '?';
    return $url . $sep . 'q=' . urlencode($q);
  };
@endphp

<div class="card" style="max-width:1100px;margin:0 auto;">

  {{-- Header --}}
  <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:flex-start;">
    <div>
      <h2 style="margin:0 0 6px 0;">Permisos</h2>
      <div style="color:#64748b;font-size:13px;">
        Catálogo de permisos del sistema (Spatie). Puedes crear y editar nombres.
      </div>
    </div>

    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
      <form method="GET" action="{{ route('admin.permisos') }}" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <div class="input-wrap" style="min-width:260px;">
          <div class="input-ico">Q</div>
          <input class="input" name="q" value="{{ $q }}" placeholder="Buscar permiso...">
        </div>
        <button class="btn btn-outline" type="submit">Buscar</button>
      </form>

      @can('permisos.crear')
        <a class="btn" href="{{ route('admin.permisos.create') }}">+ Nuevo Permiso</a>
      @endcan
    </div>
  </div>

  {{-- Alerts --}}
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

  {{-- Tabla --}}
  <div style="margin-top:14px;overflow:auto;border:1px solid rgba(15,23,42,.08);border-radius:14px;">
    <table width="100%" cellpadding="10" style="border-collapse:collapse;min-width:980px;">
      <thead>
        <tr style="background:rgba(2,6,23,.03);text-align:left;">
          <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);">Nombre</th>
          <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);">Módulo</th>
          <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);">Guard</th>
          <th style="padding:12px;border-bottom:1px solid rgba(15,23,42,.08);width:180px;">Acciones</th>
        </tr>
      </thead>

      <tbody>
        @forelse($permisos as $p)
          @php
            $parts = explode('.', (string)$p->name);
            $mod = $parts[0] ?? 'otros';
          @endphp

          <tr style="border-bottom:1px solid rgba(15,23,42,.06);">
            <td style="padding:12px;">
              <div style="font-weight:900;">{{ $p->name }}</div>
              <div style="color:#64748b;font-size:12px;">ID: {{ $p->id }}</div>
            </td>

            <td style="padding:12px;">
              <span style="display:inline-flex;padding:6px 10px;border-radius:999px;font-weight:900;font-size:12px;
                background:rgba(37,99,235,.10);border:1px solid rgba(15,23,42,.10);">
                {{ $mod }}
              </span>
            </td>

            <td style="padding:12px;">
              <span style="color:#64748b;font-weight:800;">{{ $p->guard_name }}</span>
            </td>

            <td style="padding:12px;">
              <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                @can('permisos.editar')
                  <a class="btn btn-outline" href="{{ route('admin.permisos.edit',$p) }}">Editar</a>
                @endcan

                @can('permisos.eliminar')
                  <form method="POST" action="{{ route('admin.permisos.destroy',$p) }}"
                        onsubmit="return confirm('¿Seguro que deseas eliminar este permiso?');"
                        style="margin:0;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline" type="submit" style="border-color:rgba(239,68,68,.35);color:#b91c1c;">
                      Eliminar
                    </button>
                  </form>
                @endcan

                @if(!auth()->user()->can('permisos.editar') && !auth()->user()->can('permisos.eliminar'))
                  <span style="color:#94a3b8;">—</span>
                @endif
              </div>
            </td>

          </tr>
        @empty
          <tr>
            <td colspan="4" style="padding:14px;color:#64748b;">No hay permisos.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Paginación estilo ERP --}}
  @if($isPaginator)
    <div style="margin-top:14px;display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
      <div style="color:#64748b;font-size:13px;font-weight:700;">
        Mostrando
        <b>{{ $permisos->firstItem() ?? 0 }}</b>–
        <b>{{ $permisos->lastItem() ?? 0 }}</b>
        de <b>{{ $totalReg }}</b>
      </div>

      <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">

        {{-- Anterior --}}
        @if($permisos->onFirstPage())
          <span class="btn btn-outline" style="opacity:.45;pointer-events:none;">← Anterior</span>
        @else
          <a class="btn btn-outline" href="{{ $withQ($permisos->previousPageUrl()) }}">← Anterior</a>
        @endif

        {{-- Páginas (compacto) --}}
        @php
          $cur  = $permisos->currentPage();
          $last = method_exists($permisos,'lastPage') ? $permisos->lastPage() : $cur;
          $from = max(1, $cur - 2);
          $to   = min($last, $cur + 2);
        @endphp

        @if($from > 1)
          <a class="btn btn-outline" href="{{ $withQ($permisos->url(1)) }}" style="padding:10px 12px;">1</a>
          @if($from > 2)
            <span style="color:#94a3b8;font-weight:800;padding:0 6px;">…</span>
          @endif
        @endif

        @for($i=$from; $i<=$to; $i++)
          @if($i == $cur)
            <span class="btn" style="pointer-events:none;padding:10px 12px;">{{ $i }}</span>
          @else
            <a class="btn btn-outline" href="{{ $withQ($permisos->url($i)) }}" style="padding:10px 12px;">{{ $i }}</a>
          @endif
        @endfor

        @if($to < $last)
          @if($to < $last - 1)
            <span style="color:#94a3b8;font-weight:800;padding:0 6px;">…</span>
          @endif
          <a class="btn btn-outline" href="{{ $withQ($permisos->url($last)) }}" style="padding:10px 12px;">{{ $last }}</a>
        @endif

        {{-- Siguiente --}}
        @if($permisos->hasMorePages())
          <a class="btn btn-outline" href="{{ $withQ($permisos->nextPageUrl()) }}">Siguiente →</a>
        @else
          <span class="btn btn-outline" style="opacity:.45;pointer-events:none;">Siguiente →</span>
        @endif

      </div>
    </div>
  @endif

</div>
@endsection
