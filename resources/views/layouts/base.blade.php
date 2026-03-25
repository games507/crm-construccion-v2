<!doctype html>
<html lang="es" x-data>
<head>
  <meta charset="utf-8">
  <title>@yield('title','CRM Construcción')</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @vite(['resources/css/app.css','resources/js/app.js'])
  @stack('head')
  <style>
@keyframes bellRing {
  0%, 100% { transform: rotate(0deg); }
  5% { transform: rotate(12deg); }
  10% { transform: rotate(-10deg); }
  15% { transform: rotate(8deg); }
  20% { transform: rotate(-6deg); }
  25% { transform: rotate(4deg); }
  30% { transform: rotate(0deg); }
}
</style>
</head>

<body class="min-h-screen bg-[linear-gradient(180deg,#f1f5f9_0%,#e2e8f0_100%)] text-slate-900">

@php
  use App\Support\EmpresaScope;

  $user = auth()->user();

  $isSuperAdmin = false;
  if ($user) {
    if (method_exists($user, 'hasRole')) {
      $isSuperAdmin = $user->hasRole('SuperAdmin') || $user->hasRole('Super Admin');
    } elseif (isset($user->is_superadmin)) {
      $isSuperAdmin = (bool) $user->is_superadmin;
    }
  }

  $hasCtx = EmpresaScope::has();
@endphp

<div class="h-screen w-full flex overflow-hidden">

  @auth
    @include('partials.sidebar')
  @endauth

  <main class="flex-1 min-w-0 min-h-0 flex flex-col">

    <header class="sticky top-0 z-20 bg-white/80 backdrop-blur-xl border-b border-slate-200 shadow-sm">
      <div class="h-16 px-4 lg:px-6 flex items-center justify-between gap-3 min-w-0">

        <div class="text-sm text-slate-600 truncate min-w-0 font-semibold">
          @yield('breadcrumb','Panel / ' . trim($__env->yieldContent('title','Dashboard')))
        </div>

        <div class="flex items-center gap-3 shrink-0">

          {{-- Campana de notificaciones --}}
          @auth
            @php
              $notificacionesNoLeidas = auth()->user()->unreadNotifications()->latest()->take(8)->get();
              $totalNoLeidas = auth()->user()->unreadNotifications()->count();
            @endphp

            <div class="relative" x-data="{ open:false }">
              <button
                type="button"
                @click="open=!open"
                class="relative inline-flex items-center justify-center h-12 w-12 rounded-2xl bg-white border border-slate-200 shadow-sm hover:bg-slate-50 hover:shadow-md transition {{ $totalNoLeidas > 0 ? 'animate-[bellRing_1.8s_ease-in-out_infinite]' : '' }}"
                title="Notificaciones"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082A23.848 23.848 0 0 0 18 15.75V11.25a6 6 0 1 0-12 0v4.5c0 .681-.287 1.332-.79 1.787A23.848 23.848 0 0 0 8.143 17.082m6.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>

                @if($totalNoLeidas > 0)
                 <span class="absolute -top-2 -right-2 flex items-center justify-center min-w-[22px] h-[22px] px-1.5 rounded-full bg-red-600 text-white text-[11px] font-bold shadow-lg ring-2 ring-white animate-pulse">
                    {{ $totalNoLeidas > 99 ? '99+' : $totalNoLeidas }}
                  </span>
                @endif
              </button>

              <div
                x-show="open"
                @click.outside="open=false"
                x-transition
                class="absolute right-0 mt-2 w-96 max-w-[90vw] rounded-2xl border border-slate-200 bg-white shadow-xl overflow-hidden z-50"
                style="display:none;"
              >
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                  <div class="font-bold text-slate-800">Notificaciones</div>

                  @if($totalNoLeidas > 0)
                    <form method="POST" action="{{ route('notificaciones.leer_todas') }}">
                      @csrf
                      <button class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                        Marcar todas
                      </button>
                    </form>
                  @endif
                </div>

                <div class="max-h-96 overflow-y-auto">
                  @forelse($notificacionesNoLeidas as $n)
                    <div class="px-4 py-3 border-b border-slate-100 hover:bg-slate-50">
                      <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                          <div class="text-sm font-bold text-slate-800">
                            {{ $n->data['titulo'] ?? 'Notificación' }}
                          </div>
                          <div class="text-sm text-slate-600 mt-1">
                            {{ $n->data['mensaje'] ?? '' }}
                          </div>
                          <div class="text-xs text-slate-400 mt-1">
                            {{ $n->created_at?->diffForHumans() }}
                          </div>
                        </div>

                        <form method="POST" action="{{ route('notificaciones.leer', $n->id) }}">
                          @csrf
                          <button class="text-xs font-semibold text-slate-500 hover:text-slate-700">
                            Leer
                          </button>
                        </form>
                      </div>
                    </div>
                  @empty
                    <div class="px-4 py-6 text-sm text-slate-500">
                      No tienes notificaciones nuevas.
                    </div>
                  @endforelse
                </div>
              </div>
            </div>
          @endauth

          {{-- Badges --}}
          @if($isSuperAdmin)
            <div class="hidden md:flex items-center gap-2">
              <span class="text-xs font-extrabold px-3 py-1 rounded-full bg-indigo-600 text-white shadow">
                Super Admin
              </span>

              <span class="text-xs font-extrabold px-3 py-1 rounded-full border
                {{ $hasCtx ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-amber-100 text-amber-700 border-amber-200' }}">
                {{ $hasCtx ? 'Empresa activa' : 'Sin contexto' }}
              </span>
            </div>
          @endif

          {{-- User --}}
          @auth
            <div class="hidden sm:flex items-center gap-3 bg-white border border-slate-200 rounded-full px-3 py-1 shadow-sm hover:shadow transition">
              <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-600 to-indigo-800 text-white grid place-items-center font-bold shadow">
                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
              </div>
              <div class="leading-tight">
                <div class="text-sm font-bold text-slate-800">{{ $user->name }}</div>
                <div class="text-xs text-slate-500">{{ $user->email }}</div>
              </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-semibold bg-slate-900 text-white hover:bg-slate-800 shadow-md transition">
                Salir
              </button>
            </form>
          @endauth

        </div>

      </div>
    </header>

    <div class="flex-1 min-h-0 w-full min-w-0 overflow-y-auto overflow-x-hidden p-4 lg:p-6">
      <div class="max-w-7xl mx-auto">
        <div class="rounded-3xl bg-white shadow-[0_10px_40px_rgba(15,23,42,.08)] border border-slate-200 p-4 lg:p-6">
          @yield('content')
        </div>
      </div>
    </div>

  </main>
</div>

@stack('scripts')
</body>
</html>