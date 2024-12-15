<!DOCTYPE html>
<html>
<head>
    <title>Fudcounar ! @yield('title') </title>
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fudounar.css') }}">
</head>
<body>
    <header id="header">
        <img src="img/logo.jpg" alt="Logo Fudcounar">
            @yield('menu')
    </header>
    <div class="contenedor">
        <div class="div-dominicana"></div>
        <div class="div-aruba"></div>
    </div>

    <main>
        @yield('content')  
    </main>

    <footer>
        <p>&copy; Fudcounar 2023</p>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>