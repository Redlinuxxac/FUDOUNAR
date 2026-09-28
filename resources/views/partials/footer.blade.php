@php
    $footerContact = \App\Models\ContactSetting::first();
@endphp
<footer class="bg-[#f0f0f0] text-center p-6 mt-12 border-t border-gray-300">
    @if($footerContact && $footerContact->hasSocialLinks())
        <div class="flex justify-center items-center gap-4 mb-3">
            @if($footerContact->isFacebookVisible())
                <a href="{{ $footerContact->facebook_url }}" target="_blank" rel="noopener noreferrer" class="text-gray-600 hover:text-[#1877F2] transition" title="Facebook">
                    <span class="sr-only">Facebook</span>
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </a>
            @endif
            @if($footerContact->isInstagramVisible())
                <a href="{{ $footerContact->instagram_url }}" target="_blank" rel="noopener noreferrer" class="text-gray-600 hover:text-[#E4405F] transition" title="Instagram">
                    <span class="sr-only">Instagram</span>
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </a>
            @endif
            @if($footerContact->isTwitterVisible())
                <a href="{{ $footerContact->twitter_url }}" target="_blank" rel="noopener noreferrer" class="text-gray-600 hover:text-black transition" title="X (Twitter)">
                    <span class="sr-only">X</span>
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </a>
            @endif
            @if($footerContact->isYoutubeVisible())
                <a href="{{ $footerContact->youtube_url }}" target="_blank" rel="noopener noreferrer" class="text-gray-600 hover:text-[#FF0000] transition" title="YouTube">
                    <span class="sr-only">YouTube</span>
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                    </svg>
                </a>
            @endif
            @if($footerContact->isTiktokVisible())
                <a href="{{ $footerContact->tiktok_url }}" target="_blank" rel="noopener noreferrer" class="text-gray-600 hover:text-black transition" title="TikTok">
                    <span class="sr-only">TikTok</span>
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-1.01v8.42c0 1.63-.44 3.28-1.34 4.62-.89 1.34-2.18 2.37-3.69 2.93-1.51.56-3.18.59-4.71.09-1.53-.5-2.89-1.49-3.83-2.79-1.25-1.72-1.74-3.94-1.33-6.04.41-2.1 1.74-3.9 3.65-4.89 1.25-.65 2.69-.94 4.09-.85v4.14c-.67-.09-1.36.03-1.95.34-.6.31-1.05.85-1.25 1.5-.2.65-.13 1.37.19 1.96.32.59.88 1.01 1.53 1.17.65.16 1.35.05 1.92-.3.57-.35.95-.94 1.04-1.61.08-.62.07-1.25.07-1.87V.02h-.44z"/>
                    </svg>
                </a>
            @endif
            @if($footerContact->isWhatsappVisible())
                <a href="{{ $footerContact->whatsapp_link }}" target="_blank" rel="noopener noreferrer" class="text-gray-600 hover:text-[#25D366] transition" title="WhatsApp">
                    <span class="sr-only">WhatsApp</span>
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2zm.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 012.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.42 0-2.82-.37-4.05-1.07l-.29-.17-3.12.82.83-3.04-.19-.3a8.216 8.216 0 01-1.26-4.47c0-4.54 3.7-8.24 8.24-8.24zm4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.02-1.24-.74-.66-1.24-1.48-1.39-1.73-.14-.25-.02-.39.11-.51.11-.11.25-.29.37-.43.13-.14.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.13-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.13.17 1.78 2.72 4.31 3.81.6.26 1.07.42 1.44.53.61.2 1.16.17 1.6.11.49-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.14-1.18-.06-.11-.23-.17-.48-.29z"/>
                    </svg>
                </a>
            @endif
            @if($footerContact->isLinkedinVisible())
                <a href="{{ $footerContact->linkedin_url }}" target="_blank" rel="noopener noreferrer" class="text-gray-600 hover:text-[#0A66C2] transition" title="LinkedIn">
                    <span class="sr-only">LinkedIn</span>
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                    </svg>
                </a>
            @endif
        </div>
    @endif
    
    <div class="flex flex-wrap justify-center items-center gap-x-6 gap-y-2 mb-4 text-xs text-gray-600">
        <a href="{{ route('home') }}" class="hover:text-red-600 transition">Inicio</a>
        <a href="{{ route('about') }}" class="hover:text-red-600 transition">Quiénes Somos</a>
        <a href="{{ route('courses') }}" class="hover:text-red-600 transition">Cursos</a>
        <a href="{{ route('blog') }}" class="hover:text-red-600 transition">Blog</a>
        <a href="{{ route('contact') }}" class="hover:text-red-600 transition">Contacto</a>
        <span class="text-gray-300 hidden sm:inline">|</span>
        <a href="{{ route('privacy') }}" class="hover:text-red-600 transition font-medium">Política de Privacidad</a>
        <a href="{{ route('terms') }}" class="hover:text-red-600 transition font-medium">Términos y Condiciones</a>
    </div>

    <p class="text-gray-700 text-xs sm:text-sm">&copy; FUDOUNAR {{ date('Y') }} Creating by REDDATASRD</p>
</footer>
