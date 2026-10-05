@extends('layouts.web')

@section('title', 'FUDOUNAR - Contáctanos | Ubicación, Teléfono y Correo')
@section('meta_description', 'Comunícate con la Fundación Dominicanos Unidos en Aruba (FUDOUNAR). Encuentra nuestra dirección física, teléfonos, mapa de ubicación y formulario de contacto.')
@section('canonical_url', route('contact'))

@section('content')
<div class="max-w-4xl mx-auto space-y-12">
    <!-- Encabezado -->
    <div class="text-center">
        <h2 class="text-4xl font-bold text-gray-800 border-b-4 border-blue-600 inline-block pb-2 uppercase tracking-wide">Contáctanos</h2>
        <p class="text-gray-600 mt-4 max-w-2xl mx-auto">Estamos aquí para escucharte. Envíanos un mensaje o visítanos.</p>
    </div>

    <!-- 1. El Mapa (Primero - Ancho completo del contenedor) -->
    <section class="bg-white p-6 rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <h3 class="text-2xl font-bold mb-4 text-gray-800 flex items-center">
            <flux:icon name="map-pin" class="mr-2 text-red-600" />
            Nuestra Ubicación
        </h3>
        <div class="rounded-2xl overflow-hidden border border-gray-200 shadow-inner h-80 relative bg-gray-50">
            @if($contact->google_maps_url)
                <iframe 
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade"
                    src="{{ $contact->google_maps_url }}">
                </iframe>
            @else
                <div class="flex items-center justify-center h-full text-gray-400 italic">
                    Mapa no configurado
                </div>
            @endif
        </div>
        @if($contact->address)
            <div class="mt-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                <p class="text-gray-700 text-sm">
                    <span class="font-bold">Dirección:</span> {{ $contact->address }}
                </p>
            </div>
        @endif
    </section>

    <!-- 2. El Formulario (Segundo - Ancho completo del contenedor) -->
    <section class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100">
        <h3 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
            <flux:icon name="envelope" class="mr-2 text-blue-600" />
            Envíanos un mensaje
        </h3>
        <form method="POST" action="#" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                    <input type="text" name="name" id="name" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-gray-50" placeholder="Tu nombre..." required>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                    <input type="email" name="email" id="email" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-gray-50" placeholder="tu@email.com" required>
                </div>
            </div>
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Mensaje</label>
                <input id="message" type="hidden" name="message" required>
                <trix-editor input="message" class="trix-content w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition min-h-[200px] bg-gray-50" placeholder="¿En qué podemos ayudarte?"></trix-editor>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl transition transform hover:scale-[1.01] shadow-lg flex items-center justify-center uppercase tracking-wider">
                Enviar Mensaje ahora
            </button>
        </form>
    </section>

    <!-- 3. Teléfono, Correo y WhatsApp (Debajo) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 {{ $contact?->isWhatsappVisible() ? 'lg:grid-cols-3' : '' }} gap-6">
        <div class="bg-blue-50 p-6 rounded-3xl border border-blue-100 text-center shadow-sm hover:shadow-md transition-shadow">
            <div class="bg-blue-600 text-white w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <h4 class="font-bold text-blue-900 text-lg">Email Directo</h4>
            <p class="text-sm text-blue-700 mt-1 font-medium">{{ $contact?->email ?? 'No disponible' }}</p>
        </div>
        
        <div class="bg-red-50 p-6 rounded-3xl border border-red-100 text-center shadow-sm hover:shadow-md transition-shadow">
            <div class="bg-red-600 text-white w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            </div>
            <h4 class="font-bold text-red-900 text-lg">Llámanos</h4>
            <p class="text-sm text-red-700 mt-1 font-medium">{{ $contact?->phone ?? 'No disponible' }}</p>
        </div>

        @if($contact?->isWhatsappVisible())
            <a href="{{ $contact->whatsapp_link }}" target="_blank" rel="noopener noreferrer" class="bg-emerald-50 p-6 rounded-3xl border border-emerald-100 text-center shadow-sm hover:shadow-md hover:border-emerald-300 transition block group">
                <div class="bg-emerald-600 group-hover:scale-110 transition-transform text-white w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2zm.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 012.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.42 0-2.82-.37-4.05-1.07l-.29-.17-3.12.82.83-3.04-.19-.3a8.216 8.216 0 01-1.26-4.47c0-4.54 3.7-8.24 8.24-8.24zm4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.02-1.24-.74-.66-1.24-1.48-1.39-1.73-.14-.25-.02-.39.11-.51.11-.11.25-.29.37-.43.13-.14.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.13-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.13.17 1.78 2.72 4.31 3.81.6.26 1.07.42 1.44.53.61.2 1.16.17 1.6.11.49-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.14-1.18-.06-.11-.23-.17-.48-.29z"/>
                    </svg>
                </div>
                <h4 class="font-bold text-emerald-900 text-lg">WhatsApp</h4>
                <p class="text-sm text-emerald-700 mt-1 font-medium">Escríbenos directamente &rarr;</p>
            </a>
        @endif
    </div>

    <!-- 4. Redes Sociales -->
    @if($contact?->hasSocialLinks())
        <section class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100 text-center">
            <h3 class="text-2xl font-bold mb-2 text-gray-800 flex items-center justify-center">
                <flux:icon name="share" class="mr-2 text-blue-600" />
                Síguenos en nuestras Redes Sociales
            </h3>
            <p class="text-gray-600 text-sm mb-6 max-w-xl mx-auto">
                Conéctate con nuestra comunidad en redes sociales para enterarte de cursos, actividades y noticias.
            </p>

            <div class="flex flex-wrap justify-center items-center gap-4">
                @if($contact->isFacebookVisible())
                    <a href="{{ $contact->facebook_url }}" target="_blank" rel="noopener noreferrer" class="flex items-center space-x-2 px-5 py-3 rounded-2xl bg-[#1877F2]/10 text-[#1877F2] hover:bg-[#1877F2] hover:text-white transition transform hover:-translate-y-0.5 font-bold text-sm shadow-sm">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        <span>Facebook</span>
                    </a>
                @endif

                @if($contact->isInstagramVisible())
                    <a href="{{ $contact->instagram_url }}" target="_blank" rel="noopener noreferrer" class="flex items-center space-x-2 px-5 py-3 rounded-2xl bg-[#E4405F]/10 text-[#E4405F] hover:bg-gradient-to-tr hover:from-[#F58529] hover:via-[#DD2A7B] hover:to-[#8134AF] hover:text-white transition transform hover:-translate-y-0.5 font-bold text-sm shadow-sm">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                        <span>Instagram</span>
                    </a>
                @endif

                @if($contact->isTwitterVisible())
                    <a href="{{ $contact->twitter_url }}" target="_blank" rel="noopener noreferrer" class="flex items-center space-x-2 px-5 py-3 rounded-2xl bg-black/10 text-gray-900 hover:bg-black hover:text-white transition transform hover:-translate-y-0.5 font-bold text-sm shadow-sm">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                        <span>X (Twitter)</span>
                    </a>
                @endif

                @if($contact->isYoutubeVisible())
                    <a href="{{ $contact->youtube_url }}" target="_blank" rel="noopener noreferrer" class="flex items-center space-x-2 px-5 py-3 rounded-2xl bg-[#FF0000]/10 text-[#FF0000] hover:bg-[#FF0000] hover:text-white transition transform hover:-translate-y-0.5 font-bold text-sm shadow-sm">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                        <span>YouTube</span>
                    </a>
                @endif

                @if($contact->isTiktokVisible())
                    <a href="{{ $contact->tiktok_url }}" target="_blank" rel="noopener noreferrer" class="flex items-center space-x-2 px-5 py-3 rounded-2xl bg-black/10 text-gray-900 hover:bg-black hover:text-white transition transform hover:-translate-y-0.5 font-bold text-sm shadow-sm">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-1.01v8.42c0 1.63-.44 3.28-1.34 4.62-.89 1.34-2.18 2.37-3.69 2.93-1.51.56-3.18.59-4.71.09-1.53-.5-2.89-1.49-3.83-2.79-1.25-1.72-1.74-3.94-1.33-6.04.41-2.1 1.74-3.9 3.65-4.89 1.25-.65 2.69-.94 4.09-.85v4.14c-.67-.09-1.36.03-1.95.34-.6.31-1.05.85-1.25 1.5-.2.65-.13 1.37.19 1.96.32.59.88 1.01 1.53 1.17.65.16 1.35.05 1.92-.3.57-.35.95-.94 1.04-1.61.08-.62.07-1.25.07-1.87V.02h-.44z"/>
                        </svg>
                        <span>TikTok</span>
                    </a>
                @endif

                @if($contact->isWhatsappVisible())
                    <a href="{{ $contact->whatsapp_link }}" target="_blank" rel="noopener noreferrer" class="flex items-center space-x-2 px-5 py-3 rounded-2xl bg-[#25D366]/10 text-[#25D366] hover:bg-[#25D366] hover:text-white transition transform hover:-translate-y-0.5 font-bold text-sm shadow-sm">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2zm.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 012.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.42 0-2.82-.37-4.05-1.07l-.29-.17-3.12.82.83-3.04-.19-.3a8.216 8.216 0 01-1.26-4.47c0-4.54 3.7-8.24 8.24-8.24zm4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.02-1.24-.74-.66-1.24-1.48-1.39-1.73-.14-.25-.02-.39.11-.51.11-.11.25-.29.37-.43.13-.14.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.13-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.13.17 1.78 2.72 4.31 3.81.6.26 1.07.42 1.44.53.61.2 1.16.17 1.6.11.49-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.14-1.18-.06-.11-.23-.17-.48-.29z"/>
                        </svg>
                        <span>WhatsApp</span>
                    </a>
                @endif

                @if($contact->isLinkedinVisible())
                    <a href="{{ $contact->linkedin_url }}" target="_blank" rel="noopener noreferrer" class="flex items-center space-x-2 px-5 py-3 rounded-2xl bg-[#0A66C2]/10 text-[#0A66C2] hover:bg-[#0A66C2] hover:text-white transition transform hover:-translate-y-0.5 font-bold text-sm shadow-sm">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                        </svg>
                        <span>LinkedIn</span>
                    </a>
                @endif
            </div>
        </section>
    @endif
</div>
@endsection
