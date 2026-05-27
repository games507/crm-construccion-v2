@extends('layouts.base')
@section('title','Nueva orden de compra')

@section('content')

@php

  $today = now()->format('Y-m-d');
@endphp

<style>
  .vs-wrap{max-width:1400px;margin:0 auto;padding:18px}
  .vs-head{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:18px}
  .vs-title{font-size:28px;font-weight:950;color:#0f172a;line-height:1}
  .vs-sub{margin-top:6px;font-size:13px;color:#64748b;font-weight:700}
  .panel{background:white;border-radius:28px;border:1px solid #e2e8f0;box-shadow:0 18px 50px rgba(15,23,42,.07);overflow:hidden;margin-bottom:16px}
  .panel-head{padding:18px;border-bottom:1px solid #e2e8f0}
  .panel-title{font-weight:950;color:#0f172a;font-size:16px}
  .panel-sub{font-size:12px;color:#64748b;margin-top:4px;font-weight:700}
  .panel-body{padding:18px}
  .grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
  @media(max-width:1000px){.grid{grid-template-columns:repeat(2,1fr)}}
  @media(max-width:650px){.grid{grid-template-columns:1fr}.vs-wrap{padding:12px}}
  .field label{display:block;margin-bottom:7px;font-size:12px;font-weight:900;color:#334155;text-transform:uppercase;letter-spacing:.06em}
  .field input,.field select,.field textarea{width:100%;height:46px;border:1px solid #dbe2ea;border-radius:16px;padding:0 14px;font-weight:750;outline:none;background:white}
  .field textarea{height:96px;padding:12px 14px;resize:vertical}
  .span-2{grid-column:span 2}.span-4{grid-column:span 4}
  @media(max-width:650px){.span-2,.span-4{grid-column:span 1}}
  .btn{height:44px;border:none;border-radius:16px;padding:0 18px;display:inline-flex;align-items:center;justify-content:center;gap:10px;font-weight:900;text-decoration:none;cursor:pointer;transition:.2s ease}
  .btn:hover{transform:translateY(-2px)}
  .btn-primary{background:linear-gradient(135deg,#0f172a,#0b4f7d);color:white;box-shadow:0 12px 30px rgba(15,23,42,.15)}
  .btn-light{background:#f1f5f9;color:#334155;border:1px solid #e2e8f0}
  .btn-danger{background:#fee2e2;color:#991b1b;border:1px solid #fecaca}
  .table-wrap{overflow:auto}
  table{width:100%;border-collapse:collapse}
  thead{background:#f8fafc}
  th{padding:14px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#64748b;font-weight:950;white-space:nowrap}
  td{padding:12px;border-top:1px solid #edf2f7;vertical-align:top}
  .item-input{width:100%;height:42px;border:1px solid #dbe2ea;border-radius:14px;padding:0 12px;font-weight:750;outline:none;background:white}
  .item-total{font-weight:950;color:#0f172a;white-space:nowrap}
  .totals{display:flex;justify-content:flex-end;margin-top:18px}
  .totals-card{width:360px;max-width:100%;border:1px solid #e2e8f0;border-radius:22px;background:#f8fafc;padding:16px}
  .tot-row{display:flex;justify-content:space-between;gap:12px;padding:9px 0;font-size:14px;font-weight:850;color:#334155;border-bottom:1px solid #e2e8f0}
  .tot-row:last-child{border-bottom:none}
  .grand{font-size:20px;color:#0f172a;font-weight:950}
  .errors{margin-bottom:14px;padding:14px 18px;border-radius:16px;background:#fee2e2;color:#991b1b;font-weight:900;border:1px solid #fecaca}
</style>

<div class="vs-wrap">

  @if($errors->any())
    <div class="errors">
      @foreach($errors->all() as $error)
        <div>{{ $error }}</div>
      @endforeach
    </div>
  @endif

  <div class="vs-head">
    <div>
      <div class="vs-title">Nueva orden de compra</div>
      <div class="vs-sub">Registra compras por proveedor, proyecto y materiales.</div>
    </div>

    <a href="{{ route('admin.ordenes_compra.index') }}" class="btn btn-light">
      Volver
    </a>
  </div>

  <form method="POST" action="{{ route('admin.ordenes_compra.store') }}" id="ocForm">
    @csrf

    <div class="panel">
      <div class="panel-head">
        <div class="panel-title">Datos generales</div>
        <div class="panel-sub">Información principal de la orden.</div>
      </div>

      <div class="panel-body">
        <div class="grid">
          <div class="field">
            <label>Número</label>
            <input type="text" name="numero" value="{{ old('numero', $numero) }}" required>
          </div>

          <div class="field">
            <label>Fecha</label>
            <input type="date" name="fecha" value="{{ old('fecha', $today) }}" required>
          </div>

          <div class="field">
            <label>Fecha entrega</label>
            <input type="date" name="fecha_entrega" value="{{ old('fecha_entrega') }}">
          </div>

          <div class="field">
            <label>Estado</label>
            <select name="estado" required>
              @foreach(['borrador','solicitada','aprobada','recibida','parcial','cancelada'] as $e)
                <option value="{{ $e }}" @selected(old('estado','borrador') === $e)>
                  {{ ucfirst($e) }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="field span-2">
            <label>Proveedor *</label>
            
            <select name="proveedor_id" required>
              <option value="">Seleccione proveedor</option>
              @foreach($proveedores as $p)
                <option value="{{ $p->id }}" @selected(old('proveedor_id') == $p->id)>
                  {{ $p->nombre }}
                </option>

                
              @endforeach
            </select>
          </div>
<div class="field span-2">
  <label>Almacén destino</label>

  <select name="almacen_destino_id">
    <option value="">Seleccione almacén</option>

    @foreach($almacenes as $a)
      <option
        value="{{ $a->id }}"
        @selected(old('almacen_destino_id') == $a->id)
      >
        {{ $a->codigo }} - {{ $a->nombre }}
      </option>
    @endforeach

  </select>
</div>
          <div class="field span-2">
            <label>Proyecto</label>
            <select name="proyecto_id">
              <option value="">Sin proyecto</option>
              @foreach($proyectos as $p)
                <option value="{{ $p->id }}" @selected(old('proyecto_id') == $p->id)>
                  {{ $p->codigo }} - {{ $p->nombre }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="field span-4">
            <label>Observación</label>
            <textarea name="observacion" placeholder="Notas internas de la compra...">{{ old('observacion') }}</textarea>
          </div>
        </div>
      </div>
    </div>

    <div class="panel">
      <div class="panel-head" style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap">
        <div>
          <div class="panel-title">Items de la orden</div>
          <div class="panel-sub">Agrega materiales, cantidades y precios.</div>
        </div>

        <button type="button" class="btn btn-light" onclick="addRow()">
          Agregar item
        </button>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th style="min-width:220px">Material</th>
              <th style="min-width:260px">Descripción</th>
              <th style="min-width:120px">Cantidad</th>
              <th style="min-width:140px">Precio</th>
              <th style="min-width:120px">Impuesto</th>
              <th style="min-width:120px">Descuento</th>
              <th style="min-width:120px">Total</th>
              <th width="70"></th>
            </tr>
          </thead>

          <tbody id="itemsBody"></tbody>
        </table>
      </div>

      <div class="panel-body">
        <div class="totals">
          <div class="totals-card">
            <div class="tot-row">
              <span>Subtotal</span>
              <span id="subtotalTxt">$0.00</span>
            </div>
            <div class="tot-row">
              <span>Impuesto</span>
              <span id="impuestoTxt">$0.00</span>
            </div>
            <div class="tot-row">
              <span>Descuento</span>
              <span id="descuentoTxt">$0.00</span>
            </div>
            <div class="tot-row grand">
              <span>Total</span>
              <span id="totalTxt">$0.00</span>
            </div>
          </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:10px;flex-wrap:wrap;margin-top:18px">
          <a href="{{ route('admin.ordenes_compra.index') }}" class="btn btn-light">Cancelar</a>
          <button class="btn btn-primary">Guardar orden</button>
        </div>
      </div>
    </div>

  </form>
</div>

<script>
  const materiales = @json($materiales);
  let rowIndex = 0;

  function money(n) {
    return '$' + Number(n || 0).toFixed(2);
  }

  function materialOptions() {
    let html = '<option value="">Manual / sin material</option>';

    materiales.forEach(m => {
      const nombre = m.descripcion || m.nombre || 'Material';
      html += `<option value="${m.id}" data-desc="${escapeHtml(nombre)}">${escapeHtml(nombre)}</option>`;
    });

    return html;
  }

  function escapeHtml(text) {
    return String(text ?? '')
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  function addRow(data = {}) {
    const tbody = document.getElementById('itemsBody');
    const i = rowIndex++;

    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>
        <select class="item-input material-select" name="items[${i}][material_id]" onchange="materialChanged(this)">
          ${materialOptions()}
        </select>
      </td>

      <td>
        <input class="item-input desc-input" name="items[${i}][descripcion]" value="${escapeHtml(data.descripcion || '')}" required>
      </td>

      <td>
        <input class="item-input calc" type="number" step="0.0001" min="0.0001" name="items[${i}][cantidad]" value="${data.cantidad || 1}" required>
      </td>

      <td>
        <input class="item-input calc" type="number" step="0.0001" min="0" name="items[${i}][precio_unitario]" value="${data.precio_unitario || 0}" required>
      </td>

      <td>
        <input class="item-input calc" type="number" step="0.01" min="0" name="items[${i}][impuesto]" value="${data.impuesto || 0}">
      </td>

      <td>
        <input class="item-input calc" type="number" step="0.01" min="0" name="items[${i}][descuento]" value="${data.descuento || 0}">
      </td>

      <td>
        <div class="item-total">$0.00</div>
      </td>

      <td>
        <button type="button" class="btn btn-danger" style="width:42px;padding:0" onclick="removeRow(this)">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 6h18"/>
            <path d="M8 6V4h8v2"/>
            <path d="M19 6l-1 14H6L5 6"/>
            <path d="M10 11v6"/>
            <path d="M14 11v6"/>
          </svg>
        </button>
      </td>
    `;

    tbody.appendChild(tr);

    tr.querySelectorAll('.calc').forEach(input => {
      input.addEventListener('input', calcularTotales);
    });

    calcularTotales();
  }

  function materialChanged(select) {
    const tr = select.closest('tr');
    const opt = select.options[select.selectedIndex];

    if (opt && opt.dataset.desc) {
      tr.querySelector('.desc-input').value = opt.dataset.desc;
    }
  }

  function removeRow(btn) {
    const tbody = document.getElementById('itemsBody');

    if (tbody.children.length <= 1) {
      alert('La orden debe tener al menos un item.');
      return;
    }

    btn.closest('tr').remove();
    calcularTotales();
  }

  function calcularTotales() {
    let subtotal = 0;
    let impuestoTotal = 0;
    let descuentoTotal = 0;
    let totalGeneral = 0;

    document.querySelectorAll('#itemsBody tr').forEach(tr => {
      const cantidad = parseFloat(tr.querySelector('[name*="[cantidad]"]').value || 0);
      const precio = parseFloat(tr.querySelector('[name*="[precio_unitario]"]').value || 0);
      const impuesto = parseFloat(tr.querySelector('[name*="[impuesto]"]').value || 0);
      const descuento = parseFloat(tr.querySelector('[name*="[descuento]"]').value || 0);

      const sub = cantidad * precio;
      const total = (sub + impuesto) - descuento;

      subtotal += sub;
      impuestoTotal += impuesto;
      descuentoTotal += descuento;
      totalGeneral += total;

      tr.querySelector('.item-total').innerText = money(total);
    });

    document.getElementById('subtotalTxt').innerText = money(subtotal);
    document.getElementById('impuestoTxt').innerText = money(impuestoTotal);
    document.getElementById('descuentoTxt').innerText = money(descuentoTotal);
    document.getElementById('totalTxt').innerText = money(totalGeneral);
  }

  addRow();
</script>
@endsection