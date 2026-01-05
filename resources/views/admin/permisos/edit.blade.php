@extends('layouts.base')
@section('title','Editar Permiso')

@section('content')
<div class="card" style="max-width:980px;margin:0 auto;">

  <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:flex-start;">
    <div>
      <h2 style="margin:0 0 6px 0;">Editar Permiso</h2>
      <div style="color:#64748b;font-size:13px;">
        Actualiza el nombre del permiso (formato sugerido: <b>modulo.accion</b>)
      </div>
    </div>

    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
      <a class="btn btn-outline" href="{{ route('admin.permisos') }}">← Volver</a>

      @can('permisos.eliminar')
        <form method="POST" action="{{ route('admin.permisos.destroy',$permiso) }}"
              onsubmit="return confirm('¿Seguro que deseas eliminar este permiso?');"
              style="margin:0;">
          @csrf
          @method('DELETE')
          <button class="btn btn-outline" type="submit"
                  style="border-color:rgba(239,68,68,.35);color:#b91c1c;">
            Eliminar
          </button>
        </form>
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

  <form method="POST" action="{{ route('admin.permisos.update',$permiso) }}" style="margin-top:16px;">
    @csrf
    @method('PUT')

    <div class="grid">
      <div class="col-12">
        <div class="field">
          <div class="label">Nombre del permiso</div>

          <div class="input-wrap" style="max-width:560px;">
            <div class="input-ico">P</div>
            <input class="input" name="name"
                   value="{{ old('name',$permiso->name) }}"
                   placeholder="Ej: inventario.crear"
                   required
                   oninput="this.value = this.value.toLowerCase()">
          </div>

          <div style="font-size:12px;color:#64748b;margin-top:8px;">
            ID: <b>{{ $permiso->id }}</b>
            &nbsp;•&nbsp; Guard: <b>{{ $permiso->guard_name }}</b>
          </div>

          <div style="font-size:12px;color:#64748b;margin-top:4px;">
            Recomendación: usa puntos para agrupar (ej: <b>usuarios.ver</b>, <b>roles.crear</b>, <b>inventario.ver</b>).
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="card" style="padding:14px;border-radius:14px;background:#f8fafc;border:1px dashed rgba(15,23,42,.12);">
          <div style="font-weight:900;">Ejemplos rápidos</div>

          <div style="margin-top:10px;display:flex;gap:10px;flex-wrap:wrap;">
            @foreach([
              'usuarios.ver','usuarios.crear','usuarios.editar',
              'roles.ver','roles.crear','roles.editar',
              'permisos.ver','permisos.crear','permisos.editar','permisos.eliminar',
              'empresas.ver','empresas.crear','empresas.editar',
              'proyectos.ver','proyectos.crear','proyectos.editar',
              'inventario.ver','inventario.crear','kardex.ver'
            ] as $ex)
              <button type="button"
                      class="btn btn-outline"
                      style="padding:8px 10px;border-radius:10px;"
                      onclick="setExample('{{ $ex }}')">
                {{ $ex }}
              </button>
            @endforeach
          </div>

          <div style="margin-top:10px;color:#64748b;font-size:12px;">
            Nota: renombrar permisos puede afectar roles existentes. Hazlo con cuidado.
          </div>
        </div>
      </div>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:18px;flex-wrap:wrap;">
      <a class="btn btn-outline" href="{{ route('admin.permisos') }}">Cancelar</a>
      <button class="btn" type="submit">Guardar cambios</button>
    </div>
  </form>

</div>

@push('scripts')
<script>
function setExample(val){
  const i = document.querySelector('input[name="name"]');
  if(i){
    i.value = String(val).toLowerCase();
    i.focus();
  }
}
</script>
@endpush
@endsection
