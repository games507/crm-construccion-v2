@extends('layouts.base')
@section('title','Nuevo Rol')

@section('content')
@php
  // blindaje para evitar errores
  $grupos = $grupos ?? collect();
@endphp

<div class="card" style="max-width:1100px;margin:0 auto;">

  <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;">
    <div>
      <h2 style="margin:0 0 6px 0;">Nuevo Rol</h2>
      <div style="color:#64748b;font-size:13px;">Crea un rol y asigna permisos por módulo.</div>
    </div>
    <a class="btn btn-outline" href="{{ route('admin.roles') }}">← Volver</a>
  </div>

  @if ($errors->any())
    <div class="alert" style="margin-top:14px;">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('admin.roles.store') }}" style="margin-top:16px;">
    @csrf

    <div class="grid">
      <div class="col-12">
        <div class="field">
          <div class="label">Nombre del Rol</div>
          <div class="input-wrap">
            <div class="input-ico">R</div>
            <input class="input" name="name" value="{{ old('name') }}" placeholder="Ej: Supervisor" required>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="card" style="padding:14px;border-radius:14px;background:#f8fafc;border:1px dashed rgba(15,23,42,.12);">
          <div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;align-items:center;">
            <div style="font-weight:900;">Permisos</div>

            <div style="display:flex;gap:10px;flex-wrap:wrap;">
              <button type="button" class="btn btn-outline" onclick="toggleAll(true)">Marcar todo</button>
              <button type="button" class="btn btn-outline" onclick="toggleAll(false)">Desmarcar todo</button>
            </div>
          </div>

          @if($grupos->count() === 0)
            <div style="margin-top:10px;color:#64748b;">
              No hay permisos registrados. Ejecuta tu seeder de permisos/roles.
            </div>
          @else
            <div style="margin-top:12px;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;">
              @foreach($grupos as $key => $items)
                @php
                  $titulo = strtoupper((string)$key);
                @endphp

                <div style="border:1px solid rgba(15,23,42,.10);border-radius:14px;padding:12px;background:#fff;">
                  <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;">
                    <div style="font-weight:900;">{{ $titulo }}</div>
                    <button type="button" class="btn btn-outline" onclick="toggleGroup('{{ $key }}')">Marcar grupo</button>
                  </div>

                  <div style="margin-top:10px;display:flex;flex-direction:column;gap:8px;">
                    @foreach($items as $perm)
                      @php
                        $p = $perm->name;
                        $checked = in_array($p, old('permissions', []), true);
                      @endphp
                      <label style="display:flex;align-items:center;gap:10px;">
                        <input class="perm perm-{{ $key }}"
                               type="checkbox"
                               name="permissions[]"
                               value="{{ $p }}"
                               {{ $checked ? 'checked' : '' }}>
                        <span style="font-weight:700;">{{ $p }}</span>
                      </label>
                    @endforeach
                  </div>
                </div>
              @endforeach
            </div>
          @endif

        </div>
      </div>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:18px;">
      <a class="btn btn-outline" href="{{ route('admin.roles') }}">Cancelar</a>
      <button class="btn" type="submit">Guardar rol</button>
    </div>
  </form>
</div>

@push('scripts')
<script>
function toggleAll(on){
  document.querySelectorAll('.perm').forEach(c => c.checked = !!on);
}
function toggleGroup(key){
  const items = document.querySelectorAll('.perm-' + key);
  const anyOff = Array.from(items).some(c => !c.checked);
  items.forEach(c => c.checked = anyOff);
}
</script>
@endpush
@endsection
