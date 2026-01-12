<!doctype html>
<html lang="es" x-data>
<head>
  <meta charset="utf-8">
  <title>@yield('title','CRM Construcción')</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @vite(['resources/css/app.css','resources/js/app.js'])
  @stack('head')
</head>

<body class="min-h-screen bg-gradient-to-b from-indigo-50 via-slate-50 to-slate-100 text-slate-900">
@php
  use App\Support\EmpresaScope;

  $user = auth()->user();

  // ✅ NO dependas del nombre exacto del rol
  $isSuperAdmin = false;
  if ($user) {
    if (method_exists($user, 'hasRole')) {
      $isSuperAdmin = $user->hasRole('SuperAdmin') || $user->hasRole('Super Admin');
    } elseif (isset($user->is_superadmin)) {
      $isSuperAdmin = (bool) $user->is_superadmin;
    }
  }

  $empresaCtxId = EmpresaScope::getId();
  $hasCtx = EmpresaScope::has();
@endphp

{{-- ✅ Wrapper pantalla completa + evita bugs de scroll en flex --}}
<div class="h-screen w-full flex overflow-hidden">

  {{-- SIDEBAR --}}
  @auth
    @include('partials.sidebar')
  @endauth

  {{-- MAIN --}}
  {{-- ✅ min-h-0 es CLAVE para que el hijo (content) pueda scrollear en flex --}}
  <main class="flex-1 min-w-0 min-h-0 flex flex-col">

    {{-- HEADER --}}
    <header class="sticky top-0 z-20 bg-white/70 backdrop-blur border-b border-slate-900/10">
      <div class="h-16 px-4 lg:px-6 flex items-center justify-between gap-3 min-w-0">

        <div class="text-sm text-slate-500 truncate min-w-0">
          @yield('breadcrumb','Panel / ' . trim($__env->yieldContent('title','Dashboard')))
        </div>

        <div class="flex items-center gap-3 shrink-0">

          {{-- Badges Super Admin --}}
          @if($isSuperAdmin)
            <div class="hidden md:flex items-center gap-2">
              <span class="text-xs font-extrabold px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100">
                Modo Super Admin
              </span>

              <span class="text-xs font-extrabold px-3 py-1 rounded-full border
                {{ $hasCtx ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-amber-50 text-amber-700 border-amber-100' }}">
                Contexto: {{ $hasCtx ? 'Empresa seleccionada' : 'Todas' }}
              </span>
            </div>
          @endif

          {{-- User pill --}}
          @auth
            <div class="hidden sm:flex items-center gap-3 bg-white border border-slate-900/10 rounded-full px-3 py-1 shadow-sm">
              <div class="w-8 h-8 rounded-full bg-indigo-600 text-white grid place-items-center font-bold">
                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
              </div>
              <div class="leading-tight">
                <div class="text-sm font-semibold">{{ $user->name }}</div>
                <div class="text-xs text-slate-500">{{ $user->email }}</div>
              </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-semibold
                             bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm">
                Salir
              </button>
            </form>
          @endauth

        </div>

      </div>
    </header>

    {{-- CONTENT --}}
    {{-- ✅ Aquí es donde se arregla el scroll: overflow-y-auto + min-h-0 --}}
    <div class="flex-1 min-h-0 w-full min-w-0 overflow-y-auto overflow-x-hidden p-4 lg:p-6">
      @yield('content')
    </div>

  </main>
</div>

@stack('scripts')
</body>
</html>
