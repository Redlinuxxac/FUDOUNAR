<!DOCTYPE html>
<html>
<head>
    <title>FUDOUNAR ! @yield('title') </title>
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fudounar.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body>
    <header id="header">
        <img src="{{ asset('img/logo.jpg') }}" alt="Logo Fudcounar">
            @yield('menu')
    </header>
    <div class="contenedor">
        <div class="div-dominicana"></div>
        <div class="div-aruba"></div>
    </div>

    <main>
        <div class="container-sm"">     
            @yield('content')
        </div>  
    </main>
    @yield('footer')

    @yield('script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>