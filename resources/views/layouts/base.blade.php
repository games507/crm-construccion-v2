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

    .notif-btn{
      position:relative;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      width:48px;
      height:48px;
      border-radius:18px;
      border:1px solid rgba(148,163,184,.25);
      background:
        linear-gradient(180deg, rgba(255,255,255,.98), rgba(248,250,252,.96));
      box-shadow:
        0 10px 30px rgba(15,23,42,.08),
        inset 0 1px 0 rgba(255,255,255,.85);
      transition: all .22s ease;
    }

    .notif-btn:hover{
      transform: translateY(-1px);
      box-shadow:
        0 14px 36px rgba(15,23,42,.12),
        inset 0 1px 0 rgba(255,255,255,.92);
      border-color: rgba(99,102,241,.22);
    }

    .notif-btn.has-alert .notif-icon{
      animation: bellRing 1.8s ease-in-out infinite;
      transform-origin: top center;
    }

    .notif-badge{
      position:absolute;
      top:-6px;
      right:-5px;
      min-width:22px;
      height:22px;
      padding:0 6px;
      border-radius:999px;
      display:flex;
      align-items:center;
      justify-content:center;
      font-size:11px;
      font-weight:800;
      color:#fff;
      background:linear-gradient(180deg,#ef4444 0%, #dc2626 100%);
      box-shadow:0 10px 20px rgba(220,38,38,.28);
      border:2px solid #fff;
      line-height:1;
    }

    .notif-panel{
      width:390px;
      max-width:92vw;
      border-radius:22px;
      border:1px solid rgba(226,232,240,.95);
      background:#fff;
      box-shadow:0 24px 70px rgba(15,23,42,.18);
      overflow:hidden;
    }

    .notif-item{
      transition: background .18s ease, transform .18s ease;
    }

    .notif-item:hover{
      background:#f8fafc;
    }

    .notif-link{
      display:flex;
      align-items:flex-start;
      gap:12px;
      min-width:0;
      flex:1 1 auto;
      text-decoration:none;
      cursor:pointer;
    }

    .notif-link:hover .notif-title{
      color:#4338ca;
    }

    .notif-dot{
      width:10px;
      height:10px;
      border-radius:999px;
      background:linear-gradient(180deg,#6366f1 0%, #4f46e5 100%);
      box-shadow:0 0 0 4px rgba(99,102,241,.10);
      flex:0 0 auto;
      margin-top:5px;
    }

    .notif-pill{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      min-width:24px;
      height:24px;
      padding:0 8px;
      border-radius:999px;
      font-size:11px;
      font-weight:800;
      background:#eef2ff;
      color:#4338ca;
      border:1px solid #c7d2fe;
    }

    .notif-mark-btn{
      border:1px solid rgba(148,163,184,.22);
      background:#fff;
      color:#475569;
      border-radius:12px;
      padding:7px 10px;
      font-size:11px;
      font-weight:800;
      transition:all .18s ease;
      white-space:nowrap;
    }

    .notif-mark-btn:hover{
      background:#f8fafc;
      color:#0f172a;
      border-color: rgba(99,102,241,.20);
    }

    .notif-empty{
      padding:26px 18px;
      text-align:center;
      color:#64748b;
      font-size:13px;
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

  $nombreMostrar = trim((string) (
    $user->name
    ?? $user->nombre
    ?? $user->nombre_completo
    ?? 'Usuario'
  ));

  $partes = preg_split('/\s+/', $nombreMostrar, -1, PREG_SPLIT_NO_EMPTY);

  $iniciales = 'U';

  if (!empty($partes[0]) && !empty($partes[1])) {
      $iniciales = strtoupper(substr($partes[0], 0, 1) . substr($partes[1], 0, 1));
  } elseif (!empty($partes[0])) {
      $iniciales = strtoupper(substr($partes[0], 0, 1));
  }
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

            <div
              class="relative"
              x-data="{ open:false }"
            >
              <button
                type="button"
                @click="open = !open"
                class="notif-btn {{ $totalNoLeidas > 0 ? 'has-alert' : '' }}"
                title="Notificaciones"
                aria-label="Notificaciones"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="notif-icon h-5 w-5 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082A23.848 23.848 0 0 0 18 15.75V11.25a6 6 0 1 0-12 0v4.5c0 .681-.287 1.332-.79 1.787A23.848 23.848 0 0 0 8.143 17.082m6.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>

                @if($totalNoLeidas > 0)
                  <span class="notif-badge">
                    {{ $totalNoLeidas > 99 ? '99+' : $totalNoLeidas }}
                  </span>
                @endif
              </button>

              <div
                x-show="open"
                @click.outside="open = false"
                x-transition:enter="transition ease-out duration-180"
                x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-140"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                class="absolute right-0 mt-3 z-50"
                style="display:none;"
              >
                <div class="notif-panel">

                  <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                      <div class="font-extrabold text-slate-800">Notificaciones</div>
                      <div class="text-xs text-slate-500 mt-0.5">Avisos recientes del sistema</div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                      <span class="notif-pill">
                        {{ $totalNoLeidas > 99 ? '99+' : $totalNoLeidas }}
                      </span>

                      @if($totalNoLeidas > 0)
                        <form method="POST" action="{{ route('notificaciones.leer_todas') }}">
                          @csrf
                          <button class="notif-mark-btn">
                            Marcar todas
                          </button>
                        </form>
                      @endif
                    </div>
                  </div>

                  <div class="max-h-[420px] overflow-y-auto">
                    @forelse($notificacionesNoLeidas as $n)
                      <div class="notif-item px-4 py-3 border-b border-slate-100/90">
                        <div class="flex items-start gap-3">

                          <a
                            href="{{ route('notificaciones.ir', $n->id) }}"
                            class="notif-link"
                          >
                            <span class="notif-dot"></span>

                            <div class="min-w-0">
                              <div class="notif-title text-sm font-extrabold text-slate-800 truncate transition-colors">
                                {{ $n->data['titulo'] ?? 'Notificación' }}
                              </div>

                              @if(!empty($n->data['mensaje']))
                                <div class="text-sm text-slate-600 mt-1 leading-5">
                                  {{ $n->data['mensaje'] }}
                                </div>
                              @endif

                              <div class="text-[11px] text-slate-400 mt-2 font-semibold">
                                {{ $n->created_at?->diffForHumans() }}
                              </div>
                            </div>
                          </a>

                        </div>
                      </div>
                    @empty
                      <div class="notif-empty">
                        No tienes notificaciones nuevas.
                      </div>
                    @endforelse
                  </div>

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
  <div style="width:36px;height:36px;border-radius:9999px;background:#1d4ed8;color:#ffffff;display:grid;place-items:center;font-weight:700;font-size:14px;">
  {{ $iniciales }}
  </div>
  <div class="leading-tight">
    <div class="text-sm font-bold text-slate-800">
      {{ $user->name ?? $user->nombre ?? $user->nombre_completo ?? 'Usuario' }}
    </div>
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