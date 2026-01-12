{{-- resources/views/partials/sidebar.blade.php --}}
<aside
  x-data="sidebarMenu"
  x-init="init()"
  :class="$store.sidebar.collapsed ? 'w-[84px]' : 'w-[280px]'"
  class="sticky top-0 min-h-screen shrink-0 border-r border-slate-200/70 bg-white/80 backdrop-blur transition-[width] duration-300 flex flex-col"
>
  @php
    use App\Support\EmpresaScope;
    use App\Models\Empresa;

    // =========================
    // ICONOS "Kraya" (inline)
    // =========================
    $icon = function($name){
      // principales
      if($name==='dashboard') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3.75 3.75h7.5v7.5h-7.5v-7.5Zm9 0h7.5v7.5h-7.5v-7.5Zm-9 9h7.5v7.5h-7.5v-7.5Zm9 0h7.5v7.5h-7.5v-7.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>';
      if($name==='shield') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 2.25l7.5 4.5v6.75c0 4.477-3.248 8.385-7.5 9.75-4.252-1.365-7.5-5.273-7.5-9.75V6.75L12 2.25Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>';
      if($name==='cube') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M21 7.5 12 12.75 3 7.5m18 0-9-5.25L3 7.5m18 0v9.75A2.25 2.25 0 0 1 19.875 19.2L12 23.25 4.125 19.2A2.25 2.25 0 0 1 3 17.25V7.5" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>';
      if($name==='chev') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="m19.5 8.25-7.5 7.5-7.5-7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

      // ✅ Proyectos (folder)
      if($name==='folder') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3.75 7.5A2.25 2.25 0 0 1 6 5.25h4.5l1.5 1.5H18A2.25 2.25 0 0 1 20.25 9v8.25A2.25 2.25 0 0 1 18 19.5H6A2.25 2.25 0 0 1 3.75 17.25V7.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>';

      // inventario
      if($name==='list') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M8.25 6.75h12m-12 5.25h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm0 5.25h.007v.008H3.75V12Zm0 5.25h.007v.008H3.75v-.008Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
      if($name==='arrows') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M7.5 7.5h12m0 0-3-3m3 3-3 3M16.5 16.5h-12m0 0 3 3m-3-3 3-3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
      if($name==='box') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M20.25 7.5v11.25A2.25 2.25 0 0 1 18 21H6a2.25 2.25 0 0 1-2.25-2.25V7.5m16.5 0A2.25 2.25 0 0 0 18 5.25H6A2.25 2.25 0 0 0 3.75 7.5m16.5 0v.375c0 .621-.504 1.125-1.125 1.125H4.875A1.125 1.125 0 0 1 3.75 7.875V7.5" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>';
      if($name==='warehouse') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3.75 21h16.5M4.5 3h15l1.5 6H3L4.5 3Zm1.5 6v12m12-12v12M9 21v-6h6v6" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>';
      if($name==='clock') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 6v6l4 2m6-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

      // configuración empresa
      if($name==='building') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3.75 21h16.5M6 21V4.5A2.25 2.25 0 0 1 8.25 2.25h7.5A2.25 2.25 0 0 1 18 4.5V21M9 6.75h.008V6.758H9V6.75Zm0 3h.008V9.758H9V9.75Zm0 3h.008v.008H9v-.008Zm6-6h.008V6.758H15V6.75Zm0 3h.008V9.758H15V9.75Zm0 3h.008v.008H15v-.008Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>';
      if($name==='users') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" stroke="currentColor" stroke-width="2"/><path d="M4.5 20.118a7.5 7.5 0 0 1 15 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.5-1.632Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>';
      if($name==='roles') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 2.25l7.5 4.5v6.75c0 4.477-3.248 8.385-7.5 9.75-4.252-1.365-7.5-5.273-7.5-9.75V6.75L12 2.25Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M9 12.75 11.25 15 15 9.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
      if($name==='key') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M15.75 5.25a4.5 4.5 0 1 1-8.584 2.11c-.23.52-.35 1.094-.35 1.69v2.25h3v3h3v3h3V9.05a4.48 4.48 0 0 1 1.934-3.8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

      return '';
    };

    $user = auth()->user();
    $empresa = $user?->empresa;

    // =========================
    // DETECTAR SUPERADMIN
    // =========================
    $isSuperAdmin = false;
    if ($user) {
      if (method_exists($user, 'hasRole')) {
        $isSuperAdmin = $user->hasRole('SuperAdmin'); // tu rol real
      } elseif (isset($user->is_superadmin)) {
        $isSuperAdmin = (bool) $user->is_superadmin;
      }
    }

    // =========================
    // CONTEXTO SUPERADMIN
    // =========================
    $ctxEmpresaId = EmpresaScope::getId();
    $hasCtx       = EmpresaScope::has();

    $ctxEmpresa = null;
    if ($isSuperAdmin && $hasCtx && $ctxEmpresaId) {
      $ctxEmpresa = Empresa::select('id','nombre')->find($ctxEmpresaId);
      if (!$ctxEmpresa) $hasCtx = false;
    }

    // =========================
    // RUTAS ACTIVAS (FIX /app)
    // =========================
    $path = trim(request()->path(), '/');                // ej: "app/inventario/materiales"
    $pathNoApp = preg_replace('#^app/?#', '', $path);    // ej: "inventario/materiales" o "" cuando es /app

    $is = fn($p) => trim($pathNoApp,'/') === trim($p,'/');
    $starts = fn($p) => str_starts_with(trim($pathNoApp,'/'), trim($p,'/'));

    $dashActive  = request()->routeIs('dashboard'); // ✅ mejor por route name
    $invActive   = $starts('inventario');
    $adminActive = $starts('admin');

    // proyectos siguen con routeIs
    $proyActive  = request()->routeIs('admin.proyectos*');

    // =========================
    // VISIBILIDAD DE MENÚS
    // =========================

    /**
     * ✅ IMPORTANTÍSIMO:
     * SuperAdmin VE el menú global SIEMPRE (sin depender de permisos).
     * Si no es superadmin, entonces sí depende de permisos.
     */
    $canAdminGlobal = $isSuperAdmin || ($user && (
      $user->can('admin.ver') ||
      $user->can('usuarios.ver') ||
      $user->can('roles.ver') ||
      $user->can('permisos.ver') ||
      $user->can('empresas.ver') ||
      $user->can('proyectos.ver')
    ));

    // Admin de empresa: gestiona su empresa/usuarios/roles/permisos (solo su empresa)
    $canMiEmpresa       = $user && $user->can('miempresa.ver');
    $canUsuariosEmpresa = $user && $user->can('usuarios.ver');
    $canRolesEmpresa    = $user && $user->can('roles.ver');
    $canPermisosEmpresa = $user && $user->can('permisos.ver');

    $canConfigEmpresaMenu = !$isSuperAdmin && ($canMiEmpresa || $canUsuariosEmpresa || $canRolesEmpresa || $canPermisosEmpresa);

    // Inventario por permisos (usuario normal)
    $canInventarioPorPermiso = $user && (
      $user->can('inventario.ver') ||
      $user->can('materiales.ver') ||
      $user->can('almacenes.ver') ||
      $user->can('kardex.ver')
    );

    /**
     * Inventario final:
     * - SuperAdmin => solo con contexto (empresa seleccionada)
     * - No superadmin => por permisos de inventario
     */
    $canInventario = $isSuperAdmin
      ? $hasCtx
      : $canInventarioPorPermiso;

    /**
     * ✅ Proyectos:
     * - SuperAdmin => SOLO si tiene contexto (si no, el controller da 403)
     * - No superadmin => por permiso proyectos.ver
     */
    $canProyectos = $isSuperAdmin
      ? $hasCtx
      : ($user && $user->can('proyectos.ver'));

    // Logo empresa (solo NO superadmin)
    $logoPath = (string) ($empresa?->logo_path ?? '');
    $hasLogo  = $logoPath !== '';
    $logoUrl  = $hasLogo ? asset('storage/' . ltrim($logoPath, '/')) : null;

    // Listado empresas para selector (solo superadmin)
    $empresasList = collect();
    if ($isSuperAdmin) {
      $empresasList = Empresa::orderBy('nombre')->get(['id','nombre']);
    }
  @endphp

  {{-- BRAND --}}
  <div class="h-16 px-3 flex items-center gap-3 border-b border-slate-200/70">
    <div class="h-10 w-10 rounded-2xl bg-indigo-600 text-white grid place-items-center font-black shadow-sm overflow-hidden">
      @if(!$isSuperAdmin && $hasLogo)
        <img src="{{ $logoUrl }}" alt="Logo {{ $empresa?->nombre ?? '' }}"
             class="h-10 w-10 object-contain bg-white p-1" loading="lazy">
      @else
        {{ $isSuperAdmin ? 'SA' : strtoupper(substr($empresa?->nombre ?? 'E', 0, 1)) }}
      @endif
    </div>

    <div class="min-w-0 flex-1" x-show="!$store.sidebar.collapsed" x-transition.opacity.duration.150ms>
      <div class="text-sm font-black text-slate-900 truncate">
        {{ $isSuperAdmin ? 'Panel Super Admin' : 'CRM Construcción' }}
      </div>
      <div class="text-xs font-bold text-slate-500 truncate">
        @if($isSuperAdmin)
          {{ $hasCtx ? ('Empresa: ' . ($ctxEmpresa?->nombre ?? 'Seleccionada')) : 'Todas las empresas' }}
        @else
          {{ $empresa?->nombre ?? 'Sin empresa' }}
        @endif
      </div>
    </div>

    <button type="button"
      class="ml-auto inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white p-2 shadow-sm hover:bg-slate-50 active:scale-[.98]"
      @click="$store.sidebar.toggle()"
      :title="$store.sidebar.collapsed ? 'Expandir menú' : 'Colapsar menú'"
      aria-label="Toggle sidebar"
    >
      <svg x-show="!$store.sidebar.collapsed" xmlns="http://www.w3.org/2000/svg"
           class="h-5 w-5 text-slate-700" fill="none" viewBox="0 0 24 24"
           stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
      </svg>
      <svg x-show="$store.sidebar.collapsed" xmlns="http://www.w3.org/2000/svg"
           class="h-5 w-5 text-slate-700" fill="none" viewBox="0 0 24 24"
           stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5 15.75 12l-7.5 7.5"/>
      </svg>
    </button>
  </div>

  {{-- SELECTOR EMPRESA (SOLO SUPERADMIN) --}}
  @if($isSuperAdmin)
    <div class="px-3 py-3 border-b border-slate-200/70" x-show="!$store.sidebar.collapsed" x-transition.opacity.duration.150ms>
      <div class="rounded-2xl border border-slate-200 bg-white/70 p-3">
        <div class="text-xs font-black text-slate-700 uppercase tracking-wide">Contexto de empresa</div>

        <form method="POST" action="{{ route('admin.empresa_context.set') }}" class="mt-2 space-y-2">
          @csrf
          <select name="empresa_id"
            class="w-full h-10 rounded-xl bg-white border border-slate-200/70 shadow-sm
                   focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300
                   text-sm font-semibold text-slate-900 px-3"
            required>
            <option value="" disabled {{ !$hasCtx ? 'selected' : '' }}>— Selecciona una empresa —</option>
            @foreach($empresasList as $e)
              <option value="{{ $e->id }}" {{ ((int)$ctxEmpresaId === (int)$e->id) ? 'selected' : '' }}>
                {{ $e->nombre }}
              </option>
            @endforeach
          </select>

          <button type="submit"
            class="w-full inline-flex items-center justify-center rounded-xl h-10 text-sm font-extrabold
                   bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm">
            Aplicar
          </button>
        </form>

        <form method="POST" action="{{ route('admin.empresa_context.clear') }}" class="mt-2">
          @csrf
          <button type="submit"
            class="w-full inline-flex items-center justify-center rounded-xl h-10 text-sm font-extrabold
                   bg-white border border-slate-200 hover:bg-slate-50 shadow-sm">
            Todas
          </button>
        </form>

        <div class="mt-2 text-[11px] font-semibold text-slate-500">
          @if($hasCtx)
            Inventario habilitado para: <span class="text-slate-900 font-extrabold">{{ $ctxEmpresa?->nombre ?? 'Empresa' }}</span>
          @else
            Para usar Inventario como Super Admin, selecciona una empresa.
          @endif
        </div>
      </div>
    </div>
  @endif

  {{-- NAV --}}
  <nav class="px-2 py-3 space-y-1 flex-1 overflow-y-auto">

    {{-- DASHBOARD --}}
    <a href="{{ route('dashboard') }}"
       class="group flex items-center gap-3 rounded-2xl px-3 py-2 text-sm font-extrabold transition
              {{ $dashActive ? 'bg-slate-100 text-slate-900' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}"
       :class="$store.sidebar.collapsed ? 'justify-center' : ''">
      <span class="h-5 w-5 shrink-0 {{ $dashActive ? 'text-slate-900' : 'text-slate-500 group-hover:text-slate-900' }}">
        {!! $icon('dashboard') !!}
      </span>
      <span x-show="!$store.sidebar.collapsed" x-transition.opacity.duration.150ms class="truncate">Dashboard</span>
    </a>

    {{-- ✅ PROYECTOS (MENÚ PRINCIPAL) --}}
    @if($canProyectos)
      <div class="pt-2">
        <button type="button"
          class="w-full group flex items-center gap-3 rounded-2xl px-3 py-2 text-sm font-extrabold transition
                 {{ $proyActive ? 'bg-slate-100 text-slate-900' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}"
          :class="$store.sidebar.collapsed ? 'justify-center' : ''"
          @click="toggleGroup('proyectos')"
          title="Proyectos"
        >
          <span class="h-5 w-5 shrink-0 {{ $proyActive ? 'text-slate-900' : 'text-slate-500 group-hover:text-slate-900' }}">
            {!! $icon('folder') !!}
          </span>

          <span class="flex-1 text-left" x-show="!$store.sidebar.collapsed" x-transition.opacity.duration.150ms>
            Proyectos
          </span>

          <span x-show="!$store.sidebar.collapsed" class="h-5 w-5 text-slate-400 transition-transform"
                :class="open.proyectos ? 'rotate-180' : ''">
            {!! $icon('chev') !!}
          </span>
        </button>

        <div x-show="open.proyectos && !$store.sidebar.collapsed"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             class="mt-1 pl-4">
          <div class="border-l border-slate-200 pl-3 space-y-1">

            <a href="{{ route('admin.proyectos') }}"
               class="group flex items-center gap-3 rounded-xl px-2 py-2 text-sm font-bold transition
                      {{ request()->routeIs('admin.proyectos*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
              <span class="h-4 w-4 {{ request()->routeIs('admin.proyectos*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700' }}">
                {!! $icon('folder') !!}
              </span>
              <span>Listado</span>
            </a>

          </div>
        </div>
      </div>
    @endif

    {{-- SUPER ADMIN (ADMIN GLOBAL) --}}
    @if($isSuperAdmin)
      <div class="pt-2">
        <button type="button"
          class="w-full group flex items-center gap-3 rounded-2xl px-3 py-2 text-sm font-extrabold transition
                 {{ $adminActive ? 'bg-slate-100 text-slate-900' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}"
          :class="$store.sidebar.collapsed ? 'justify-center' : ''"
          @click="toggleGroup('admin_global')"
          title="Super Admin"
        >
          <span class="h-5 w-5 shrink-0 {{ $adminActive ? 'text-slate-900' : 'text-slate-500 group-hover:text-slate-900' }}">
            {!! $icon('shield') !!}
          </span>

          <span class="flex-1 text-left" x-show="!$store.sidebar.collapsed" x-transition.opacity.duration.150ms>
            Super Admin
          </span>

          <span x-show="!$store.sidebar.collapsed" class="h-5 w-5 text-slate-400 transition-transform"
                :class="open.admin_global ? 'rotate-180' : ''">
            {!! $icon('chev') !!}
          </span>
        </button>

        <div x-show="open.admin_global && !$store.sidebar.collapsed"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             class="mt-1 pl-4">
          <div class="border-l border-slate-200 pl-3 space-y-1">

            <a href="{{ route('admin.empresas') }}"
               class="group flex items-center gap-3 rounded-xl px-2 py-2 text-sm font-bold transition
                      {{ request()->routeIs('admin.empresas*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
              <span class="h-4 w-4 {{ request()->routeIs('admin.empresas*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700' }}">
                {!! $icon('building') !!}
              </span>
              <span>Empresas</span>
            </a>

            <a href="{{ route('admin.usuarios') }}"
               class="group flex items-center gap-3 rounded-xl px-2 py-2 text-sm font-bold transition
                      {{ request()->routeIs('admin.usuarios*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
              <span class="h-4 w-4 {{ request()->routeIs('admin.usuarios*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700' }}">
                {!! $icon('users') !!}
              </span>
              <span>Usuarios</span>
            </a>

            <a href="{{ route('admin.roles') }}"
               class="group flex items-center gap-3 rounded-xl px-2 py-2 text-sm font-bold transition
                      {{ request()->routeIs('admin.roles*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
              <span class="h-4 w-4 {{ request()->routeIs('admin.roles*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700' }}">
                {!! $icon('roles') !!}
              </span>
              <span>Roles</span>
            </a>

            <a href="{{ route('admin.permisos') }}"
               class="group flex items-center gap-3 rounded-xl px-2 py-2 text-sm font-bold transition
                      {{ request()->routeIs('admin.permisos*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
              <span class="h-4 w-4 {{ request()->routeIs('admin.permisos*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700' }}">
                {!! $icon('key') !!}
              </span>
              <span>Permisos</span>
            </a>

          </div>
        </div>
      </div>
    @endif

    {{-- INVENTARIO --}}
    @if($canInventario)
      <div class="pt-2">
        <button type="button"
          class="w-full group flex items-center gap-3 rounded-2xl px-3 py-2 text-sm font-extrabold transition
                 {{ $invActive ? 'bg-slate-100 text-slate-900' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}"
          :class="$store.sidebar.collapsed ? 'justify-center' : ''"
          @click="toggleGroup('inventario')"
          title="Inventario"
        >
          <span class="h-5 w-5 shrink-0 {{ $invActive ? 'text-slate-900' : 'text-slate-500 group-hover:text-slate-900' }}">
            {!! $icon('cube') !!}
          </span>

          <span class="flex-1 text-left" x-show="!$store.sidebar.collapsed" x-transition.opacity.duration.150ms>
            Inventario
          </span>

          <span x-show="!$store.sidebar.collapsed" class="h-5 w-5 text-slate-400 transition-transform"
                :class="open.inventario ? 'rotate-180' : ''">
            {!! $icon('chev') !!}
          </span>
        </button>

        <div x-show="open.inventario && !$store.sidebar.collapsed"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             class="mt-1 pl-4">
          <div class="border-l border-slate-200 pl-3 space-y-1">

            <a href="{{ route('inventario.existencias') }}"
               class="group flex items-center gap-3 rounded-xl px-2 py-2 text-sm font-bold transition
                      {{ request()->routeIs('inventario.existencias') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
              <span class="h-4 w-4 {{ request()->routeIs('inventario.existencias') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700' }}">
                {!! $icon('list') !!}
              </span>
              <span>Existencias</span>
            </a>

            <a href="{{ route('inventario.movimientos') }}"
               class="group flex items-center gap-3 rounded-xl px-2 py-2 text-sm font-bold transition
                      {{ request()->routeIs('inventario.movimientos*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
              <span class="h-4 w-4 {{ request()->routeIs('inventario.movimientos*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700' }}">
                {!! $icon('arrows') !!}
              </span>
              <span>Movimientos</span>
            </a>

            <a href="{{ route('inventario.materiales') }}"
               class="group flex items-center gap-3 rounded-xl px-2 py-2 text-sm font-bold transition
                      {{ request()->routeIs('inventario.materiales*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
              <span class="h-4 w-4 {{ request()->routeIs('inventario.materiales*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700' }}">
                {!! $icon('box') !!}
              </span>
              <span>Materiales</span>
            </a>

            <a href="{{ route('inventario.almacenes') }}"
               class="group flex items-center gap-3 rounded-xl px-2 py-2 text-sm font-bold transition
                      {{ request()->routeIs('inventario.almacenes*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
              <span class="h-4 w-4 {{ request()->routeIs('inventario.almacenes*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700' }}">
                {!! $icon('warehouse') !!}
              </span>
              <span>Almacenes</span>
            </a>

            <a href="{{ route('inventario.kardex') }}"
               class="group flex items-center gap-3 rounded-xl px-2 py-2 text-sm font-bold transition
                      {{ request()->routeIs('inventario.kardex*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
              <span class="h-4 w-4 {{ request()->routeIs('inventario.kardex*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700' }}">
                {!! $icon('clock') !!}
              </span>
              <span>Kardex</span>
            </a>

          </div>
        </div>
      </div>
    @endif

    {{-- CONFIGURACIÓN (ADMIN EMPRESA) --}}
    @if($canConfigEmpresaMenu)
      <div class="pt-2">
        <button type="button"
          class="w-full group flex items-center gap-3 rounded-2xl px-3 py-2 text-sm font-extrabold transition
                 {{ request()->routeIs('admin.*') && !$isSuperAdmin ? 'bg-slate-100 text-slate-900' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}"
          :class="$store.sidebar.collapsed ? 'justify-center' : ''"
          @click="toggleGroup('config_empresa')"
          title="Configuración"
        >
          <span class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.*') && !$isSuperAdmin ? 'text-slate-900' : 'text-slate-500 group-hover:text-slate-900' }}">
            {!! $icon('shield') !!}
          </span>

          <span class="flex-1 text-left" x-show="!$store.sidebar.collapsed" x-transition.opacity.duration.150ms>
            Configuración
          </span>

          <span x-show="!$store.sidebar.collapsed" class="h-5 w-5 text-slate-400 transition-transform"
                :class="open.config_empresa ? 'rotate-180' : ''">
            {!! $icon('chev') !!}
          </span>
        </button>

        <div x-show="open.config_empresa && !$store.sidebar.collapsed"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             class="mt-1 pl-4">
          <div class="border-l border-slate-200 pl-3 space-y-1">

            @if($canMiEmpresa)
              <a href="{{ route('admin.mi_empresa.edit') }}"
                 class="group flex items-center gap-3 rounded-xl px-2 py-2 text-sm font-bold transition
                        {{ request()->routeIs('admin.mi_empresa.*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <span class="h-4 w-4 {{ request()->routeIs('admin.mi_empresa.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700' }}">
                  {!! $icon('building') !!}
                </span>
                <span>Mi empresa</span>
              </a>
            @endif

            @if($canUsuariosEmpresa)
              <a href="{{ route('admin.usuarios') }}"
                 class="group flex items-center gap-3 rounded-xl px-2 py-2 text-sm font-bold transition
                        {{ request()->routeIs('admin.usuarios*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <span class="h-4 w-4 {{ request()->routeIs('admin.usuarios*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700' }}">
                  {!! $icon('users') !!}
                </span>
                <span>Usuarios</span>
              </a>
            @endif

            @if($canRolesEmpresa)
              <a href="{{ route('admin.roles') }}"
                 class="group flex items-center gap-3 rounded-xl px-2 py-2 text-sm font-bold transition
                        {{ request()->routeIs('admin.roles*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <span class="h-4 w-4 {{ request()->routeIs('admin.roles*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700' }}">
                  {!! $icon('roles') !!}
                </span>
                <span>Roles</span>
              </a>
            @endif

            @if($canPermisosEmpresa)
              <a href="{{ route('admin.permisos') }}"
                 class="group flex items-center gap-3 rounded-xl px-2 py-2 text-sm font-bold transition
                        {{ request()->routeIs('admin.permisos*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <span class="h-4 w-4 {{ request()->routeIs('admin.permisos*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700' }}">
                  {!! $icon('key') !!}
                </span>
                <span>Permisos</span>
              </a>
            @endif

          </div>
        </div>
      </div>
    @endif

  </nav>

  {{-- FOOTER --}}
  <div class="mt-auto border-t border-slate-200/70 p-3">
    <div class="flex items-center gap-2" :class="$store.sidebar.collapsed ? 'justify-center' : ''">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 2.25a9.75 9.75 0 1 0 0 19.5 9.75 9.75 0 0 0 0-19.5ZM12 8.25a3.75 3.75 0 1 0 0 7.5 3.75 3.75 0 0 0 0-7.5Z"/>
      </svg>

      <div x-show="!$store.sidebar.collapsed" x-transition.opacity.duration.150ms class="text-xs font-bold text-slate-500">
        Soporte: <span class="text-slate-900 font-extrabold">TI</span>
      </div>
    </div>
  </div>
</aside>
