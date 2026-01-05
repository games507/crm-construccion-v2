{{-- resources/views/partials/sidebar.blade.php --}}
<aside
  x-data="sidebarMenu"
  x-init="init()"
  :class="$store.sidebar.collapsed ? 'w-[84px]' : 'w-[280px]'"
  class="sticky top-0 min-h-screen shrink-0 border-r border-slate-200/70 bg-white/80 backdrop-blur transition-[width] duration-300 flex flex-col"
>
  @php
    $user = auth()->user();
    $empresa = $user?->empresa;


    $path = trim(request()->path(), '/'); // ej: inventario/almacenes
    $is = fn($p) => trim($path,'/') === trim($p,'/');
    $starts = fn($p) => str_starts_with(trim($path,'/'), trim($p,'/'));

    $invActive = $starts('inventario');
    $adminActive = $starts('admin');

    // Permisos (para esconder/mostrar items)
    $canInventario = $user && (
      $user->can('inventario.ver') ||
      $user->can('materiales.ver') ||
      $user->can('almacenes.ver') ||
      $user->can('kardex.ver')
    );

    $canConfig = $user && (
      $user->can('admin.ver') ||
      $user->can('usuarios.ver') ||
      $user->can('roles.ver') ||
      $user->can('permisos.ver') ||
      $user->can('empresas.ver') ||
      $user->can('proyectos.ver') ||
      $user->can('miempresa.ver')
    );
  @endphp

  {{-- BRAND --}}
  @php
    // LOGO: usa empresa.logo_path (guardado en public disk)
    $logoPath = (string) ($empresa?->logo_path ?? '');
    $hasLogo  = $logoPath !== '';
    $logoUrl  = $hasLogo ? asset('storage/' . ltrim($logoPath, '/')) : null;
  @endphp

  <div class="h-16 px-3 flex items-center gap-3 border-b border-slate-200/70">
    {{-- Logo o inicial --}}
    <div class="h-10 w-10 rounded-2xl bg-indigo-600 text-white grid place-items-center font-black shadow-sm overflow-hidden">
      @if($hasLogo)
        <img
          src="{{ $logoUrl }}"
          alt="Logo {{ $empresa?->nombre ?? '' }}"
          class="h-10 w-10 object-contain bg-white p-1"
          loading="lazy"
        >
      @else
        {{ strtoupper(substr($empresa?->nombre ?? 'E', 0, 1)) }}
      @endif
    </div>

    <div class="min-w-0 flex-1" x-show="!$store.sidebar.collapsed" x-transition.opacity.duration.150ms>
      <div class="text-sm font-black text-slate-900 truncate">CRM Construcción</div>
      <div class="text-xs font-bold text-slate-500 truncate">
        {{ $empresa?->nombre ?? 'Sin empresa' }}
      </div>
    </div>

    {{-- Toggle --}}
    <button
      type="button"
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

  {{-- NAV --}}
  <nav class="px-2 py-3 space-y-1 flex-1 overflow-y-auto">

    {{-- DASHBOARD --}}
    <a href="{{ route('dashboard') }}"
       title="Dashboard"
       class="group flex items-center gap-3 rounded-2xl px-3 py-2 text-sm font-extrabold transition
              {{ $is('dashboard') ? 'bg-slate-100 text-slate-900' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}"
       :class="$store.sidebar.collapsed ? 'justify-center' : ''"
    >
      {{-- icon: squares --}}
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0
           {{ $is('dashboard') ? 'text-slate-900' : 'text-slate-500 group-hover:text-slate-900' }}"
           fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M3.75 3.75h7.5v7.5h-7.5v-7.5Zm9 0h7.5v7.5h-7.5v-7.5Zm-9 9h7.5v7.5h-7.5v-7.5Zm9 0h7.5v7.5h-7.5v-7.5Z"/>
      </svg>

      <span x-show="!$store.sidebar.collapsed" x-transition.opacity.duration.150ms class="truncate">Dashboard</span>
    </a>

    {{-- INVENTARIO --}}
    @if($canInventario)
      <div class="pt-2">
        <button
          type="button"
          title="Inventario"
          class="w-full group flex items-center gap-3 rounded-2xl px-3 py-2 text-sm font-extrabold transition
                 {{ $invActive ? 'bg-slate-100 text-slate-900' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}"
          :class="$store.sidebar.collapsed ? 'justify-center' : ''"
          @click="toggleGroup('inventario')"
        >
          {{-- icon: cube --}}
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0
               {{ $invActive ? 'text-slate-900' : 'text-slate-500 group-hover:text-slate-900' }}"
               fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M21 7.5 12 12.75 3 7.5m18 0-9-5.25L3 7.5m18 0v9.75A2.25 2.25 0 0 1 19.875 19.2L12 23.25 4.125 19.2A2.25 2.25 0 0 1 3 17.25V7.5"/>
          </svg>

          <span class="flex-1 text-left" x-show="!$store.sidebar.collapsed" x-transition.opacity.duration.150ms>
            Inventario
          </span>

          {{-- chevron --}}
          <svg x-show="!$store.sidebar.collapsed" xmlns="http://www.w3.org/2000/svg"
               class="h-5 w-5 text-slate-400 transition-transform"
               :class="open.inventario ? 'rotate-180' : ''"
               fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
          </svg>
        </button>

        {{-- submenu inventario --}}
        <div
          x-show="open.inventario && !$store.sidebar.collapsed"
          x-transition:enter="transition ease-out duration-200"
          x-transition:enter-start="opacity-0 -translate-y-1"
          x-transition:enter-end="opacity-100 translate-y-0"
          x-transition:leave="transition ease-in duration-150"
          x-transition:leave-start="opacity-100 translate-y-0"
          x-transition:leave-end="opacity-0 -translate-y-1"
          class="mt-1 pl-4"
        >
          <div class="border-l border-slate-200 pl-3 space-y-1">

            @can('inventario.ver')
              <a href="{{ route('inventario.existencias') }}"
                 title="Existencias"
                 class="group flex items-center gap-3 rounded-xl px-2 py-2 text-sm font-bold transition
                        {{ request()->routeIs('inventario.existencias') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{-- icon: list --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4
                     {{ request()->routeIs('inventario.existencias') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700' }}"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8.25 6.75h12m-12 5.25h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm0 5.25h.007v.008H3.75V12Zm0 5.25h.007v.008H3.75v-.008Z"/>
                </svg>
                <span>Existencias</span>
              </a>
            @endcan

            @can('inventario.ver')
              <a href="{{ route('inventario.movimientos') }}"
                 title="Movimientos"
                 class="group flex items-center gap-3 rounded-xl px-2 py-2 text-sm font-bold transition
                        {{ request()->routeIs('inventario.movimientos*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{-- icon: arrows-right-left --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4
                     {{ request()->routeIs('inventario.movimientos*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700' }}"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M7.5 7.5h12m0 0-3-3m3 3-3 3M16.5 16.5h-12m0 0 3 3m-3-3 3-3"/>
                </svg>
                <span>Movimientos</span>
              </a>
            @endcan

            @can('materiales.ver')
              <a href="{{ route('inventario.materiales') }}"
                 title="Materiales"
                 class="group flex items-center gap-3 rounded-xl px-2 py-2 text-sm font-bold transition
                        {{ request()->routeIs('inventario.materiales*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{-- icon: archive-box --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4
                     {{ request()->routeIs('inventario.materiales*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700' }}"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M20.25 7.5v11.25A2.25 2.25 0 0 1 18 21H6a2.25 2.25 0 0 1-2.25-2.25V7.5m16.5 0A2.25 2.25 0 0 0 18 5.25H6A2.25 2.25 0 0 0 3.75 7.5m16.5 0v.375c0 .621-.504 1.125-1.125 1.125H4.875A1.125 1.125 0 0 1 3.75 7.875V7.5"/>
                </svg>
                <span>Materiales</span>
              </a>
            @endcan

            @can('almacenes.ver')
              <a href="{{ route('inventario.almacenes') }}"
                 title="Almacenes"
                 class="group flex items-center gap-3 rounded-xl px-2 py-2 text-sm font-bold transition
                        {{ request()->routeIs('inventario.almacenes*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{-- icon: building-storefront --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4
                     {{ request()->routeIs('inventario.almacenes*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700' }}"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 21h16.5M4.5 3h15l1.5 6H3L4.5 3Zm1.5 6v12m12-12v12M9 21v-6h6v6"/>
                </svg>
                <span>Almacenes</span>
              </a>
            @endcan

            @can('kardex.ver')
              <a href="{{ route('inventario.kardex') }}"
                 title="Kardex"
                 class="group flex items-center gap-3 rounded-xl px-2 py-2 text-sm font-bold transition
                        {{ request()->routeIs('inventario.kardex*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{-- icon: clock --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4
                     {{ request()->routeIs('inventario.kardex*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700' }}"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6v6l4 2m6-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0Z"/>
                </svg>
                <span>Kardex</span>
              </a>
            @endcan

          </div>
        </div>

        {{-- modo colapsado: accesos rápidos (opcional) --}}
        <div x-show="$store.sidebar.collapsed" class="mt-1 space-y-1">
          <a href="{{ route('inventario.existencias') }}" title="Existencias"
             class="group flex items-center justify-center rounded-2xl px-3 py-2 transition hover:bg-slate-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500 group-hover:text-slate-900"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M8.25 6.75h12m-12 5.25h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm0 5.25h.007v.008H3.75V12Zm0 5.25h.007v.008H3.75v-.008Z"/>
            </svg>
          </a>
        </div>
      </div>
    @endif

    {{-- CONFIGURACIÓN / ADMIN --}}
    @if($canConfig)
      <div class="pt-2">
        <button
          type="button"
          title="Configuración"
          class="w-full group flex items-center gap-3 rounded-2xl px-3 py-2 text-sm font-extrabold transition
                 {{ $adminActive ? 'bg-slate-100 text-slate-900' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}"
          :class="$store.sidebar.collapsed ? 'justify-center' : ''"
          @click="toggleGroup('config')"
        >
          {{-- icon: cog --}}
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0
               {{ $adminActive ? 'text-slate-900' : 'text-slate-500 group-hover:text-slate-900' }}"
               fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M4.5 12a7.5 7.5 0 0 1 13.995-3.375M19.5 12a7.5 7.5 0 0 1-13.995 3.375M12 9.75a2.25 2.25 0 1 1 0 4.5 2.25 2.25 0 0 1 0-4.5Z"/>
          </svg>

          <span class="flex-1 text-left" x-show="!$store.sidebar.collapsed" x-transition.opacity.duration.150ms>
            Configuración
          </span>

          <svg x-show="!$store.sidebar.collapsed" xmlns="http://www.w3.org/2000/svg"
               class="h-5 w-5 text-slate-400 transition-transform"
               :class="open.config ? 'rotate-180' : ''"
               fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
          </svg>
        </button>

        <div
          x-show="open.config && !$store.sidebar.collapsed"
          x-transition:enter="transition ease-out duration-200"
          x-transition:enter-start="opacity-0 -translate-y-1"
          x-transition:enter-end="opacity-100 translate-y-0"
          x-transition:leave="transition ease-in duration-150"
          x-transition:leave-start="opacity-100 translate-y-0"
          x-transition:leave-end="opacity-0 -translate-y-1"
          class="mt-1 pl-4"
        >
          <div class="border-l border-slate-200 pl-3 space-y-1">

            @can('usuarios.ver')
              <a href="{{ route('admin.usuarios') }}" title="Usuarios"
                 class="group flex items-center gap-3 rounded-xl px-2 py-2 text-sm font-bold transition
                        {{ request()->routeIs('admin.usuarios*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{-- icon: user --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4
                     {{ request()->routeIs('admin.usuarios*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700' }}"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.118a7.5 7.5 0 0 1 15 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.5-1.632Z"/>
                </svg>
                <span>Usuarios</span>
              </a>
            @endcan

            @can('roles.ver')
              <a href="{{ route('admin.roles') }}" title="Roles"
                 class="group flex items-center gap-3 rounded-xl px-2 py-2 text-sm font-bold transition
                        {{ request()->routeIs('admin.roles*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{-- icon: shield-check --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4
                     {{ request()->routeIs('admin.roles*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700' }}"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75 11.25 15 15 9.75M12 2.25l7.5 4.5v6.75c0 4.477-3.248 8.385-7.5 9.75-4.252-1.365-7.5-5.273-7.5-9.75V6.75L12 2.25Z"/>
                </svg>
                <span>Roles</span>
              </a>
            @endcan

            @can('permisos.ver')
              <a href="{{ route('admin.permisos') }}" title="Permisos"
                 class="group flex items-center gap-3 rounded-xl px-2 py-2 text-sm font-bold transition
                        {{ request()->routeIs('admin.permisos*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{-- icon: key --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4
                     {{ request()->routeIs('admin.permisos*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700' }}"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 5.25a4.5 4.5 0 1 1-8.584 2.11c-.23.52-.35 1.094-.35 1.69v2.25h3v3h3v3h3V9.05a4.48 4.48 0 0 1 1.934-3.8Z"/>
                </svg>
                <span>Permisos</span>
              </a>
            @endcan

            @can('empresas.ver')
              <a href="{{ route('admin.empresas') }}" title="Empresas"
                 class="group flex items-center gap-3 rounded-xl px-2 py-2 text-sm font-bold transition
                        {{ request()->routeIs('admin.empresas*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{-- icon: building-office-2 --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4
                     {{ request()->routeIs('admin.empresas*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700' }}"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 21h16.5M6 21V4.5A2.25 2.25 0 0 1 8.25 2.25h7.5A2.25 2.25 0 0 1 18 4.5V21M9 6.75h.008V6.758H9V6.75Zm0 3h.008V9.758H9V9.75Zm0 3h.008v.008H9v-.008Zm6-6h.008V6.758H15V6.75Zm0 3h.008V9.758H15V9.75Zm0 3h.008v.008H15v-.008Z"/>
                </svg>
                <span>Empresas</span>
              </a>
            @endcan

            @can('proyectos.ver')
              <a href="{{ route('admin.proyectos') }}" title="Proyectos"
                 class="group flex items-center gap-3 rounded-xl px-2 py-2 text-sm font-bold transition
                        {{ request()->routeIs('admin.proyectos*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{-- icon: folder --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4
                     {{ request()->routeIs('admin.proyectos*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700' }}"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 12.75V6A2.25 2.25 0 0 1 4.5 3.75h5.379a2.25 2.25 0 0 1 1.59.659l1.313 1.313A2.25 2.25 0 0 0 15.372 6H19.5A2.25 2.25 0 0 1 21.75 8.25v8.25A2.25 2.25 0 0 1 19.5 18.75H7.5"/>
                </svg>
                <span>Proyectos</span>
              </a>
            @endcan

            @can('miempresa.ver')
              <a href="{{ route('admin.mi_empresa.edit') }}" title="Mi empresa"
                 class="group flex items-center gap-3 rounded-xl px-2 py-2 text-sm font-bold transition
                        {{ request()->routeIs('admin.mi_empresa.*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{-- icon: identification --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4
                     {{ request()->routeIs('admin.mi_empresa.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-700' }}"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 9h3.75M15 12h3.75M4.5 6.75A2.25 2.25 0 0 1 6.75 4.5h10.5A2.25 2.25 0 0 1 19.5 6.75v10.5A2.25 2.25 0 0 1 17.25 19.5H6.75A2.25 2.25 0 0 1 4.5 17.25V6.75Zm3 2.25h.008V9.008H7.5V9Zm0 3h.008V12.008H7.5V12Zm0 3h.008V15.008H7.5V15Z"/>
                </svg>
                <span>Mi empresa</span>
              </a>
            @endcan

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
