@extends('layouts.auth')
@section('title','Iniciar sesión')

@section('content')
<style>
    .inpt-wf:focus{
        border: 2.5px solid #14598d !important;
        background-color: white !important;
    }
</style>

{{-- =========================================================
   CLOUDflare Turnstile
   LOCAL: comentado para que no bloquee el botón de login
   PRODUCCIÓN: descomenta estas 2 líneas
========================================================= --}}
{{--
<link rel="preconnect" href="https://challenges.cloudflare.com">
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
--}}

<div class="fixed inset-0 w-screen h-screen overflow-hidden">

  {{-- FONDO LLENO --}}
  <img
    src="{{ asset('img/login/fondo-login.jpg') }}"
    alt="Fondo"
    class="absolute inset-0 w-full h-full object-cover"
    style="object-position: 60% 65%;"
  >

  {{-- Overlay suave --}}
  <div class="absolute inset-0 bg-white/20"></div>

  {{-- Card a la izquierda --}}
  <div
    class="relative h-full w-full mx-auto px-8 flex items-start justify-start pt-36"
    style="margin: 2% 0% 0% 10%; max-width: 300px; color: white;"
  >
    <div class="w-[320px] max-w-[90vw]">

      <div
        class="rounded-2xl bg-white/55 backdrop-blur-xl border border-white/60 shadow-[0_18px_45px_rgba(15,23,42,.22)] p-5"
        style="background-color: #14598dcc; font-family: 'Open Sans', sans-serif; color: white; box-shadow: 0 0 15px #96c7da;"
      >

        <div style="min-width: 80%; text-align: center; align-items: center;">
          <svg xmlns="http://www.w3.org/2000/svg" width="132" height="114" viewBox="0 0 152 134" style="margin: auto;">
            <rect width="142" height="124" fill="#02202e00"/>
            <path fill="#FFF" d="M 85.00,118.50 L 49.00,118.50 L 47.50,117.00 L 3.50,25.00 L 5.00,23.50 L 36.00,23.50 L 37.50,22.00 L 31.50,10.00 L 33.00,8.50 L 101.00,8.50 L 102.50,10.00 L 68.00,82.50 L 65.50,81.00 L 47.50,42.00 L 46.00,40.50 L 32.00,40.50 L 30.50,42.00 L 58.50,100.00 L 60.00,101.50 L 75.00,101.50 L 84.50,85.00 L 119.50,10.00 L 121.00,8.50 L 137.00,8.50 L 138.50,10.00 L 95.50,101.00 L 94.00,102.50 L 79.00,102.50 L 77.50,104.00 L 85.50,115.00 L 86.50,117.00 L 85.00,118.50 Z" fill="#0adff7"/>
          </svg>

          <h3 style="font-family: 'Open Sans', sans-serif; font-weight: 800;">VERTICE SOFT</h3>
          <h3 style="font-family: 'Open Sans', sans-serif; font-weight: 500;">TECHNOLOGY</h3>
        </div>

        <div class="text-xl font-semibold mt-1" style="text-align: center; margin-top: 1rem">
          Acceso al sistema
        </div>

        @if ($errors->any())
          <div class="mt-3 rounded-xl border border-red-200 bg-red-50/80 px-3 py-2 text-sm font-bold text-red-800">
            {{ $errors->first() }}
          </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="mt-4 space-y-3">
          @csrf

          <div>
            <label class="font-semibold mb-1" style="color: white; margin-bottom: 10px; display:block;">
              Correo electrónico
            </label>
            <input
              type="email"
              name="email"
              value="{{ old('email') }}"
              required
              autofocus
              class="block w-full h-10 rounded-xl inpt-wf border border-slate-200/80 bg-white/80 px-3 text-sm font-semibold text-slate-900 focus:outline-none focus:ring-4 focus:border-indigo-400"
            >
          </div>

          <div style="margin-top: 20px">
            <label class="font-semibold mb-1" style="margin-bottom: 10px; display:block;">
              Contraseña
            </label>
            <input
              type="password"
              name="password"
              required
              class="block w-full h-10 rounded-xl inpt-wf border border-slate-300/80 bg-white/80 px-3 text-sm font-semibold text-slate-900 focus:outline-none focus:ring-4 focus:border-indigo-400"
            >
          </div>

          {{-- =========================================================
             TURNSTILE
             LOCAL: comentado
             PRODUCCIÓN: descomenta este bloque completo
          ========================================================= --}}
          {{--
          <div style="margin-top: 16px; display:flex; justify-content:center;">
            <div
              class="cf-turnstile"
              data-sitekey="{{ env('TURNSTILE_SITE_KEY') }}"
              data-callback="tsOk"
              data-expired-callback="tsReset"
              data-error-callback="tsReset">
            </div>
          </div>
          --}}

          <button
            id="btnLogin"
            type="submit"
            class="block w-full h-10 rounded-xl bg-slate-900 text-white text-sm font-extrabold hover:bg-slate-800 transition"
            style="margin-top: 20px; background-color: #0e3451;"
          >
            Ingresar
          </button>
        </form>

        <div class="mt-4 text-[11px] font-semibold">
          © {{ date('Y') }} · VERTICESOFT
        </div>

      </div>
    </div>
  </div>

</div>

{{-- =========================================================
   JS de Turnstile
   LOCAL: comentado
   PRODUCCIÓN: descomenta este bloque completo
========================================================= --}}
{{--
<script>
  function tsOk() {
    const btn = document.getElementById('btnLogin');
    if (btn) btn.disabled = false;
  }

  function tsReset() {
    const btn = document.getElementById('btnLogin');
    if (btn) btn.disabled = true;
  }

  document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('btnLogin');
    if (btn) btn.disabled = true;
  });
</script>
--}}
@endsection