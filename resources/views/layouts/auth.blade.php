<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>@yield('title','Iniciar sesión')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="min-h-screen w-full overflow-hidden">

  @yield('content')

</body>
</html>
