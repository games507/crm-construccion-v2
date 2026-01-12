{{-- resources/views/landing.blade.php --}}
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>VerticeSoft — Control total y eficiencia</title>

  <meta name="description" content="VerticeSoft: sistema ERP/CRM para inventario y proyectos de construcción. Control total, trazabilidad y eficiencia operativa.">
  <meta name="theme-color" content="#1e40af">

  @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="min-h-screen bg-gradient-to-b from-indigo-50 via-slate-50 to-white text-slate-900">

  {{-- TOPBAR (RESPONSIVE) --}}
  <header x-data="{ mobile:false }" class="sticky top-0 z-50 border-b border-slate-200/70 bg-white/70 backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 py-3 flex items-center justify-between gap-3">
      <a href="#inicio" class="flex items-center gap-3 min-w-0">
        <div class="h-10 w-10 rounded-2xl bg-indigo-600 shadow-sm"></div>
        <div class="min-w-0">
          <div class="font-black leading-tight truncate">VerticeSoft</div>
          <div class="text-xs text-slate-500 -mt-0.5 truncate">Inventario • Proyectos • Control y trazabilidad</div>
        </div>
      </a>

      {{-- NAV desktop --}}
      <nav class="hidden lg:flex items-center gap-1">
        <a href="#beneficios" class="px-3 h-10 inline-flex items-center rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-100">Beneficios</a>
        <a href="#modulos" class="px-3 h-10 inline-flex items-center rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-100">Módulos</a>
        <a href="#como-funciona" class="px-3 h-10 inline-flex items-center rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-100">Cómo funciona</a>
        <a href="#contacto" class="px-3 h-10 inline-flex items-center rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-100">Contacto</a>
      </nav>

      <div class="flex items-center gap-2 shrink-0">
        {{-- botón menú móvil --}}
        <button type="button"
          class="lg:hidden inline-flex items-center justify-center h-10 w-10 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 active:scale-[.98]"
          @click="mobile = !mobile"
          :aria-expanded="mobile.toString()"
          aria-label="Abrir menú"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>

        <a href="{{ route('login') }}"
           class="inline-flex items-center justify-center px-4 h-10 rounded-xl bg-indigo-600 text-white font-extrabold shadow-sm hover:bg-indigo-700">
          Entrar
        </a>
      </div>
    </div>

    {{-- panel móvil --}}
    <div x-show="mobile" x-transition.opacity.duration.150ms class="lg:hidden border-t border-slate-200/70 bg-white/80">
      <div class="mx-auto max-w-7xl px-4 py-3 grid gap-2">
        <a @click="mobile=false" href="#beneficios" class="h-11 rounded-xl px-3 inline-flex items-center font-semibold text-slate-700 hover:bg-slate-100">Beneficios</a>
        <a @click="mobile=false" href="#modulos" class="h-11 rounded-xl px-3 inline-flex items-center font-semibold text-slate-700 hover:bg-slate-100">Módulos</a>
        <a @click="mobile=false" href="#como-funciona" class="h-11 rounded-xl px-3 inline-flex items-center font-semibold text-slate-700 hover:bg-slate-100">Cómo funciona</a>
        <a @click="mobile=false" href="#contacto" class="h-11 rounded-xl px-3 inline-flex items-center font-semibold text-slate-700 hover:bg-slate-100">Contacto</a>
      </div>
    </div>
  </header>

  {{-- HERO --}}
  <section id="inicio" class="mx-auto max-w-7xl px-4 pt-10 pb-10 sm:pt-14">
    <div class="grid lg:grid-cols-2 gap-10 items-center">

      <div>
        <div class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-extrabold text-indigo-700">
          Control total • Eficiencia • Trazabilidad
        </div>

        <h1 class="mt-4 text-3xl sm:text-4xl xl:text-5xl font-black tracking-tight text-slate-900">
          VerticeSoft para Inventario y Proyectos de Construcción
        </h1>

        <p class="mt-4 text-slate-600 text-base sm:text-lg leading-relaxed">
          Centraliza inventario, proyectos, movimientos y seguimiento en una sola plataforma.
          Reduce errores, mejora la coordinación y toma decisiones con datos en tiempo real.
        </p>

        {{-- CTAs responsive: full width en móvil --}}
        <div class="mt-6 grid grid-cols-1 sm:flex sm:flex-wrap gap-3">
          <a href="{{ route('login') }}"
             class="w-full sm:w-auto inline-flex items-center justify-center px-5 h-11 rounded-xl bg-indigo-600 text-white font-extrabold shadow-sm hover:bg-indigo-700">
            Entrar al sistema
          </a>

          <a href="#contacto"
             class="w-full sm:w-auto inline-flex items-center justify-center px-5 h-11 rounded-xl bg-white text-slate-800 font-extrabold border border-slate-200 hover:bg-slate-50">
            Solicitar demo
          </a>
        </div>

        <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-3">
          <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-sm font-black">Multi-empresa</div>
            <div class="mt-1 text-xs text-slate-500">Contexto por empresa (Super Admin)</div>
          </div>
          <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-sm font-black">Roles & permisos</div>
            <div class="mt-1 text-xs text-slate-500">Acceso controlado por perfil</div>
          </div>
          <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-sm font-black">Trazabilidad</div>
            <div class="mt-1 text-xs text-slate-500">Auditoría de movimientos</div>
          </div>
        </div>
      </div>

      {{-- MOCKUP / PREVIEW --}}
      <div class="rounded-3xl border border-slate-200 bg-white shadow-[0_20px_60px_rgba(2,6,23,.10)] overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex items-center justify-between">
          <div class="font-black">Vista general</div>
          <div class="text-xs font-semibold text-slate-500">Portal interno</div>
        </div>

        <div class="p-6">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
              <div class="text-xs text-slate-500">Inventario</div>
              <div class="mt-2 text-2xl font-black">Existencias</div>
              <div class="text-xs text-slate-500 mt-1">Stock, entradas, salidas</div>
            </div>
            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
              <div class="text-xs text-slate-500">Proyectos</div>
              <div class="mt-2 text-2xl font-black">Seguimiento</div>
              <div class="text-xs text-slate-500 mt-1">Planificación y control</div>
            </div>

            <div class="sm:col-span-2 rounded-2xl bg-slate-50 border border-slate-200 p-4">
              <div class="text-xs text-slate-500">Actividad reciente</div>
              <div class="mt-3 space-y-2 text-sm">
                <div class="flex items-center justify-between">
                  <span class="font-semibold">Entrada</span>
                  <span class="text-slate-500">Material • Almacén</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="font-semibold">Salida</span>
                  <span class="text-slate-500">Obra • Proyecto</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="font-semibold">Actualización</span>
                  <span class="text-slate-500">Estado • Registro</span>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="h-11 rounded-xl bg-indigo-600"></div>
            <div class="h-11 rounded-xl bg-slate-200"></div>
          </div>
        </div>
      </div>

    </div>
  </section>

  {{-- BENEFICIOS --}}
  <section id="beneficios" class="bg-slate-50 border-y border-slate-200/70">
    <div class="mx-auto max-w-7xl px-4 py-12">
      <div class="flex items-end justify-between gap-4 flex-wrap">
        <div>
          <h2 class="text-2xl sm:text-3xl font-black">¿Por qué VerticeSoft?</h2>
          <p class="mt-2 text-slate-600 max-w-2xl">
            Menos improvisación, más control. Ordena procesos, reduce errores y mejora coordinación entre áreas.
          </p>
        </div>
        <a href="#contacto"
           class="inline-flex items-center justify-center px-4 h-10 rounded-xl bg-white border border-slate-200 text-slate-800 font-extrabold hover:bg-slate-100">
          Hablemos
        </a>
      </div>

      <div class="mt-8 grid md:grid-cols-3 gap-4">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
          <div class="text-sm font-black">Centralización de datos</div>
          <p class="mt-2 text-sm text-slate-600 leading-relaxed">
            Información unificada para inventario, proyectos, usuarios y control administrativo.
          </p>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
          <div class="text-sm font-black">Colaboración en tiempo real</div>
          <p class="mt-2 text-sm text-slate-600 leading-relaxed">
            Equipos alineados con registros claros, movimientos auditables y flujos ordenados.
          </p>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
          <div class="text-sm font-black">Eficiencia y reducción de errores</div>
          <p class="mt-2 text-sm text-slate-600 leading-relaxed">
            Menos reprocesos, más productividad. Control por roles y trazabilidad por usuario.
          </p>
        </div>
      </div>
    </div>
  </section>

  {{-- MODULOS --}}
  <section id="modulos" class="mx-auto max-w-7xl px-4 py-12">
    <h2 class="text-2xl sm:text-3xl font-black">Módulos principales</h2>
    <p class="mt-2 text-slate-600 max-w-2xl">
      Diseñado para el trabajo diario y para control administrativo, sin complicarte el flujo.
    </p>

    <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
        <div class="text-lg font-black">Inventario inteligente</div>
        <ul class="mt-3 space-y-2 text-sm text-slate-600">
          <li>• Existencias y stock</li>
          <li>• Movimientos (entradas/salidas/ajustes)</li>
          <li>• Kardex por material</li>
          <li>• Almacenes</li>
        </ul>
      </div>

      <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
        <div class="text-lg font-black">Gestión de proyectos</div>
        <ul class="mt-3 space-y-2 text-sm text-slate-600">
          <li>• Planificación</li>
          <li>• Ejecución</li>
          <li>• Certificaciones</li>
          <li>• Cierre</li>
        </ul>
      </div>

      <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
        <div class="text-lg font-black">CRM integrado</div>
        <ul class="mt-3 space-y-2 text-sm text-slate-600">
          <li>• Clientes y contacto</li>
          <li>• Seguimiento de oportunidades</li>
          <li>• Proveedores / subcontratistas</li>
          <li>• Historial y gestión</li>
        </ul>
      </div>

      <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
        <div class="text-lg font-black">Usuarios, roles y permisos</div>
        <p class="mt-3 text-sm text-slate-600 leading-relaxed">
          Controla quién ve qué. Perfiles por rol y permisos por módulo.
        </p>
      </div>

      <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
        <div class="text-lg font-black">Multi-empresa</div>
        <p class="mt-3 text-sm text-slate-600 leading-relaxed">
          Operación por empresa seleccionada. Ideal para administrar varios entornos desde un panel central.
        </p>
      </div>

      <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
        <div class="text-lg font-black">Reportes & trazabilidad</div>
        <p class="mt-3 text-sm text-slate-600 leading-relaxed">
          Indicadores, consultas y auditoría para decisiones rápidas con información confiable.
        </p>
      </div>
    </div>
  </section>

  {{-- CÓMO FUNCIONA --}}
  <section id="como-funciona" class="bg-white border-y border-slate-200/70">
    <div class="mx-auto max-w-7xl px-4 py-12">
      <div class="grid lg:grid-cols-2 gap-10 items-start">
        <div>
          <h2 class="text-2xl sm:text-3xl font-black">Cómo funciona (flujo simple)</h2>
          <p class="mt-2 text-slate-600">
            Un flujo claro que se adapta a tu operación, con control y visibilidad en cada etapa.
          </p>

          <div class="mt-6 space-y-3">
            @foreach([
              ['1', 'Configura tu entorno', 'Empresas, usuarios, roles y permisos.'],
              ['2', 'Controla inventario', 'Registra existencias, almacenes y movimientos.'],
              ['3', 'Gestiona proyectos', 'Planificación, ejecución y seguimiento ordenado.'],
              ['4', 'Mide y mejora', 'Reportes, trazabilidad y decisiones basadas en datos.'],
            ] as $step)
              <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                <div class="flex items-start gap-3">
                  <div class="h-8 w-8 rounded-xl bg-indigo-600 text-white grid place-items-center font-black">{{ $step[0] }}</div>
                  <div>
                    <div class="font-black text-slate-900">{{ $step[1] }}</div>
                    <div class="text-sm text-slate-600 mt-1">{{ $step[2] }}</div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        {{-- KPIs --}}
        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
          <div class="flex items-end justify-between gap-4">
            <div>
              <div class="text-sm font-black">Beneficios medibles</div>
              <div class="text-xs text-slate-500 mt-1">Resultados típicos al ordenar procesos</div>
            </div>
            <span class="text-xs font-extrabold px-3 py-1 rounded-full bg-white border border-slate-200 text-slate-700">
              Enfoque: eficiencia
            </span>
          </div>

          <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="rounded-2xl bg-white border border-slate-200 p-4">
              <div class="text-xs text-slate-500">Reducción de sobrecostos</div>
              <div class="mt-2 text-3xl font-black">↓</div>
              <div class="text-xs text-slate-500 mt-1">Mejor control y trazabilidad</div>
            </div>
            <div class="rounded-2xl bg-white border border-slate-200 p-4">
              <div class="text-xs text-slate-500">Productividad</div>
              <div class="mt-2 text-3xl font-black">↑</div>
              <div class="text-xs text-slate-500 mt-1">Menos reprocesos</div>
            </div>
            <div class="rounded-2xl bg-white border border-slate-200 p-4">
              <div class="text-xs text-slate-500">Satisfacción</div>
              <div class="mt-2 text-3xl font-black">✓</div>
              <div class="text-xs text-slate-500 mt-1">Mejor seguimiento</div>
            </div>
          </div>

          <div class="mt-5 rounded-2xl bg-white border border-slate-200 p-4">
            <div class="text-sm font-black">Tecnología & movilidad</div>
            <ul class="mt-2 text-sm text-slate-600 space-y-1">
              <li>• Acceso desde cualquier dispositivo</li>
              <li>• Flujo moderno y rápido</li>
              <li>• Preparado para crecimiento y módulos futuros</li>
            </ul>
          </div>
        </div>

      </div>
    </div>
  </section>

  {{-- CTA FINAL --}}
  <section class="mx-auto max-w-7xl px-4 py-12">
    <div class="rounded-3xl border border-slate-200 bg-indigo-600 text-white p-7 sm:p-10 shadow-[0_20px_60px_rgba(2,6,23,.12)]">
      <div class="grid lg:grid-cols-2 gap-8 items-center">
        <div>
          <h2 class="text-2xl sm:text-3xl font-black">
            Da el paso hacia una gestión eficiente y rentable
          </h2>
          <p class="mt-2 text-white/85">
            Implementa control real sobre inventario y proyectos, con una plataforma moderna y segura.
          </p>
        </div>

        <div class="grid grid-cols-1 sm:flex gap-3 sm:justify-end">
          <a href="{{ route('login') }}"
             class="w-full sm:w-auto inline-flex items-center justify-center px-5 h-11 rounded-xl bg-white text-indigo-700 font-extrabold shadow-sm hover:bg-indigo-50">
            Entrar al sistema
          </a>
          <a href="#contacto"
             class="w-full sm:w-auto inline-flex items-center justify-center px-5 h-11 rounded-xl bg-indigo-500/30 border border-white/30 text-white font-extrabold hover:bg-indigo-500/40">
            Solicitar demo
          </a>
        </div>
      </div>
    </div>
  </section>

  {{-- CONTACTO --}}
  <section id="contacto" class="bg-slate-50 border-t border-slate-200/70">
    <div class="mx-auto max-w-7xl px-4 py-12">
      <div class="grid lg:grid-cols-2 gap-8 items-start">
        <div>
          <h2 class="text-2xl sm:text-3xl font-black">Contacto</h2>
          <p class="mt-2 text-slate-600">
            Para mayor información, puede comunicarse al <span class="font-extrabold text-slate-900">300.0000</span> o escribir a nuestro WhatsApp
            <span class="font-extrabold text-slate-900">6827.26353</span>.
            Con gusto le orientaremos.
          </p>

          @php
            $wa = '50768272635';
            $waMsg = rawurlencode('Hola, me interesa una demo de VerticeSoft. ¿Podemos coordinar?');
          @endphp

          <div class="mt-5 grid grid-cols-1 sm:flex gap-3">
            <a href="https://wa.me/{{ $wa }}?text={{ $waMsg }}"
               target="_blank" rel="noopener"
               class="w-full sm:w-auto inline-flex items-center justify-center px-5 h-11 rounded-xl bg-white border border-slate-200 text-slate-900 font-extrabold hover:bg-slate-100">
              WhatsApp
            </a>
            <a href="tel:300.0000"
               class="w-full sm:w-auto inline-flex items-center justify-center px-5 h-11 rounded-xl bg-white border border-slate-200 text-slate-900 font-extrabold hover:bg-slate-100">
              Llamar 300.0000
            </a>
          </div>

          <div class="mt-6 text-xs text-slate-500">
            *La disponibilidad de módulos puede variar según el plan/implementación.
          </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
          <div class="text-sm font-black">Checklist para una demo</div>
          <ul class="mt-3 text-sm text-slate-600 space-y-2">
            <li>• ¿Cuántas empresas/áreas manejarán?</li>
            <li>• ¿Qué flujo de inventario usan (entradas/salidas/traslados)?</li>
            <li>• ¿Qué etapas del proyecto necesitan controlar?</li>
            <li>• ¿Quiénes serán administradores y usuarios finales?</li>
          </ul>

          <div class="mt-5 rounded-2xl bg-slate-50 border border-slate-200 p-4">
            <div class="text-xs font-extrabold text-slate-700 uppercase tracking-wide">Acceso</div>
            <div class="mt-2 grid grid-cols-1 sm:flex gap-2">
              <a href="{{ route('login') }}"
                 class="w-full sm:w-auto inline-flex items-center justify-center px-4 h-10 rounded-xl bg-indigo-600 text-white font-extrabold hover:bg-indigo-700">
                Entrar
              </a>
              <a href="#inicio"
                 class="w-full sm:w-auto inline-flex items-center justify-center px-4 h-10 rounded-xl bg-white border border-slate-200 text-slate-900 font-extrabold hover:bg-slate-100">
                Inicio
              </a>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  {{-- FOOTER INSTITUCIONAL --}}
  <footer class="bg-[#002d69] text-white">
    <div class="mx-auto max-w-7xl px-4 py-10">
      <div class="grid md:grid-cols-2 gap-6 items-start">
        <div>
          <div class="text-lg font-black">VerticeSoft</div>
          <p class="mt-2 text-white/85 text-sm leading-relaxed">
            Plataforma para inventario y proyectos con control por roles, trazabilidad y operación moderna.
          </p>
        </div>
        <div class="md:text-right">
          <div class="text-sm text-white/80">© {{ date('Y') }} — VerticeSoft</div>
          <div class="text-xs text-white/70 mt-1">Soporte: 300.000 • WhatsApp: 6827.2635</div>
        </div>
      </div>
    </div>
  </footer>

</body>
</html>
