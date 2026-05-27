<header id="header" class="bg-[#f0f0f0] border-b border-gray-300" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
        <!-- Logo -->
        <div class="flex-shrink-0">
            <a href="{{ route('home') }}">
                <img src="{{ asset('img/logo.jpg') }}" alt="Logo Fudounar" class="w-[200px] md:w-[350px] h-auto transition-all">
            </a>
        </div>
        
        <!-- Desktop Nav -->
        <nav class="hidden lg:block">
            <ul class="flex space-x-6">
                <li><a href="{{ route('home') }}" class="font-bold text-black hover:text-red-600 transition {{ request()->routeIs('home') ? 'underline decoration-red-600 decoration-2' : '' }}">Inicio</a></li>
                <li><a href="{{ route('about') }}" class="font-bold text-black hover:text-red-600 transition {{ request()->routeIs('about') ? 'underline decoration-red-600 decoration-2' : '' }}">Quienes Somos</a></li>
                <li><a href="{{ route('activities') }}" class="font-bold text-black hover:text-red-600 transition {{ request()->routeIs('activities') ? 'underline decoration-red-600 decoration-2' : '' }}">Actividades</a></li>
                <li><a href="{{ route('blog') }}" class="font-bold text-black hover:text-red-600 transition {{ request()->routeIs('blog') ? 'underline decoration-red-600 decoration-2' : '' }}">Blog</a></li>
                <li><a href="{{ route('courses') }}" class="font-bold text-black hover:text-red-600 transition {{ request()->routeIs('courses') ? 'underline decoration-red-600 decoration-2' : '' }}">Cursos</a></li>
                <li><a href="{{ route('contact') }}" class="font-bold text-black hover:text-red-600 transition {{ request()->routeIs('contact') ? 'underline decoration-red-600 decoration-2' : '' }}">Contacto</a></li>
            </ul>
        </nav>

        <!-- Mobile Menu Button -->
        <div class="lg:hidden">
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-black p-2 focus:outline-none">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!mobileMenuOpen">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="mobileMenuOpen" x-cloak>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Nav -->
    <div class="lg:hidden bg-white border-t border-gray-200 shadow-xl" x-show="mobileMenuOpen" x-transition x-cloak>
        <nav class="flex flex-col p-4 space-y-4 font-bold">
            <a href="{{ route('home') }}" class="text-black hover:text-red-600 transition-colors {{ request()->routeIs('home') ? 'text-red-600' : '' }}">Inicio</a>
            <a href="{{ route('about') }}" class="text-black hover:text-red-600 transition-colors {{ request()->routeIs('about') ? 'text-red-600' : '' }}">Quienes Somos</a>
            <a href="{{ route('activities') }}" class="text-black hover:text-red-600 transition-colors {{ request()->routeIs('activities') ? 'text-red-600' : '' }}">Actividades</a>
            <a href="{{ route('blog') }}" class="text-black hover:text-red-600 transition-colors {{ request()->routeIs('blog') ? 'text-red-600' : '' }}">Blog</a>
            <a href="{{ route('courses') }}" class="text-black hover:text-red-600 transition-colors {{ request()->routeIs('courses') ? 'text-red-600' : '' }}">Cursos</a>
            <a href="{{ route('contact') }}" class="text-black hover:text-red-600 transition-colors {{ request()->routeIs('contact') ? 'text-red-600' : '' }}">Contacto</a>
        </nav>
    </div>

    <!-- Cinta de Banderas -->
    <div class="flex w-full h-2.5">
        <div class="w-1/2 bg-[url('/img/RD.png')] bg-repeat-x bg-[length:auto_100%]"></div>
        <div class="w-1/2 bg-[url('/img/aruba.png')] bg-repeat-x bg-[length:auto_100%]"></div>
    </div>
</header>
