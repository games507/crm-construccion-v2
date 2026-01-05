<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Sistema integral para la gestión de proyectos de construcción: presupuestos, cronogramas, control de obra y colaboración en tiempo real.">
    <title>ConstruApp – Gestión inteligente para constructores</title>
    @vite('resources/css/app.css')
    <!-- Puedes añadir Google Fonts si quieres -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white">
    <!-- Hero Section -->
    <div class="relative bg-gray-900 overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="relative z-10 pb-8 bg-gray-900 sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                <svg class="hidden lg:block absolute right-0 inset-y-0 h-full w-48 text-gray-900 transform translate-x-1/2" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                    <polygon points="50,0 100,0 50,100 0,100" />
                </svg>

                <div class="pt-10 px-4 sm:px-6 lg:px-8">
                    <nav class="relative flex items-center justify-between sm:h-10">
                        <div class="flex items-center flex-shrink-0">
                            <span class="text-white text-2xl font-bold">ConstruApp</span>
                        </div>
                        <div class="hidden md:block">
                            <a href="{{ route('login') }}" class="text-base font-medium text-white hover:text-gray-300">Iniciar sesión</a>
                        </div>
                    </nav>
                </div>

                <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                    <div class="sm:text-center lg:text-left">
                        <h1 class="text-4xl tracking-tight font-extrabold text-white sm:text-5xl md:text-6xl">
                            <span class="block">Gestiona tus obras</span>
                            <span class="block text-blue-400">como un profesional</span>
                        </h1>
                        <p class="mt-3 text-base text-gray-300 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                            Sistema todo-en-uno para contratistas: presupuestos precisos, cronogramas, control de gastos y colaboración en tiempo real.
                        </p>
                        <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                            <div class="rounded-md shadow">
                                <a href="{{ route('register') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:py-4 md:text-lg md:px-10">
                                    Comenzar gratis
                                </a>
                            </div>
                            <div class="mt-3 sm:mt-0 sm:ml-3">
                                <a href="#features" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-blue-400 bg-gray-800 hover:bg-gray-700 md:py-4 md:text-lg md:px-10">
                                    Ver características
                                </a>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
            <img class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full" src="https://images.unsplash.com/photo-1504307651254-35680f32b326?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="Obra en construcción">
        </div>
    </div>

    <!-- Features -->
    <div id="features" class="py-12 bg-white sm:py-16 lg:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h2 class="text-base text-blue-600 font-semibold tracking-wide uppercase">Características</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Herramientas pensadas para constructores
                </p>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
                    Todo lo que necesitas en una sola plataforma.
                </p>
            </div>

            <div class="mt-10">
                <dl class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-10">
                    <div class="relative">
                        <dt>
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                                💰
                            </div>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Presupuestos inteligentes</p>
                        </dt>
                        <dd class="mt-2 ml-16 text-base text-gray-500">
                            Genera cotizaciones detalladas con base en precios actuales del mercado y materiales.
                        </dd>
                    </div>

                    <div class="relative">
                        <dt>
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                                🗓️
                            </div>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Cronogramas dinámicos</p>
                        </dt>
                        <dd class="mt-2 ml-16 text-base text-gray-500">
                            Planifica etapas, asigna recursos y recibe alertas de retrasos automáticamente.
                        </dd>
                    </div>

                    <div class="relative">
                        <dt>
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                                📱
                            </div>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900">App móvil incluida</p>
                        </dt>
                        <dd class="mt-2 ml-16 text-base text-gray-500">
                            Registra avances, gastos e incidencias desde la obra, en tiempo real.
                        </dd>
                    </div>

                    <div class="relative">
                        <dt>
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                                👥
                            </div>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Colaboración en equipo</p>
                        </dt>
                        <dd class="mt-2 ml-16 text-base text-gray-500">
                            Comparte información con tu equipo, proveedores y clientes sin salir de la plataforma.
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="bg-gray-50">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
            <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                <span class="block">¿Listo para optimizar tu próxima obra?</span>
                <span class="block text-blue-600">Únete hoy mismo. Es gratis.</span>
            </h2>
            <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
                <div class="inline-flex rounded-md shadow">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Crear cuenta
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-base text-gray-400">
                &copy; {{ date('Y') }} ConstruApp. Todos los derechos reservados.
            </p>
        </div>
    </footer>
</body>
</html>