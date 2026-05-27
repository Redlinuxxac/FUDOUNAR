@extends('layouts.web')

@section('title', 'FUDOUNAR - Todas nuestras Actividades')

@section('content')
@php
    $activities = \App\Models\Activity::active()->latest()->paginate(10);
@endphp
<div class="space-y-12 max-w-7xl mx-auto px-4">
    <div class="text-center">
        <h2 id="titulo-pagina" class="text-4xl font-bold text-gray-800 border-b-4 border-red-600 inline-block pb-2">Catálogo de Actividades</h2>
        <p class="text-gray-600 mt-4 max-w-2xl mx-auto">Explora el historial completo de nuestras iniciativas y el impacto que hemos logrado juntos.</p>
    </div>

    <!-- Cuadrícula de Actividades -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse ($activities as $activity)
            <article class="bg-white border rounded-xl shadow-sm overflow-hidden group hover:border-blue-300 transition-all flex flex-col h-full">
                <div class="h-48 overflow-hidden bg-gray-100 flex items-center justify-center">
                    <img src="{{ $activity->image }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500" alt="{{ $activity->title }}">
                </div>
                <div class="p-5 flex flex-col flex-grow">
                    <span class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-2">Publicado: {{ $activity->created_at->diffForHumans() }}</span>
                    <h3 class="text-xl font-bold mb-3 text-gray-900 leading-tight">{{ $activity->title }}</h3>
                    <p class="text-gray-600 text-sm mb-6 flex-grow line-clamp-3">{{ $activity->description }}</p>
                    <a href="{{ route('activities.show', $activity->slug) }}" class="text-blue-600 font-bold flex items-center mt-auto">
                        Leer más
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </article>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 italic">No hay actividades publicadas en este momento.</p>
            </div>
        @endforelse
    </div>

    <!-- Paginación -->
    <div class="mt-12 mb-8">
        {{ $activities->links() }}
    </div>

    <!-- Banner Informativo -->
    <div class="bg-gray-900 rounded-3xl p-10 text-white text-center shadow-2xl relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
        <h3 class="text-2xl font-bold mb-4 relative z-10">¿Tienes fotos o noticias de alguna actividad?</h3>
        <p class="mb-8 opacity-80 relative z-10">Ayúdanos a documentar nuestra labor. Envíanos el material a nuestro equipo de comunicaciones.</p>
        <a href="{{ route('contact') }}" class="relative z-10 inline-block bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-10 rounded-full transition transform hover:scale-105 shadow-xl">
            Contactar ahora
        </a>
    </div>
</div>
@endsection
