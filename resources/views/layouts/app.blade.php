<!DOCTYPE html>
<html>
<head>
    <title>Fudcounar ! @yield('title') </title>
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fudounar.css') }}">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
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
    @yield('footer')
    @yield('script')
</body>
</html>