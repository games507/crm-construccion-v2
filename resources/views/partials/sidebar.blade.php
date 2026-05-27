{{-- resources/views/partials/sidebar.blade.php --}}
<aside
  x-data="sidebarMenu"
  x-init="init()"
  :class="$store.sidebar.collapsed ? 'w-[84px]' : 'w-[280px]'"
  class="sticky top-0 min-h-screen shrink-0 border-r border-slate-200/70 bg-white/85 backdrop-blur-xl transition-[width] duration-300 flex flex-col"
>
  @php
    use App\Support\EmpresaScope;
    use App\Models\Empresa;

    /*
    |--------------------------------------------------------------------------
    | ICONOS MODERNOS - estilo SaaS / Lucide
    |--------------------------------------------------------------------------
    */
    $icon = function($name){
      $base = 'width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';

      return match($name) {
        'dashboard' => '<svg '.$base.'><rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/></svg>',
        'chev' => '<svg '.$base.'><path d="m6 9 6 6 6-6"/></svg>',
        'shield' => '<svg '.$base.'><path d="M20 13c0 5-3.5 7.5-8 9-4.5-1.5-8-4-8-9V5l8-3 8 3v8Z"/><path d="m9 12 2 2 4-5"/></svg>',
        'building' => '<svg '.$base.'><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/><path d="M9 9h1"/><path d="M9 13h1"/><path d="M9 17h1"/></svg>',
        'folder' => '<svg '.$base.'><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.5L10 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/></svg>',
        'tasks' => '<svg '.$base.'><path d="M9 6h11"/><path d="M9 12h11"/><path d="M9 18h11"/><path d="m3 6 1 1 2-2"/><path d="m3 12 1 1 2-2"/><path d="m3 18 1 1 2-2"/></svg>',
        'cube' => '<svg '.$base.'><path d="m21 8-9-5-9 5 9 5 9-5Z"/><path d="M3 8v8l9 5 9-5V8"/><path d="M12 13v8"/></svg>',
        'list' => '<svg '.$base.'><path d="M8 6h13"/><path d="M8 12h13"/><path d="M8 18h13"/><path d="M3 6h.01"/><path d="M3 12h.01"/><path d="M3 18h.01"/></svg>',
        'arrows' => '<svg '.$base.'><path d="M17 3l4 4-4 4"/><path d="M3 7h18"/><path d="M7 21l-4-4 4-4"/><path d="M21 17H3"/></svg>',
        'box' => '<svg '.$base.'><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>',
        'warehouse' => '<svg '.$base.'><path d="M22 8.35V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8.35A2 2 0 0 1 3.26 6.5l8-3.2a2 2 0 0 1 1.48 0l8 3.2A2 2 0 0 1 22 8.35Z"/><path d="M6 18h12"/><path d="M6 14h12"/><path d="M6 10h12"/></svg>',
        'clock' => '<svg '.$base.'><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>',
        'users' => '<svg '.$base.'><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        'roles' => '<svg '.$base.'><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-5"/></svg>',
        'key' => '<svg '.$base.'><circle cx="7.5" cy="15.5" r="5.5"/><path d="m21 2-9.6 9.6"/><path d="m15.5 7.5 3 3L22 7l-3-3"/></svg>',
        'money' => '<svg '.$base.'><rect x="3" y="6" width="18" height="12" rx="2"/><circle cx="12" cy="12" r="2.5"/><path d="M6 12h.01"/><path d="M18 12h.01"/></svg>',
        'wallet' => '<svg '.$base.'><path d="M20 12V8H6a2 2 0 0 1 0-4h12v4"/><path d="M4 6v14a2 2 0 0 0 2 2h14v-6"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/></svg>',
        'receipt' => '<svg '.$base.'><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2Z"/><path d="M8 7h8"/><path d="M8 12h8"/><path d="M8 17h5"/></svg>',
        'coins' => '<svg '.$base.'><circle cx="8" cy="8" r="6"/><path d="M18.09 10.37A6 6 0 1 1 10.34 18"/><path d="M7 6h1v4"/><path d="m16.71 13.88.7.71-2.82 2.82"/></svg>',
        'truck' => '<svg '.$base.'><path d="M10 17h4V5H2v12h3"/><path d="M14 17h1"/><path d="M19 17h3v-6l-3-4h-5"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/></svg>',
        'supplier' => '<svg '.$base.'><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6"/><path d="M23 11h-6"/></svg>',
        'cart' => '<svg '.$base.'><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57L22 7H5.12"/></svg>',
        'settings' => '<svg '.$base.'><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.38a2 2 0 0 0-.73-2.73l-.15-.09a2 2 0 0 1-1-1.74v-.51a2 2 0 0 1 1-1.72l.15-.1a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2Z"/><circle cx="12" cy="12" r="3"/></svg>',
        default => '<svg '.$base.'><circle cx="12" cy="12" r="10"/></svg>',
      };
    };

    $user = auth()->user();
    $empresa = $user?->empresa;

    $isSuperAdmin = false;
    if ($user) {
      if (method_exists($user, 'hasRole')) {
        $isSuperAdmin = $user->hasRole('SuperAdmin') || $user->hasRole('Super Admin');
      } elseif (isset($user->is_superadmin)) {
        $isSuperAdmin = (bool) $user->is_superadmin;
      }
    }

$ctxEmpresaId = EmpresaScope::getId();
$hasCtx = EmpresaScope::has();

$ctxEmpresa = null;

if ($isSuperAdmin && $ctxEmpresaId) {
  $ctxEmpresa = Empresa::select('id','nombre','logo_path')->find($ctxEmpresaId);
}

if ($isSuperAdmin && !$ctxEmpresa) {
  $ctxEmpresa = Empresa::select('id','nombre','logo_path')->orderBy('id')->first();
}

$brandEmpresa = $isSuperAdmin
  ? $ctxEmpresa
  : ($empresa ?: Empresa::select('id','nombre','logo_path')->orderBy('id')->first());

$hasCtx = (bool) $brandEmpresa;

    $logoPath = (string) ($brandEmpresa?->logo_path ?? '');
    $hasLogo = $logoPath !== '';
    $logoUrl = $hasLogo ? asset('storage/' . ltrim($logoPath, '/')) : null;

    $path = trim(request()->path(), '/');
    $pathNoApp = preg_replace('#^app/?#', '', $path);
    $starts = fn($p) => str_starts_with(trim($pathNoApp,'/'), trim($p,'/'));

    $dashActive = request()->routeIs('dashboard');
    $invActive = $starts('inventario');

    $proyActive = request()->routeIs('admin.proyectos*');
    $comprasActive = request()->routeIs('admin.proveedores*');
$finanzasActive =
    request()->routeIs('admin.cuentas*') ||
    request()->routeIs('admin.cobros*') ||
    request()->routeIs('admin.ingresos*') ||
    request()->routeIs('admin.ordenes_compra*');

    $adminActive = $starts('admin') && !$proyActive && !$comprasActive && !$finanzasActive;

    $canMiEmpresa       = $user && $user->can('miempresa.ver');
    $canUsuariosEmpresa = $user && $user->can('usuarios.ver');
    $canRolesEmpresa    = $user && $user->can('roles.ver');
    $canPermisosEmpresa = $user && $user->can('permisos.ver');

    $canConfigEmpresaMenu = !$isSuperAdmin && ($canMiEmpresa || $canUsuariosEmpresa || $canRolesEmpresa || $canPermisosEmpresa);

    $canInventarioPorPermiso = $user && (
      $user->can('inventario.ver') ||
      $user->can('materiales.ver') ||
      $user->can('almacenes.ver') ||
      $user->can('kardex.ver') ||
      $user->can('movimientos.ver')
    );

    $canInventario = $isSuperAdmin ? $hasCtx : $canInventarioPorPermiso;

    $canProyectos = $isSuperAdmin
      ? $hasCtx
      : ($user && (
          $user->can('proyectos.ver') ||
          $user->can('mis_tareas.ver')
      ));

    $canCompras = $isSuperAdmin
      ? $hasCtx
      : ($user && (
          $user->can('proyectos.ver') ||
          $user->can('cuentas.ver') ||
          $user->can('admin.ver')
      ));

    $canFinanzas = $isSuperAdmin
      ? $hasCtx
      : ($user && (
          $user->can('cuentas.ver') ||
          $user->can('cobros.ver') ||
          $user->can('ingresos.ver') ||
          $user->can('flujo.ver')
      ));

    $canAdminGlobal = $isSuperAdmin || ($user && (
      $user->can('admin.ver') ||
      $user->can('usuarios.ver') ||
      $user->can('roles.ver') ||
      $user->can('permisos.ver') ||
      $user->can('empresas.ver')
    ));

    $empresasList = collect();
    if ($isSuperAdmin) {
      $empresasList = Empresa::orderBy('nombre')->get(['id','nombre']);
    }

    $navItem = function($route, $label, $iconName, $active, $subtitle = null) use ($icon) {
      $activeClass = $active
        ? 'bg-gradient-to-r from-sky-50 to-blue-50 text-slate-950 ring-1 ring-sky-100'
        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950';

      $iconClass = $active
        ? 'bg-sky-100 text-sky-700'
        : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-800';

      return '
        <a href="'.e($route).'"
           class="group flex items-center gap-3 rounded-2xl px-2.5 py-2 text-sm font-bold transition '.$activeClass.'">
          <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl transition '.$iconClass.'">
            '.$icon($iconName).'
          </span>
          <span class="min-w-0 flex-1">
            <span class="block truncate">'.$label.'</span>
            '.($subtitle ? '<span class="block truncate text-[11px] font-bold text-slate-400">'.$subtitle.'</span>' : '').'
          </span>
        </a>
      ';
    };
  @endphp

{{-- BRAND --}}
<div class="h-16 px-3 flex items-center gap-3 border-b border-slate-200/70">

  <div class="h-11 w-11 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden flex items-center justify-center shrink-0">

    @if($hasLogo)

      <img
        src="{{ $logoUrl }}"
        alt="Logo {{ $brandEmpresa?->nombre ?? 'Empresa' }}"
        class="h-full w-full object-cover"
        loading="lazy"
        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
      >

      <div
        style="display:none;"
        class="h-full w-full items-center justify-center bg-gradient-to-br from-sky-600 to-slate-900 text-white font-black text-sm"
      >
        {{ strtoupper(substr($brandEmpresa?->nombre ?? 'V', 0, 1)) }}
      </div>

    @else

      <div class="h-full w-full flex items-center justify-center bg-gradient-to-br from-sky-600 to-slate-900 text-white font-black text-sm">
        {{ $isSuperAdmin && !$brandEmpresa ? 'SA' : strtoupper(substr($brandEmpresa?->nombre ?? 'V', 0, 1)) }}
      </div>

    @endif

  </div>

  <div
    class="min-w-0 flex-1"
    x-show="!$store.sidebar.collapsed"
    x-transition.opacity.duration.150ms
  >
    <div class="text-sm font-black text-slate-900 truncate">
      {{ $isSuperAdmin ? 'Panel Super Admin' : 'VerticeSoft' }}
    </div>

    <div class="text-xs font-bold text-slate-500 truncate">
      @if($isSuperAdmin)
        {{ $hasCtx ? ('Empresa: ' . ($ctxEmpresa?->nombre ?? 'Seleccionada')) : 'Selecciona una empresa' }}
      @else
        {{ $brandEmpresa?->nombre ?? 'Sin empresa' }}
      @endif
    </div>
  </div>

  <button
    type="button"
    class="ml-auto inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white p-2 shadow-sm hover:bg-slate-50 active:scale-[.98]"
    @click="$store.sidebar.toggle()"
    :title="$store.sidebar.collapsed ? 'Expandir menú' : 'Colapsar menú'"
    aria-label="Toggle sidebar"
  >
    <svg
      x-show="!$store.sidebar.collapsed"
      xmlns="http://www.w3.org/2000/svg"
      class="h-5 w-5 text-slate-700"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
      stroke-width="1.8"
    >
      <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
    </svg>

    <svg
      x-show="$store.sidebar.collapsed"
      xmlns="http://www.w3.org/2000/svg"
      class="h-5 w-5 text-slate-700"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
      stroke-width="1.8"
    >
      <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5 15.75 12l-7.5 7.5"/>
    </svg>

  </button>

</div>
  {{-- SELECTOR EMPRESA --}}
  @if($isSuperAdmin)
    <div class="px-3 py-3 border-b border-slate-200/70" x-show="!$store.sidebar.collapsed" x-transition.opacity.duration.150ms>
      <div class="rounded-2xl border border-slate-200 bg-white/80 p-3 shadow-sm">
        <div class="text-xs font-black text-slate-700 uppercase tracking-wide">Contexto de empresa</div>

        <form method="POST" action="{{ route('admin.empresa_context.set') }}" class="mt-2 space-y-2">
          @csrf
          <select name="empresa_id"
            class="w-full h-10 rounded-xl bg-white border border-slate-200/70 shadow-sm focus:ring-2 focus:ring-sky-200 focus:border-sky-300 text-sm font-semibold text-slate-900 px-3"
            required>
            <option value="" disabled {{ !$hasCtx ? 'selected' : '' }}>— Selecciona una empresa —</option>
            @foreach($empresasList as $e)
              <option value="{{ $e->id }}" {{ ((int)$ctxEmpresaId === (int)$e->id) ? 'selected' : '' }}>
                {{ $e->nombre }}
              </option>
            @endforeach
          </select>

          <button type="submit"
            class="w-full inline-flex items-center justify-center rounded-xl h-10 text-sm font-extrabold bg-sky-700 text-white hover:bg-sky-800 shadow-sm">
            Aplicar
          </button>
        </form>

        <form method="POST" action="{{ route('admin.empresa_context.clear') }}" class="mt-2">
          @csrf
          <button type="submit"
            class="w-full inline-flex items-center justify-center rounded-xl h-10 text-sm font-extrabold bg-white border border-slate-200 hover:bg-slate-50 shadow-sm">
            Todas
          </button>
        </form>
      </div>
    </div>
  @endif

  {{-- NAV --}}
  <nav class="px-2 py-3 space-y-1 flex-1 overflow-y-auto">

    {{-- DASHBOARD --}}
    <a href="{{ route('dashboard') }}"
       class="group flex items-center gap-3 rounded-2xl px-3 py-2 text-sm font-extrabold transition
              {{ $dashActive ? 'bg-gradient-to-r from-slate-900 to-sky-800 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}"
       :class="$store.sidebar.collapsed ? 'justify-center' : ''">
      <span class="h-5 w-5 shrink-0 {{ $dashActive ? 'text-white' : 'text-slate-500 group-hover:text-slate-900' }}">
        {!! $icon('dashboard') !!}
      </span>
      <span x-show="!$store.sidebar.collapsed" x-transition.opacity.duration.150ms class="truncate">Dashboard</span>
    </a>

    {{-- PROYECTOS --}}
    @if($canProyectos)
      <div class="pt-2">
        <button type="button"
          class="w-full group flex items-center gap-3 rounded-2xl px-3 py-2 text-sm font-extrabold transition
                 {{ $proyActive ? 'bg-slate-100 text-slate-900' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}"
          :class="$store.sidebar.collapsed ? 'justify-center' : ''"
          @click="toggleGroup('proyectos')"
          title="Proyectos"
        >
          <span class="h-5 w-5 shrink-0 {{ $proyActive ? 'text-sky-700' : 'text-slate-500 group-hover:text-slate-900' }}">
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

            {!! $navItem(
              route('admin.proyectos'),
              'Listado',
              'folder',
              request()->routeIs('admin.proyectos') || request()->routeIs('admin.proyectos.create') || request()->routeIs('admin.proyectos.edit') || request()->routeIs('admin.proyectos.show'),
              'Obras y avances'
            ) !!}

            @can('mis_tareas.ver')
              {!! $navItem(
                route('admin.proyectos.mis_tareas'),
                'Mis tareas',
                'tasks',
                request()->routeIs('admin.proyectos.mis_tareas*'),
                'Asignaciones'
              ) !!}
            @endcan

          </div>
        </div>
      </div>
    @endif

    {{-- COMPRAS --}}
    @if($canCompras)
      <div class="pt-2">
        <button type="button"
          class="w-full group flex items-center gap-3 rounded-2xl px-3 py-2 text-sm font-extrabold transition
                 {{ $comprasActive ? 'bg-slate-100 text-slate-900' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}"
          :class="$store.sidebar.collapsed ? 'justify-center' : ''"
          @click="toggleGroup('compras')"
          title="Compras"
        >
          <span class="h-5 w-5 shrink-0 {{ $comprasActive ? 'text-sky-700' : 'text-slate-500 group-hover:text-slate-900' }}">
            {!! $icon('cart') !!}
          </span>

          <span class="flex-1 text-left" x-show="!$store.sidebar.collapsed" x-transition.opacity.duration.150ms>
            Compras
          </span>

          <span x-show="!$store.sidebar.collapsed" class="h-5 w-5 text-slate-400 transition-transform"
                :class="open.compras ? 'rotate-180' : ''">
            {!! $icon('chev') !!}
          </span>
        </button>

        <div x-show="open.compras && !$store.sidebar.collapsed"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             class="mt-1 pl-4">
          <div class="border-l border-slate-200 pl-3 space-y-1">

            {!! $navItem(
              route('admin.proveedores.index'),
              'Proveedores',
              'supplier',
              request()->routeIs('admin.proveedores*'),
              'Catálogo comercial'
            ) !!}

          </div>
        </div>
      </div>
    @endif

    {{-- FINANZAS --}}
    @if($canFinanzas)
      <div class="pt-2">
        <button type="button"
          class="w-full group flex items-center gap-3 rounded-2xl px-3 py-2 text-sm font-extrabold transition
                 {{ $finanzasActive ? 'bg-slate-100 text-slate-900' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}"
          :class="$store.sidebar.collapsed ? 'justify-center' : ''"
          @click="toggleGroup('finanzas')"
          title="Finanzas"
        >
          <span class="h-5 w-5 shrink-0 {{ $finanzasActive ? 'text-emerald-700' : 'text-slate-500 group-hover:text-slate-900' }}">
            {!! $icon('wallet') !!}
          </span>

          <span class="flex-1 text-left" x-show="!$store.sidebar.collapsed" x-transition.opacity.duration.150ms>
            Finanzas
          </span>

          <span x-show="!$store.sidebar.collapsed" class="h-5 w-5 text-slate-400 transition-transform"
                :class="open.finanzas ? 'rotate-180' : ''">
            {!! $icon('chev') !!}
          </span>
        </button>

        <div x-show="open.finanzas && !$store.sidebar.collapsed"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             class="mt-1 pl-4">
          <div class="border-l border-slate-200 pl-3 space-y-1">

            @can('cuentas.ver')
              {!! $navItem(
                route('admin.cuentas.index'),
                'Cuentas por pagar',
                'receipt',
                request()->routeIs('admin.cuentas*'),
                'Proveedores y pagos'
              ) !!}
            @endcan

            @can('cobros.ver')
              {!! $navItem(
                route('admin.cobros.index'),
                'Cuentas por cobrar',
                'coins',
                request()->routeIs('admin.cobros*'),
                'Clientes y cobros'
              ) !!}
            @endcan

            @can('ingresos.ver')
              {!! $navItem(
                route('admin.ingresos.index'),
                'Ingresos',
                'money',
                request()->routeIs('admin.ingresos*'),
                'Entradas de dinero'
              ) !!}
            @endcan
@can('ordenes_compra.ver')
  {!! $navItem(
    route('admin.ordenes_compra.index'),
    'Órdenes de compra',
    'receipt',
    request()->routeIs('admin.ordenes_compra*'),
    'Compras y abastecimiento'
  ) !!}
@endcan
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
          <span class="h-5 w-5 shrink-0 {{ $invActive ? 'text-sky-700' : 'text-slate-500 group-hover:text-slate-900' }}">
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

            {!! $navItem(route('inventario.existencias'), 'Existencias', 'list', request()->routeIs('inventario.existencias'), 'Stock actual') !!}
            {!! $navItem(route('inventario.movimientos'), 'Movimientos', 'arrows', request()->routeIs('inventario.movimientos*'), 'Entradas y salidas') !!}
            {!! $navItem(route('inventario.materiales'), 'Materiales', 'box', request()->routeIs('inventario.materiales*'), 'Catálogo') !!}
            {!! $navItem(route('inventario.almacenes'), 'Almacenes', 'warehouse', request()->routeIs('inventario.almacenes*'), 'Ubicaciones') !!}
            {!! $navItem(route('inventario.kardex'), 'Kardex', 'clock', request()->routeIs('inventario.kardex*'), 'Historial') !!}

          </div>
        </div>
      </div>
    @endif

    {{-- SUPER ADMIN --}}
    @if($isSuperAdmin)
      <div class="pt-2">
        <button type="button"
          class="w-full group flex items-center gap-3 rounded-2xl px-3 py-2 text-sm font-extrabold transition
                 {{ $adminActive ? 'bg-slate-100 text-slate-900' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}"
          :class="$store.sidebar.collapsed ? 'justify-center' : ''"
          @click="toggleGroup('admin_global')"
          title="Super Admin"
        >
          <span class="h-5 w-5 shrink-0 {{ $adminActive ? 'text-sky-700' : 'text-slate-500 group-hover:text-slate-900' }}">
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

            {!! $navItem(route('admin.empresas'), 'Empresas', 'building', request()->routeIs('admin.empresas*'), 'Multiempresa') !!}
            {!! $navItem(route('admin.usuarios'), 'Usuarios', 'users', request()->routeIs('admin.usuarios*'), 'Accesos') !!}
            {!! $navItem(route('admin.roles'), 'Roles', 'roles', request()->routeIs('admin.roles*'), 'Perfiles') !!}
            {!! $navItem(route('admin.permisos'), 'Permisos', 'key', request()->routeIs('admin.permisos*'), 'Seguridad') !!}

          </div>
        </div>
      </div>
    @endif

    {{-- CONFIGURACIÓN --}}
    @if($canConfigEmpresaMenu)
      <div class="pt-2">
        <button type="button"
          class="w-full group flex items-center gap-3 rounded-2xl px-3 py-2 text-sm font-extrabold transition
                 {{ request()->routeIs('admin.*') && !$isSuperAdmin && !$proyActive && !$comprasActive && !$finanzasActive ? 'bg-slate-100 text-slate-900' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}"
          :class="$store.sidebar.collapsed ? 'justify-center' : ''"
          @click="toggleGroup('config_empresa')"
          title="Configuración"
        >
          <span class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.*') && !$isSuperAdmin && !$proyActive && !$comprasActive && !$finanzasActive ? 'text-sky-700' : 'text-slate-500 group-hover:text-slate-900' }}">
            {!! $icon('settings') !!}
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
              {!! $navItem(route('admin.mi_empresa.edit'), 'Mi empresa', 'building', request()->routeIs('admin.mi_empresa.*'), 'Datos generales') !!}
            @endif

            @if($canUsuariosEmpresa)
              {!! $navItem(route('admin.usuarios'), 'Usuarios', 'users', request()->routeIs('admin.usuarios*'), 'Equipo') !!}
            @endif

            @if($canRolesEmpresa)
              {!! $navItem(route('admin.roles'), 'Roles', 'roles', request()->routeIs('admin.roles*'), 'Perfiles') !!}
            @endif

            @if($canPermisosEmpresa)
              {!! $navItem(route('admin.permisos'), 'Permisos', 'key', request()->routeIs('admin.permisos*'), 'Seguridad') !!}
            @endif

          </div>
        </div>
      </div>
    @endif

  </nav>

  {{-- FOOTER --}}
  <div class="mt-auto border-t border-slate-200/70 p-3">
    <div class="flex items-center gap-2" :class="$store.sidebar.collapsed ? 'justify-center' : ''">
      <div class="h-9 w-9 rounded-2xl bg-slate-100 text-slate-500 grid place-items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 2.25a9.75 9.75 0 1 0 0 19.5 9.75 9.75 0 0 0 0-19.5ZM12 8.25a3.75 3.75 0 1 0 0 7.5 3.75 3.75 0 0 0 0-7.5Z"/>
        </svg>
      </div>

      <div x-show="!$store.sidebar.collapsed" x-transition.opacity.duration.150ms class="min-w-0">
        <div class="text-xs font-black text-slate-800 truncate">VerticeSoft</div>
        <div class="text-[11px] font-bold text-slate-400 truncate">ERP / CRM Construcción</div>
      </div>
    </div>
  </div>
</aside>
