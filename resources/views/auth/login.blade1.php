@extends('layouts.auth')
@section('title','Iniciar sesión')

@section('content')
<div class="fixed inset-0 w-screen h-screen overflow-hidden">

  {{-- FONDO LLENO (sin bordes) --}}
  <img
    src="{{ asset('img/login/fondo-login.jpg') }}"
    alt="Fondo"
    class="absolute inset-0 w-full h-full object-cover"
    style="object-position: 60% 65%;"  {{-- mueve el encuadre: más ciudad, menos recorte raro --}}
  >

  {{-- Overlay suave para contraste --}}
  <div class="absolute inset-0 bg-white/20"></div>

  {{-- Card a la izquierda --}}
  <div class="relative h-full w-full max-w-6xl mx-auto px-8 flex items-start justify-start pt-36">
    <div class="w-[320px] max-w-[90vw]">

      <div class="rounded-2xl
                  bg-white/55 backdrop-blur-xl
                  border border-white/60
                  shadow-[0_18px_45px_rgba(15,23,42,.22)]
                  p-5">

        <h1 class="text-xl font-black text-slate-900 leading-tight">
          VERTICESOFT
        </h1>
        <div class="text-xs font-semibold text-slate-600 mt-1">
          Acceso al sistema
        </div>

        @if ($errors->any())
          <div class="mt-3 rounded-xl border border-red-200 bg-red-50/80 px-3 py-2
                      text-sm font-bold text-red-800">
            {{ $errors->first() }}
          </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="mt-4 space-y-3">
          @csrf

          <div>
            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wide mb-1">
              Correo electrónico
            </label>
            <input
              type="email"
              name="email"
              value="{{ old('email') }}"
              required
              autofocus
              class="block w-full h-10 rounded-xl
                     border border-slate-300/80
                     bg-white/80 px-3
                     text-sm font-semibold text-slate-900
                     focus:outline-none focus:ring-4 focus:ring-indigo-200 focus:border-indigo-400">
          </div>

          <div>
            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wide mb-1">
              Contraseña
            </label>
            <input
              type="password"
              name="password"
              required
              class="block w-full h-10 rounded-xl
                     border border-slate-300/80
                     bg-white/80 px-3
                     text-sm font-semibold text-slate-900
                     focus:outline-none focus:ring-4 focus:ring-indigo-200 focus:border-indigo-400">
          </div>

          <button
            type="submit"
            class="block w-full h-10 rounded-xl
                   bg-slate-900 text-white
                   text-sm font-extrabold
                   hover:bg-slate-800 transition">
            Ingresar
          </button>
        </form>

        <div class="mt-4 text-[11px] font-semibold text-slate-600">
          © {{ date('Y') }} · VERTICESOFT
        </div>

      </div>
    </div>
  </div>

</div>
@endsection
