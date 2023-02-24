<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SISTEMA FACTURACION</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

     <!-- mi estilo -->
     <link rel= "stylesheet" type="text/css" href= "{{ asset('css/estilo.css') }}"> 
   
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="pruebalogin1">
    <div id="app" class="pruebalogin1">

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
