@extends('layouts.base')
@section('title','Proveedores')

@section('content')
<style>
  .vs-wrap{max-width:1400px;margin:0 auto;padding:18px}
  .vs-head{
    display:flex;align-items:center;justify-content:space-between;
    gap:16px;flex-wrap:wrap;margin-bottom:18px
  }
  .vs-title{
    font-size:28px;font-weight:950;color:#0f172a;line-height:1
  }
  .vs-sub{
    margin-top:6px;font-size:13px;color:#64748b;font-weight:700
  }

  .vs-btn{
    height:44px;border:none;border-radius:16px;
    padding:0 18px;
    display:inline-flex;align-items:center;gap:10px;
    background:linear-gradient(135deg,#0f172a,#0b4f7d);
    color:white;font-weight:900;
    text-decoration:none;
    box-shadow:0 12px 30px rgba(15,23,42,.15);
    transition:.2s ease;
  }
  .vs-btn:hover{
    transform:translateY(-2px);
    color:white;
  }

  .panel{
    background:white;
    border-radius:28px;
    border:1px solid #e2e8f0;
    box-shadow:0 18px 50px rgba(15,23,42,.07);
    overflow:hidden;
  }

  .toolbar{
    padding:18px;
    border-bottom:1px solid #e2e8f0;
    display:flex;
    justify-content:space-between;
    gap:14px;
    flex-wrap:wrap;
  }

  .search{
    width:320px;
    max-width:100%;
    height:46px;
    border-radius:16px;
    border:1px solid #dbe2ea;
    padding:0 16px;
    font-weight:700;
    outline:none;
  }

  .table-wrap{
    overflow:auto;
  }

  table{
    width:100%;
    border-collapse:collapse;
  }

  thead{
    background:#f8fafc;
  }

  th{
    padding:16px;
    text-align:left;
    font-size:12px;
    text-transform:uppercase;
    letter-spacing:.08em;
    color:#64748b;
    font-weight:900;
    white-space:nowrap;
  }

  td{
    padding:16px;
    border-top:1px solid #edf2f7;
    vertical-align:middle;
  }

  tr:hover{
    background:#fafcff;
  }

  .name{
    font-weight:900;
    color:#0f172a;
  }

  .muted{
    color:#64748b;
    font-size:12px;
    font-weight:700;
    margin-top:4px;
  }

  .badge{
    display:inline-flex;
    padding:6px 12px;
    border-radius:999px;
    font-size:11px;
    font-weight:900;
  }

  .b-green{
    background:#dcfce7;
    color:#166534;
  }

  .b-red{
    background:#fee2e2;
    color:#991b1b;
  }

  .actions{
    display:flex;
    gap:8px;
  }

  .btn-icon{
    width:38px;
    height:38px;
    border:none;
    border-radius:14px;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    transition:.2s ease;
  }

  .btn-icon:hover{
    transform:translateY(-2px);
  }
.btn-edit{
  background:#eff6ff;
  color:#2563eb;
  border:1px solid #bfdbfe;
}

.btn-edit:hover{
  background:#dbeafe;
}

.btn-toggle{
  background:#f8fafc;
  color:#475569;
  border:1px solid #e2e8f0;
}

.btn-toggle:hover{
  background:#f1f5f9;
}

  .empty{
    padding:40px;
    text-align:center;
    color:#64748b;
    font-weight:800;
  }

  .modal-backdrop{
    position:fixed;
    inset:0;
    background:rgba(15,23,42,.55);
    z-index:50;
    display:none;
    align-items:center;
    justify-content:center;
    padding:20px;
  }

  .modal{
    width:100%;
    max-width:720px;
    background:white;
    border-radius:30px;
    overflow:hidden;
    box-shadow:0 25px 70px rgba(15,23,42,.25);
  }

  .modal-head{
    padding:20px 24px;
    border-bottom:1px solid #e2e8f0;
  }

  .modal-title{
    font-size:20px;
    font-weight:950;
    color:#0f172a;
  }

  .modal-body{
    padding:24px;
  }

  .grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:16px;
  }

  @media(max-width:700px){
    .grid{
      grid-template-columns:1fr;
    }
  }

  .field label{
    display:block;
    margin-bottom:7px;
    font-size:12px;
    font-weight:900;
    color:#334155;
    text-transform:uppercase;
    letter-spacing:.06em;
  }

  .field input,
  .field textarea{
    width:100%;
    border:1px solid #dbe2ea;
    border-radius:16px;
    padding:12px 14px;
    font-weight:700;
    outline:none;
  }

  .field textarea{
    min-height:100px;
    resize:vertical;
  }

  .modal-foot{
    padding:20px 24px;
    border-top:1px solid #e2e8f0;
    display:flex;
    justify-content:flex-end;
    gap:10px;
  }

  .btn-cancel{
    height:44px;
    padding:0 18px;
    border:none;
    border-radius:14px;
    background:#e2e8f0;
    font-weight:900;
  }

  .btn-save{
    height:44px;
    padding:0 20px;
    border:none;
    border-radius:14px;
    background:linear-gradient(135deg,#0f172a,#0b4f7d);
    color:white;
    font-weight:900;
  }
</style>
@if(session('ok'))
  <div style="max-width:1400px;margin:18px auto 0;padding:14px 18px;border-radius:16px;background:#dcfce7;color:#166534;font-weight:900;border:1px solid #bbf7d0;">
    {{ session('ok') }}
  </div>
@endif

@if($errors->any())
  <div style="max-width:1400px;margin:18px auto 0;padding:14px 18px;border-radius:16px;background:#fee2e2;color:#991b1b;font-weight:900;border:1px solid #fecaca;">
    @foreach($errors->all() as $error)
      <div>{{ $error }}</div>
    @endforeach
  </div>
@endif
<div class="vs-wrap">

  <div class="vs-head">
    <div>
      <div class="vs-title">Proveedores</div>
      <div class="vs-sub">
        Administración de proveedores y contactos comerciales.
      </div>
    </div>

    <button class="vs-btn" onclick="openCreateModal()">
      Nuevo proveedor
    </button>
  </div>

  <div class="panel">

    <div class="toolbar">
      <form>
        <input
          type="text"
          name="q"
          value="{{ $q }}"
          class="search"
          placeholder="Buscar proveedor..."
        >
      </form>
    </div>

    <div class="table-wrap">

      <table>
        <thead>
          <tr>
            <th>Proveedor</th>
            <th>Contacto</th>
            <th>RUC</th>
            <th>Teléfono</th>
            <th>Estado</th>
            <th width="120"></th>
          </tr>
        </thead>

        <tbody>

          @forelse($proveedores as $p)
            <tr>

              <td>
                <div class="name">{{ $p->nombre }}</div>
                <div class="muted">
                  {{ $p->email ?: 'Sin email' }}
                </div>
              </td>

              <td>
                <div class="name">
                  {{ $p->contacto ?: '—' }}
                </div>
                <div class="muted">
                  {{ $p->direccion ?: 'Sin dirección' }}
                </div>
              </td>

              <td>
                <div class="name">
                  {{ $p->ruc ?: '—' }}
                </div>
              </td>

              <td>
                <div class="name">
                  {{ $p->telefono ?: '—' }}
                </div>
              </td>

              <td>
                @if($p->activo)
                  <span class="badge b-green">Activo</span>
                @else
                  <span class="badge b-red">Inactivo</span>
                @endif
              </td>

              <td>
                <div class="actions">

               <button
  class="btn-icon btn-edit"
  onclick='editProveedor(@json($p))'
>
  <svg xmlns="http://www.w3.org/2000/svg"
       width="18"
       height="18"
       viewBox="0 0 24 24"
       fill="none"
       stroke="currentColor"
       stroke-width="2"
       stroke-linecap="round"
       stroke-linejoin="round">

    <path d="M12 20h9"/>
    <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4Z"/>

  </svg>
</button>

<form
  method="POST"
  action="{{ route('admin.proveedores.toggle', $p) }}"
>
  @csrf

  <button class="btn-icon btn-toggle">

    @if($p->activo)

      <svg xmlns="http://www.w3.org/2000/svg"
           width="18"
           height="18"
           viewBox="0 0 24 24"
           fill="none"
           stroke="currentColor"
           stroke-width="2"
           stroke-linecap="round"
           stroke-linejoin="round">

        <circle cx="12" cy="12" r="10"/>
        <path d="M15 9l-6 6"/>
        <path d="M9 9l6 6"/>

      </svg>

    @else

      <svg xmlns="http://www.w3.org/2000/svg"
           width="18"
           height="18"
           viewBox="0 0 24 24"
           fill="none"
           stroke="currentColor"
           stroke-width="2"
           stroke-linecap="round"
           stroke-linejoin="round">

        <circle cx="12" cy="12" r="10"/>
        <path d="M8 12h8"/>

      </svg>

    @endif

  </button>
</form>

                </div>
              </td>

            </tr>
          @empty
            <tr>
              <td colspan="6">
                <div class="empty">
                  No hay proveedores registrados.
                </div>
              </td>
            </tr>
          @endforelse

        </tbody>
      </table>

    </div>

    <div style="padding:18px">
      {{ $proveedores->links() }}
    </div>

  </div>
</div>

{{-- MODAL --}}
<div class="modal-backdrop" id="modalProveedor">

  <div class="modal">

    <form
      method="POST"
      id="formProveedor"
      action="{{ route('admin.proveedores.store') }}"
    >
      @csrf

      <input type="hidden" name="_method" id="methodField" value="POST">

      <div class="modal-head">
        <div class="modal-title" id="modalTitle">
          Nuevo proveedor
        </div>
      </div>

      <div class="modal-body">

        <div class="grid">

          <div class="field">
            <label>Código</label>
            <input type="text" name="codigo" id="codigo">
          </div>

          <div class="field">
            <label>Nombre *</label>
            <input type="text" name="nombre" id="nombre" required>
          </div>

          <div class="field">
            <label>RUC</label>
            <input type="text" name="ruc" id="ruc">
          </div>

          <div class="field">
            <label>DV</label>
            <input type="text" name="dv" id="dv">
          </div>

          <div class="field">
            <label>Teléfono</label>
            <input type="text" name="telefono" id="telefono">
          </div>

          <div class="field">
            <label>Email</label>
            <input type="email" name="email" id="email">
          </div>

          <div class="field">
            <label>Contacto</label>
            <input type="text" name="contacto" id="contacto">
          </div>

          <div class="field">
            <label>Dirección</label>
            <textarea name="direccion" id="direccion"></textarea>
          </div>

        </div>

      </div>

      <div class="modal-foot">
        <button
          type="button"
          class="btn-cancel"
          onclick="closeModal()"
        >
          Cancelar
        </button>

        <button class="btn-save">
          Guardar proveedor
        </button>
      </div>

    </form>

  </div>
</div>

<script>
  const modal = document.getElementById('modalProveedor');

  function openCreateModal() {

    document.getElementById('modalTitle').innerText =
      'Nuevo proveedor';

    document.getElementById('formProveedor').reset();

    document.getElementById('formProveedor').action =
      "{{ route('admin.proveedores.store') }}";

    document.getElementById('methodField').value = 'POST';

    modal.style.display = 'flex';
  }

  function editProveedor(p) {

    document.getElementById('modalTitle').innerText =
      'Editar proveedor';

    document.getElementById('codigo').value = p.codigo ?? '';
    document.getElementById('nombre').value = p.nombre ?? '';
    document.getElementById('ruc').value = p.ruc ?? '';
    document.getElementById('dv').value = p.dv ?? '';
    document.getElementById('telefono').value = p.telefono ?? '';
    document.getElementById('email').value = p.email ?? '';
    document.getElementById('contacto').value = p.contacto ?? '';
    document.getElementById('direccion').value = p.direccion ?? '';

    document.getElementById('formProveedor').action =
      `/proveedores/${p.id}`;

    document.getElementById('methodField').value = 'PUT';

    modal.style.display = 'flex';
  }

  function closeModal() {
    modal.style.display = 'none';
  }

  window.addEventListener('click', function(e) {
    if (e.target === modal) {
      closeModal();
    }
  });
</script>

@endsection