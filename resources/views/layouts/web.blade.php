<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FUDOUNAR - Inicio')</title>

    <!-- SEO Básico y Google -->
    <meta name="description" content="@yield('meta_description', trim($__env->yieldContent('og_description', 'Fundación Dominicana de Urología Dr. Nelson Adames - Dedicados a la salud urológica, educación médica continua y apoyo a la comunidad.')))">
    <meta name="robots" content="@yield('meta_robots', 'index, follow')">
    <link rel="canonical" href="@yield('canonical_url', url()->current())">

    <link rel="icon" type="image/png" href="{{ asset('img/LogoMejorado.png') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Metadatos Open Graph / Redes Sociales -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('og_url', url()->current())">
    <meta property="og:title" content="@yield('og_title', trim($__env->yieldContent('title', config('app.name', 'FUDOUNAR'))))">
    <meta property="og:description" content="@yield('og_description', 'Fundación Dominicana de Urología Dr. Nelson Adames')">
    <meta property="og:image" content="@yield('og_image', asset('img/LogoMejorado.png'))">
    <meta property="og:image:secure_url" content="@yield('og_image', asset('img/LogoMejorado.png'))">
    <meta property="og:site_name" content="FUDOUNAR">
    <meta property="og:locale" content="es_DO">

    <!-- Twitter / X Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="@yield('og_url', url()->current())">
    <meta name="twitter:title" content="@yield('og_title', trim($__env->yieldContent('title', config('app.name', 'FUDOUNAR'))))">
    <meta name="twitter:description" content="@yield('og_description', 'Fundación Dominicana de Urología Dr. Nelson Adames')">
    <meta name="twitter:image" content="@yield('og_image', asset('img/LogoMejorado.png'))">

    @php
        $contactSettings = \App\Models\ContactSetting::first();
        $adsenseId = $contactSettings?->adsense_id;
        $gaId = $contactSettings?->google_analytics_id ?? config('services.google.analytics_id');
        $gscId = $contactSettings?->google_search_console_id ?? config('services.google.search_console_id');
    @endphp

    @if($gscId)
        <!-- Google Search Console Verification -->
        <meta name="google-site-verification" content="{{ $gscId }}">
    @endif

    @if($gaId)
        <!-- Google Analytics 4 (GA4) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $gaId }}');
        </script>
    @endif

    @if($adsenseId)
        <!-- Google AdSense -->
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $adsenseId }}" crossorigin="anonymous"></script>
    @endif

    <!-- Schema.org JSON-LD para Google (Organización Médica / ONG) -->
    @php
        $orgSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'MedicalOrganization',
            'name' => 'FUDOUNAR - Fundación Dominicana de Urología Dr. Nelson Adames',
            'url' => url('/'),
            'logo' => asset('img/LogoMejorado.png'),
            'description' => 'Fundación Dominicana de Urología Dr. Nelson Adames dedicada a la atención integral, prevención, educación e investigación en el área urológica.',
        ];

        if (!empty($contactSettings?->phone)) {
            $orgSchema['telephone'] = $contactSettings->phone;
        }

        if (!empty($contactSettings?->email)) {
            $orgSchema['email'] = $contactSettings->email;
        }

        if (!empty($contactSettings?->address)) {
            $orgSchema['address'] = [
                '@type' => 'PostalAddress',
                'streetAddress' => $contactSettings->address,
            ];
        }

        $socialLinks = array_values(array_filter([
            $contactSettings?->isFacebookVisible() ? $contactSettings->facebook_url : null,
            $contactSettings?->isInstagramVisible() ? $contactSettings->instagram_url : null,
            $contactSettings?->isTwitterVisible() ? $contactSettings->twitter_url : null,
            $contactSettings?->isYoutubeVisible() ? $contactSettings->youtube_url : null,
            $contactSettings?->isTiktokVisible() ? $contactSettings->tiktok_url : null,
            $contactSettings?->isLinkedinVisible() ? $contactSettings->linkedin_url : null,
        ]));

        if (!empty($socialLinks)) {
            $orgSchema['sameAs'] = $socialLinks;
        }
    @endphp
    <script type="application/ld+json">
    {!! json_encode($orgSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
    @yield('schema')

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

    <main class="flex-grow w-full max-w-[900px] mx-auto p-5">
        @yield('content')
    </main>

    @include('partials.footer')

    <!-- Banner de Consentimiento de Cookies (Requerido por Google AdSense / Privacidad) -->
    <div x-data="{
            accepted: localStorage.getItem('fudounar_cookie_consent') !== null,
            accept() {
                localStorage.setItem('fudounar_cookie_consent', 'accepted');
                this.accepted = true;
            },
            reject() {
                localStorage.setItem('fudounar_cookie_consent', 'rejected');
                this.accepted = true;
            }
        }"
        x-show="!accepted"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-cloak
        class="fixed bottom-4 left-4 right-4 md:left-auto md:right-4 md:max-w-md bg-white border border-gray-200 shadow-2xl rounded-2xl p-5 z-50 text-sm">
        <div class="flex items-start gap-3">
            <div class="p-2 bg-blue-50 text-blue-600 rounded-xl flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h4 class="font-bold text-gray-900 mb-1">Aviso sobre Cookies</h4>
                <p class="text-gray-600 text-xs leading-relaxed">
                    Utilizamos cookies propias y de terceros (incluyendo Google) para analizar el tráfico y personalizar anuncios. Puedes consultar nuestra <a href="{{ route('privacy') }}" class="text-blue-600 underline hover:text-blue-800">Política de Privacidad</a>.
                </p>
                <div class="mt-3 flex items-center gap-2">
                    <button @click="accept()" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs rounded-lg transition cursor-pointer">
                        Aceptar
                    </button>
                    <button @click="reject()" class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-xs rounded-lg transition cursor-pointer">
                        Rechazar
                    </button>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
