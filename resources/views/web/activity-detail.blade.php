@extends('layouts.web')

@section('title', 'FUDOUNAR - ' . $activity->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Navegación de migas de pan -->
    <nav class="flex text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="hover:text-blue-600">Inicio</a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg>
                    <a href="{{ route('activities') }}" class="hover:text-blue-600">Actividades</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg>
                    <span class="text-gray-900 font-medium">Detalle</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Encabezado de la Actividad -->
    <header class="space-y-4">
        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight">{{ $activity->title }}</h1>
        <div class="flex items-center space-x-4 text-gray-600">
            <div class="flex items-center">
                <img src="https://ui-avatars.com/api/?name=FUDOUNAR&background=0D8ABC&color=fff" class="w-10 h-10 rounded-full mr-2" alt="Autor">
                <span class="font-medium">Equipo FUDOUNAR</span>
            </div>
            <span>•</span>
            <time datetime="{{ $activity->started_at?->toDateString() ?? $activity->created_at->toDateString() }}">
                {{ $activity->started_at?->format('d \d\e M, Y') ?? $activity->created_at->format('d \d\e M, Y') }}
            </time>
        </div>
    </header>

    <!-- Imagen Principal -->
    <div class="rounded-3xl overflow-hidden shadow-2xl bg-gray-100 border border-gray-200">
        <img src="{{ $activity->image }}" class="w-full h-auto object-cover max-h-[600px]" alt="{{ $activity->title }}">
    </div>

    <!-- Contenido de la Actividad -->
    <article class="prose prose-lg max-w-none text-gray-700 leading-relaxed text-justify">
        <p class="text-xl font-medium text-blue-800 mb-6 italic">"Un compromiso firme con el desarrollo y bienestar de nuestras comunidades."</p>
        
        {!! nl2br(e($activity->description)) !!}
    </article>

    <!-- Footer de la Noticia -->
    <footer class="pt-8 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
        <div class="flex space-x-4">
            <span class="font-bold text-gray-900">Compartir:</span>
            <a href="#" class="text-blue-600 hover:text-blue-800 font-bold">Facebook</a>
            <a href="#" class="text-red-600 hover:text-red-800 font-bold">WhatsApp</a>
        </div>
        <a href="{{ route('activities') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 px-6 rounded-lg transition flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Volver al catálogo
        </a>
    </footer>
</div>
@endsection
