@extends('layouts.base')
@section('title','Nuevo Permiso')

@section('content')
<div class="card" style="max-width:980px;margin:0 auto;">

  <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:flex-start;">
    <div>
      <h2 style="margin:0 0 6px 0;">Nuevo Permiso</h2>
      <div style="color:#64748b;font-size:13px;">
        Crea un permiso con formato sugerido: <b>modulo.accion</b>
      </div>
    </div>
    <a class="btn btn-outline" href="{{ route('admin.permisos') }}">← Volver</a>
  </div>

  @if ($errors->any())
    <div class="alert" style="margin-top:14px;">
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('admin.permisos.store') }}" style="margin-top:16px;">
    @csrf

    <div class="grid">
      <div class="col-12">
        <div class="field">
          <div class="label">Nombre del permiso</div>
          <div class="input-wrap">
            <div class="input-ico">P</div>
            <input class="input" name="name" value="{{ old('name') }}" placeholder="Ej: inventario.crear" required>
          </div>
          <div style="font-size:12px;color:#64748b;margin-top:6px;">
            Recomendación: usa puntos para agrupar (<b>usuarios.ver</b>, <b>roles.crear</b>, <b>inventario.ver</b>).
          </div>
        </div>
      </div>

      <div class="col-12">
        <div style="border:1px dashed rgba(15,23,42,.14);border-radius:14px;padding:14px;background:#f8fafc;">
          <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;">
            <div style="font-weight:900;">Ejemplos rápidos</div>
            <button type="button" class="btn btn-outline" style="padding:8px 10px;border-radius:10px;" onclick="clearPerm()">
              Limpiar
            </button>
          </div>

          <div style="margin-top:10px;display:flex;gap:10px;flex-wrap:wrap;">
            @foreach(['usuarios.ver','usuarios.crear','usuarios.editar','roles.ver','roles.crear','roles.editar','permisos.ver','permisos.crear','permisos.editar','empresas.ver','empresas.crear','empresas.editar','proyectos.ver','proyectos.crear','proyectos.editar','inventario.ver','inventario.crear','kardex.ver'] as $ex)
              <button type="button" class="btn btn-outline" style="padding:8px 10px;border-radius:10px;"
                      onclick="setExample('{{ $ex }}')">{{ $ex }}</button>
            @endforeach
          </div>

          <div style="margin-top:10px;color:#64748b;font-size:12px;">
            Tip: mantén una convención fija para todo el ERP (ej: <b>modulo.ver</b>, <b>modulo.crear</b>, <b>modulo.editar</b>, <b>modulo.eliminar</b>).
          </div>
        </div>
      </div>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:18px;">
      <a class="btn btn-outline" href="{{ route('admin.permisos') }}">Cancelar</a>
      <button class="btn" type="submit">Guardar permiso</button>
    </div>
  </form>
</div>

@push('scripts')
<script>
function setExample(val){
  const i = document.querySelector('input[name="name"]');
  if(i){ i.value = val; i.focus(); }
}
function clearPerm(){
  const i = document.querySelector('input[name="name"]');
  if(i){ i.value = ''; i.focus(); }
}
</script>
@endpush
@endsection
