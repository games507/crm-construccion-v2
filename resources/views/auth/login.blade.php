@extends('layouts.base')
@section('title','Iniciar sesión')

@section('content')
<div class="auth-wrap" style="
  min-height:calc(100vh - 120px);
  display:flex;
  align-items:center;
  justify-content:center;
  background:
    radial-gradient(900px 400px at 10% -10%, rgba(37,99,235,.15), transparent),
    radial-gradient(700px 300px at 90% 10%, rgba(14,165,233,.15), transparent);
">

  <div class="card" style="width:420px;max-width:92%;">

    {{-- Header --}}
    <div style="text-align:center;margin-bottom:22px;">
      <div style="
        width:54px;height:54px;
        margin:0 auto 10px;
        border-radius:14px;
        background:#2563eb;
        color:#fff;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:26px;
        font-weight:800;
      ">
        C
      </div>
      <h2 style="margin:0;">CRM Construcción</h2>
      <div style="color:#64748b;font-size:14px;">
        Acceso administrativo
      </div>
    </div>

    {{-- Errores --}}
    @if ($errors->any())
      <div style="
        background:#fee;
        border:1px solid #fbb;
        color:#900;
        padding:10px 12px;
        border-radius:10px;
        margin-bottom:14px;
        font-size:14px;
      ">
        {{ $errors->first() }}
      </div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('login') }}">
      @csrf

      {{-- Email --}}
      <div style="margin-bottom:14px;">
        <label style="font-size:13px;font-weight:700;color:#334155;">
          Correo electrónico
        </label>
        <input
          class="input"
          type="email"
          name="email"
          value="{{ old('email') }}"
          required
          autofocus
          placeholder="admin@demo.com"
        >
      </div>

      {{-- Password --}}
      <div style="margin-bottom:18px;">
        <label style="font-size:13px;font-weight:700;color:#334155;">
          Contraseña
        </label>
        <input
          class="input"
          type="password"
          name="password"
          required
          placeholder="••••••••"
        >
      </div>

      {{-- Acción --}}
      <button class="btn" type="submit" style="width:100%;">
        Ingresar al sistema
      </button>
    </form>

    {{-- Footer --}}
    <div style="
      text-align:center;
      margin-top:16px;
      font-size:12px;
      color:#94a3b8;
    ">
      © {{ date('Y') }} · ERP Construcción
    </div>

  </div>
</div>
@endsection
