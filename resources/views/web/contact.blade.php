@extends('layouts.web')

@section('title', 'FUDOUNAR - Contacto')

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

    <!-- 3. Teléfono y Correo (Debajo - Uno al lado del otro) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="bg-blue-50 p-6 rounded-3xl border border-blue-100 text-center shadow-sm hover:shadow-md transition-shadow">
            <div class="bg-blue-600 text-white w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <h4 class="font-bold text-blue-900 text-lg">Email Directo</h4>
            <p class="text-sm text-blue-700 mt-1 font-medium">{{ $contact->email }}</p>
        </div>
        
        <div class="bg-red-50 p-6 rounded-3xl border border-red-100 text-center shadow-sm hover:shadow-md transition-shadow">
            <div class="bg-red-600 text-white w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            </div>
            <h4 class="font-bold text-red-900 text-lg">Llámanos</h4>
            <p class="text-sm text-red-700 mt-1 font-medium">{{ $contact->phone }}</p>
        </div>
    </div>
</div>
@endsection
