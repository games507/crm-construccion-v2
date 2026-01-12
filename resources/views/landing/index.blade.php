{{-- resources/views/landing.blade.php --}}
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>ERP/CRM Municipal — Plataforma de Gestión</title>

  @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="min-h-screen bg-gradient-to-b from-indigo-50 via-slate-50 to-white text-slate-900">

  {{-- Top bar --}}
  <header class="sticky top-0 z-50 border-b border-slate-200/70 bg-white/70 backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 py-3 flex items-center justify-between gap-3">
      <div class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-2xl bg-indigo-600 shadow-sm"></div>
        <div>
          <div class="font-black leading-tight">ERP/CRM Municipal</div>
          <div class="text-xs text-slate-500 -mt-0.5">Gestión integral por módulos</div>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <a href="#modulos" class="hidden sm:inline-flex px-3 h-10 items-center rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-100">
          Módulos
        </a>
        <a href="#beneficios" class="hidden sm:inline-flex px-3 h-10 items-center rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-100">
          Beneficios
        </a>

        <a href="{{ route('login') }}"
           class="inline-flex items-center justify-center px-4 h-10 rounded-xl bg-indigo-600 text-white font-bold shadow-sm hover:bg-indigo-700">
          Entrar al sistema
        </a>
      </div>
    </div>
  </header>

  {{-- Hero --}}
  <section class="mx-auto max-w-7xl px-4 py-12 sm:py-16">
    <div class="grid lg:grid-cols-2 gap-10 items-center">
      <div>
        <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900">
          Plataforma moderna para administrar inventario, proyectos y operaciones municipales
        </h1>
        <p class="mt-4 text-slate-600 text-base sm:text-lg leading-relaxed">
          Un sistema unificado con control por roles, trazabilidad, multi-empresa y paneles claros para agilizar el trabajo diario.
        </p>

        <div class="mt-6 flex flex-wrap gap-3">
          <a href="{{ route('login') }}"
             class="inline-flex items-center justify-center px-5 h-11 rounded-xl bg-indigo-600 text-white font-bold shadow-sm hover:bg-indigo-700">
            Acceder / Iniciar sesión
          </a>

          <a href="#modulos"
             class="inline-flex items-center justify-center px-5 h-11 rounded-xl bg-white text-slate-800 font-bold border border-slate-200 hover:bg-slate-50">
            Ver módulos
          </a>
        </div>

        <div class="mt-8 grid grid-cols-2 sm:grid-cols-3 gap-3">
          @foreach ([
            ['Trazabilidad', 'Movimientos y auditoría'],
            ['Multi-empresa', 'Contexto por empresa'],
            ['Roles & permisos', 'Accesos controlados'],
          ] as $b)
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
              <div class="text-sm font-extrabold">{{ $b[0] }}</div>
              <div class="text-xs text-slate-500 mt-1">{{ $b[1] }}</div>
            </div>
          @endforeach
        </div>
      </div>

      {{-- Mockup / card --}}
      <div class="rounded-3xl border border-slate-200 bg-white shadow-[0_20px_60px_rgba(2,6,23,.10)] overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex items-center justify-between">
          <div class="font-black">Vista previa</div>
          <div class="text-xs text-slate-500">Portal interno / Dashboard</div>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-2 gap-4">
            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
              <div class="text-xs text-slate-500">Alertas</div>
              <div class="mt-2 text-2xl font-black">8</div>
              <div class="text-xs text-slate-500 mt-1">Stock bajo / vencimientos</div>
            </div>
            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
              <div class="text-xs text-slate-500">Movimientos</div>
              <div class="mt-2 text-2xl font-black">124</div>
              <div class="text-xs text-slate-500 mt-1">Últimos 7 días</div>
            </div>
            <div class="col-span-2 rounded-2xl bg-slate-50 border border-slate-200 p-4">
              <div class="text-xs text-slate-500">Actividad reciente</div>
              <ul class="mt-2 space-y-2 text-sm">
                <li class="flex justify-between"><span class="font-semibold">Entrada</span><span class="text-slate-500">Material X</span></li>
                <li class="flex justify-between"><span class="font-semibold">Salida</span><span class="text-slate-500">Almacén Central</span></li>
                <li class="flex justify-between"><span class="font-semibold">Proyecto</span><span class="text-slate-500">Actualización</span></li>
              </ul>
            </div>
          </div>

          <div class="mt-5 flex gap-3">
            <div class="h-10 flex-1 rounded-xl bg-indigo-600"></div>
            <div class="h-10 w-28 rounded-xl bg-slate-200"></div>
          </div>
        </div>
      </div>

    </div>
  </section>

  {{-- Módulos --}}
  <section id="modulos" class="mx-auto max-w-7xl px-4 pb-14">
    <div class="flex items-end justify-between gap-4">
      <div>
        <h2 class="text-2xl font-black">Módulos principales</h2>
        <p class="text-slate-600 mt-1">Organizados para operación diaria y administración.</p>
      </div>
    </div>

    <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
      @foreach ([
        ['Inventario', 'Materiales, almacenes, kardex, movimientos y existencias.'],
        ['Proyectos', 'Gestión y seguimiento de proyectos (módulo principal).'],
        ['Usuarios / Roles', 'Accesos por perfil, permisos y auditoría.'],
        ['Multi-empresa', 'Operación por empresa seleccionada (Super Admin).'],
        ['Reportes', 'Indicadores, exportaciones y trazabilidad.'],
        ['Configuración', 'Parámetros del sistema (solo admin).'],
      ] as $m)
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
          <div class="text-lg font-black">{{ $m[0] }}</div>
          <div class="text-sm text-slate-600 mt-2 leading-relaxed">{{ $m[1] }}</div>
        </div>
      @endforeach
    </div>
  </section>

  {{-- Beneficios --}}
  <section id="beneficios" class="bg-slate-50 border-y border-slate-200/70">
    <div class="mx-auto max-w-7xl px-4 py-14">
      <h2 class="text-2xl font-black">Beneficios</h2>
      <div class="mt-6 grid md:grid-cols-3 gap-4">
        @foreach ([
          ['Control y orden', 'Procesos estandarizados y centralizados.'],
          ['Trazabilidad', 'Historial de cambios y movimientos.'],
          ['Seguridad', 'Roles, permisos y contexto por empresa.'],
        ] as $b)
          <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="font-black">{{ $b[0] }}</div>
            <div class="text-sm text-slate-600 mt-2">{{ $b[1] }}</div>
          </div>
        @endforeach
      </div>

      <div class="mt-8 flex flex-wrap gap-3">
        <a href="{{ route('login') }}"
           class="inline-flex items-center justify-center px-5 h-11 rounded-xl bg-indigo-600 text-white font-bold shadow-sm hover:bg-indigo-700">
          Entrar al sistema
        </a>
        <a href="#"
           class="inline-flex items-center justify-center px-5 h-11 rounded-xl bg-white text-slate-800 font-bold border border-slate-200 hover:bg-slate-100">
          Solicitar demo
        </a>
      </div>
    </div>
  </section>

  {{-- Footer institucional --}}
  <footer class="bg-[#002d69] text-white">
    <div class="mx-auto max-w-7xl px-4 py-10">
      <div class="grid md:grid-cols-2 gap-6 items-start">
        <div>
          <div class="text-lg font-black">Alcaldía / Institución</div>
          <p class="mt-2 text-white/85 text-sm leading-relaxed">
            Para mayor información, puede comunicarse al <span class="font-bold">502-4411</span> o escribir a nuestro WhatsApp
            <span class="font-bold">6850-5613</span>. Con gusto le orientaremos.
          </p>
        </div>
        <div class="md:text-right">
          <div class="text-sm text-white/80">© {{ date('Y') }} — Plataforma de Gestión</div>
          <div class="text-xs text-white/70 mt-1">Versión institucional • Seguridad y trazabilidad</div>
        </div>
      </div>
    </div>
  </footer>

</body>
</html>
