<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FUDOUNAR - Inicio')</title>

    @php
        $contactSettings = \App\Models\ContactSetting::first();
        $adsenseId = $contactSettings?->adsense_id;
    @endphp

    @if($adsenseId)
        <!-- Google AdSense -->
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $adsenseId }}" crossorigin="anonymous"></script>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; }
        trix-toolbar [data-trix-button-group="file-tools"],
        trix-toolbar [data-trix-action="link"],
        trix-toolbar [data-trix-action="quote"],
        trix-toolbar [data-trix-action="code"] { 
            display: none !important; 
        }
    </style>
</head>
<body class="bg-white text-gray-900 min-h-screen flex flex-col">
    @include('partials.header')

    @yield('top_content')

    <main class="flex-grow w-full max-w-[800px] mx-auto p-5">
        @yield('content')
    </main>

    @include('partials.footer')
    @livewireScripts
</body>
</html>
