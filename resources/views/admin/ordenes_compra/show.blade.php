@extends('layouts.base')
@section('title','Detalle orden de compra')

@section('content')
@php
  $money = fn($v) => '$' . number_format((float)$v, 2);

  $estadoClass = match($ordenCompra->estado) {
    'borrador' => 'b-slate',
    'solicitada' => 'b-blue',
    'aprobada' => 'b-green',
    'recibida' => 'b-cyan',
    'parcial' => 'b-yellow',
    'cancelada' => 'b-red',
    default => 'b-slate',
  };
@endphp

<style>
  .vs-wrap{max-width:1400px;margin:0 auto;padding:18px}
  .vs-head{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:18px}
  .vs-title{font-size:30px;font-weight:950;color:#0f172a;line-height:1}
  .vs-sub{margin-top:6px;font-size:13px;color:#64748b;font-weight:700}

  .btn{
    height:44px;border:none;border-radius:16px;padding:0 18px;
    display:inline-flex;align-items:center;justify-content:center;
    gap:10px;font-weight:900;text-decoration:none;cursor:pointer;
    transition:.2s ease;
  }

  .btn:hover{
    transform:translateY(-2px);
  }

  .btn-primary{
    background:linear-gradient(135deg,#0f172a,#0b4f7d);
    color:white;
    box-shadow:0 12px 30px rgba(15,23,42,.15);
  }

  .btn-light{
    background:#f1f5f9;
    color:#334155;
    border:1px solid #e2e8f0;
  }

  .btn-success{
    background:#dcfce7;
    color:#166534;
    border:1px solid #bbf7d0;
  }

  .btn-danger{
    background:#fee2e2;
    color:#991b1b;
    border:1px solid #fecaca;
  }

  .btn-cyan{
    background:#cffafe;
    color:#155e75;
    border:1px solid #a5f3fc;
  }

  .panel{
    background:white;
    border-radius:28px;
    border:1px solid #e2e8f0;
    box-shadow:0 18px 50px rgba(15,23,42,.07);
    overflow:hidden;
    margin-bottom:18px;
  }

  .panel-head{
    padding:18px;
    border-bottom:1px solid #e2e8f0;
  }

  .panel-title{
    font-weight:950;
    color:#0f172a;
    font-size:16px;
  }

  .panel-sub{
    font-size:12px;
    color:#64748b;
    margin-top:4px;
    font-weight:700;
  }

  .panel-body{
    padding:18px;
  }

  .grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:14px;
  }

  @media(max-width:1000px){
    .grid{
      grid-template-columns:repeat(2,1fr);
    }
  }

  @media(max-width:650px){
    .grid{
      grid-template-columns:1fr;
    }

    .vs-wrap{
      padding:12px;
    }
  }

  .card{
    border:1px solid #e2e8f0;
    border-radius:22px;
    padding:18px;
    background:#f8fafc;
  }

  .label{
    font-size:11px;
    text-transform:uppercase;
    letter-spacing:.08em;
    color:#64748b;
    font-weight:950;
  }

  .value{
    margin-top:8px;
    font-size:18px;
    font-weight:950;
    color:#0f172a;
  }

  .muted{
    margin-top:4px;
    color:#64748b;
    font-size:12px;
    font-weight:700;
  }

  .badge{
    display:inline-flex;
    padding:7px 14px;
    border-radius:999px;
    font-size:11px;
    font-weight:950;
  }

  .b-green{background:#dcfce7;color:#166534}
  .b-red{background:#fee2e2;color:#991b1b}
  .b-blue{background:#dbeafe;color:#1d4ed8}
  .b-yellow{background:#fef3c7;color:#92400e}
  .b-slate{background:#e2e8f0;color:#334155}
  .b-cyan{background:#cffafe;color:#155e75}

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
    padding:14px;
    text-align:left;
    font-size:11px;
    text-transform:uppercase;
    letter-spacing:.08em;
    color:#64748b;
    font-weight:950;
    white-space:nowrap;
  }

  td{
    padding:14px;
    border-top:1px solid #edf2f7;
    vertical-align:middle;
  }

  .item-name{
    font-weight:900;
    color:#0f172a;
  }

  .totals{
    display:flex;
    justify-content:flex-end;
    margin-top:18px;
  }

  .totals-card{
    width:360px;
    max-width:100%;
    border:1px solid #e2e8f0;
    border-radius:22px;
    background:#f8fafc;
    padding:16px;
  }

  .tot-row{
    display:flex;
    justify-content:space-between;
    gap:12px;
    padding:10px 0;
    font-size:14px;
    font-weight:850;
    color:#334155;
    border-bottom:1px solid #e2e8f0;
  }

  .tot-row:last-child{
    border-bottom:none;
  }

  .grand{
    font-size:22px;
    font-weight:950;
    color:#0f172a;
  }

  .ok{
    margin-bottom:14px;
    padding:14px 18px;
    border-radius:16px;
    background:#dcfce7;
    color:#166534;
    font-weight:900;
    border:1px solid #bbf7d0;
  }

  .errors{
    margin-bottom:14px;
    padding:14px 18px;
    border-radius:16px;
    background:#fee2e2;
    color:#991b1b;
    font-weight:900;
    border:1px solid #fecaca;
  }
</style>

<div class="vs-wrap">

  @if(session('ok'))
    <div class="ok">
      {{ session('ok') }}
    </div>
  @endif

  @if($errors->any())
    <div class="errors">
      @foreach($errors->all() as $error)
        <div>{{ $error }}</div>
      @endforeach
    </div>
  @endif

  <div class="vs-head">

    <div>
      <div class="vs-title">
        {{ $ordenCompra->numero }}
      </div>

      <div class="vs-sub">
        Orden de compra registrada en el sistema.
      </div>
    </div>

    <div style="display:flex;gap:10px;flex-wrap:wrap">

      <a
        href="{{ route('admin.ordenes_compra.index') }}"
        class="btn btn-light"
      >
        Volver
      </a>

      @if($ordenCompra->estado !== 'aprobada' && $ordenCompra->estado !== 'cancelada')

        <form
          method="POST"
          action="{{ route('admin.ordenes_compra.aprobar', $ordenCompra) }}"
        >
          @csrf

          <button class="btn btn-success">
            Aprobar
          </button>
        </form>

      @endif

      @if(!in_array($ordenCompra->estado, ['recibida','cancelada']))

        <form
          method="POST"
          action="{{ route('admin.ordenes_compra.recibir', $ordenCompra) }}"
        >
          @csrf

          <button class="btn btn-cyan">
            Marcar como recibida
          </button>
        </form>

      @endif

      @if($ordenCompra->estado !== 'cancelada' && $ordenCompra->estado !== 'recibida')

        <form
          method="POST"
          action="{{ route('admin.ordenes_compra.cancelar', $ordenCompra) }}"
        >
          @csrf

          <button class="btn btn-danger">
            Cancelar
          </button>
        </form>

      @endif

    </div>

  </div>

  

  <div class="panel">

    <div class="panel-head">
      <div class="panel-title">
        Información general
      </div>

      <div class="panel-sub">
        Datos principales de la orden.
      </div>
    </div>

    <div class="panel-body">

      <div class="grid">

        <div class="card">
          <div class="label">Proveedor</div>

          <div class="value">
            {{ $ordenCompra->proveedor->nombre ?? '—' }}
          </div>

          <div class="muted">
            {{ $ordenCompra->proveedor->ruc ?? 'Sin RUC' }}
          </div>
        </div>

        <div class="card">
          <div class="label">Proyecto</div>

          <div class="value">
            {{ $ordenCompra->proyecto->nombre ?? 'Sin proyecto' }}
          </div>

          <div class="muted">
            {{ $ordenCompra->proyecto->codigo ?? '—' }}
          </div>
        </div>

        <div class="card">
          <div class="label">Estado</div>

          <div class="value">
            <span class="badge {{ $estadoClass }}">
              {{ ucfirst($ordenCompra->estado) }}
            </span>
          </div>
        </div>

        <div class="card">
          <div class="label">Fecha</div>

          <div class="value">
            {{ $ordenCompra->fecha?->format('d/m/Y') }}
          </div>

          <div class="muted">
            Entrega:
            {{ $ordenCompra->fecha_entrega ? $ordenCompra->fecha_entrega->format('d/m/Y') : '—' }}
          </div>
        </div>

      </div>

    </div>

  </div>

  <div class="panel">

    <div class="panel-head">
      <div class="panel-title">
        Items de la orden
      </div>

      <div class="panel-sub">
        Materiales y costos registrados.
      </div>
    </div>

    <div class="table-wrap">

      <table>

        <thead>
          <tr>
            <th>Material</th>
            <th>Descripción</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>Impuesto</th>
            <th>Descuento</th>
            <th>Total</th>
          </tr>
        </thead>

        <tbody>

          @foreach($ordenCompra->items as $item)

            <tr>

              <td>
                <div class="item-name">
                  {{ $item->material->descripcion ?? 'Manual' }}
                </div>
              </td>

              <td>
                <div class="item-name">
                  {{ $item->descripcion }}
                </div>
              </td>

              <td>
                {{ number_format($item->cantidad, 4) }}
              </td>

              <td>
                {{ $money($item->precio_unitario) }}
              </td>

              <td>
                {{ $money($item->impuesto) }}
              </td>

              <td>
                {{ $money($item->descuento) }}
              </td>

              <td>
                <strong>
                  {{ $money($item->total) }}
                </strong>
              </td>

            </tr>

          @endforeach

        </tbody>

      </table>

    </div>

    <div class="panel-body">

      <div class="totals">

        <div class="totals-card">

          <div class="tot-row">
            <span>Subtotal</span>
            <span>{{ $money($ordenCompra->subtotal) }}</span>
          </div>

          <div class="tot-row">
            <span>Impuesto</span>
            <span>{{ $money($ordenCompra->impuesto) }}</span>
          </div>

          <div class="tot-row">
            <span>Descuento</span>
            <span>{{ $money($ordenCompra->descuento) }}</span>
          </div>

          <div class="tot-row grand">
            <span>Total</span>
            <span>{{ $money($ordenCompra->total) }}</span>
          </div>

        </div>

      </div>

    </div>

  </div>

  @if($ordenCompra->observacion)

    <div class="panel">

      <div class="panel-head">
        <div class="panel-title">
          Observaciones
        </div>
      </div>

      <div class="panel-body">

        <div style="
          border:1px solid #e2e8f0;
          border-radius:20px;
          padding:18px;
          background:#f8fafc;
          color:#334155;
          font-weight:700;
          line-height:1.7;
        ">
          {{ $ordenCompra->observacion }}
        </div>

      </div>

    </div>

  @endif

</div>
@endsection