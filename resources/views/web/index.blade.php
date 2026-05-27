@extends('layouts.web')

@section('title', 'FUDOUNAR - Inicio')

@section('top_content')
<div x-data="{ 
    active: 0, 
    loop() {
        setInterval(() => { this.active = (this.active + 1) % 5 }, 5000)
    }
}" 
x-init="loop()"
class="relative w-full h-[300px] md:h-[500px] overflow-hidden bg-black">
    
    <!-- Slides -->
    <div class="relative w-full h-full">
        <template x-for="(i, index) in [1,2,3,4,5]" :key="index">
            <div x-show="active === index" 
                 x-transition:enter="transition duration-1000" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 class="absolute inset-0" x-cloak>
                <img :src="'{{ asset('slider/slide') }}' + i + '.jpg'" class="w-full h-full object-cover opacity-60">
                <div class="absolute inset-0 flex flex-col items-center justify-center text-center text-white p-6">
                    <h2 class="text-2xl md:text-4xl lg:text-6xl font-bold mb-2 md:mb-4" x-text="['Uniendo Culturas', 'Educación para Todos', 'Voluntariado con Corazón', 'Salud Infantil', 'Capacitación Continua'][index]"></h2>
                    <p class="text-sm md:text-xl lg:text-2xl max-w-2xl px-4" x-text="['República Dominicana y Aruba trabajando juntas.', 'Programas de alfabetización digital transforman vidas.', 'Sé parte del cambio. Únete hoy mismo.', 'Jornadas médicas para los más pequeños.', 'Cursos técnicos para el crecimiento profesional.'][index]"></p>
                    <div class="mt-4 md:mt-8 flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                        <a href="{{ route('about') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 md:py-3 px-6 md:px-8 rounded-full transition text-sm md:text-base">Saber más</a>
                        <a href="{{ route('contact') }}" class="bg-white hover:bg-gray-100 text-gray-900 font-bold py-2 md:py-3 px-6 md:px-8 rounded-full transition text-sm md:text-base">Contáctanos</a>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Nav Buttons -->
    <button @click="active = (active - 1 + 5) % 5" class="absolute left-2 md:left-4 top-1/2 -translate-y-1/2 z-50 bg-white/20 hover:bg-white/40 text-white p-2 md:p-3 rounded-full backdrop-blur-sm transition">
        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
    </button>
    <button @click="active = (active + 1) % 5" class="absolute right-2 md:right-4 top-1/2 -translate-y-1/2 z-50 bg-white/20 hover:bg-white/40 text-white p-2 md:p-3 rounded-full backdrop-blur-sm transition">
        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
    </button>
</div>
@endsection

@section('content')
<div class="space-y-12 max-w-7xl mx-auto px-4">
    <!-- Seccion Proximas Actividad -->
    @php
        $latestActivity = \App\Models\Activity::active()->latest()->first();
    @endphp
    @if($latestActivity)
    <div>
        <h2 class="text-2xl font-bold text-center mb-6 text-gray-800 uppercase tracking-wide">Última Actividad</h2>
        <div class="flex flex-col md:flex-row border-2 border-gray-100 rounded-2xl shadow-sm overflow-hidden h-auto md:h-[250px] group hover:border-red-300 transition-all bg-white">
            <div class="md:w-1/3 overflow-hidden bg-gray-50 flex items-center justify-center">
                <img src="{{ $latestActivity->image }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500" alt="{{ $latestActivity->title }}">
            </div>
            <div class="p-8 flex flex-col justify-between flex-grow">
                <div>
                    <div class="flex items-center text-xs font-bold text-red-600 uppercase mb-2">
                        <span class="bg-red-100 px-2 py-1 rounded mr-2">Destacado</span>
                        Equipo FUDOUNAR
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 leading-tight">{{ $latestActivity->title }}</h3>
                    <p class="text-gray-600 text-sm mt-2 line-clamp-3">{{ $latestActivity->description }}</p>
                </div>
                <a href="{{ route('activities.show', $latestActivity->slug) }}" class="inline-flex items-center text-red-600 font-bold hover:text-red-800 transition">
                    Leer más actividad
                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Seccion Actividades Realizadas con Paginacion 6x6 -->
    <div x-data="{ page: 1, perPage: 6 }">
        @php
            $realizedActivities = \App\Models\Activity::active()->latest()->paginate(6, ['*'], 'realized_page');
        @endphp
        <h2 id="actividades-realizadas" class="text-2xl font-bold text-center mb-8 text-gray-800 uppercase tracking-wide">Actividades Realizadas</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse ($realizedActivities as $activity)
                <article class="bg-white border rounded-xl shadow-sm overflow-hidden group hover:border-blue-300 transition-all flex flex-col h-full">
                    <div class="h-48 overflow-hidden bg-gray-100 flex items-center justify-center">
                        <img src="{{ $activity->image }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <div class="p-5 flex flex-col flex-grow">
                        <h3 class="font-bold text-gray-900 mb-2 leading-tight">{{ $activity->title }}</h3>
                        <p class="text-gray-600 text-xs mb-4 flex-grow line-clamp-2">{{ $activity->description }}</p>
                        <a href="{{ route('activities.show', $activity->slug) }}" class="text-blue-600 font-bold text-sm mt-auto">Leer más &rarr;</a>
                    </div>
                </article>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 italic">No hay actividades históricas registradas.</p>
                </div>
            @endforelse
        </div>

        @if ($realizedActivities->hasPages())
            <div class="mt-10">
                {{ $realizedActivities->links() }}
            </div>
        @endif
        
        <div class="flex flex-col items-center mt-10">
            <a href="{{ route('activities') }}" class="bg-gray-900 text-white px-8 py-3 rounded-full font-bold hover:bg-black transition shadow-lg">Ver catálogo completo</a>
        </div>
    </div>

    <!-- Seccion Blog -->
    @php
        $latestPosts = \App\Models\Post::published()->latest()->take(3)->get();
    @endphp
    @if($latestPosts->isNotEmpty())
    <section class="mt-16">
        <h2 class="text-3xl font-bold text-center mb-10 text-gray-800">Últimas Noticias</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach ($latestPosts as $post)
            <article class="bg-white rounded-xl shadow-md overflow-hidden group hover:border-blue-300 transition-all flex flex-col h-full border">
                <div class="h-48 overflow-hidden bg-gray-100 flex items-center justify-center">
                    @if($post->image)
                        <img src="{{ $post->image }}" class="w-full h-full object-cover group-hover:scale-105 transition-duration-500">
                    @else
                        <flux:icon name="newspaper" class="w-12 h-12 text-gray-300" />
                    @endif
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="font-bold text-gray-900 mb-2 line-clamp-2">{{ $post->title }}</h3>
                    <p class="text-gray-600 text-sm flex-grow line-clamp-2">{{ strip_tags($post->content) }}</p>
                    <a href="{{ route('blog.show', $post->slug) }}" class="text-red-600 font-bold mt-4 hover:underline">Leer más &rarr;</a>
                </div>
            </article>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Seccion Cursos -->
    @php
        $latestCourses = \App\Models\Course::open()->latest()->take(3)->get();
    @endphp
    @if($latestCourses->isNotEmpty())
    <section class="mt-16 bg-blue-50 -mx-4 px-4 py-16 rounded-[3rem]">
        <h2 class="text-3xl font-bold text-center mb-10 text-blue-900">Cursos Destacados</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach ($latestCourses as $course)
            <article class="bg-white rounded-3xl shadow-xl overflow-hidden group hover:ring-2 hover:ring-blue-300 transition-all flex flex-col h-full">
                <div class="h-40 overflow-hidden bg-gray-100">
                    <img src="{{ $course->image }}" class="w-full h-full object-cover group-hover:scale-105 transition-duration-500">
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="font-bold text-gray-900 mb-2 line-clamp-2">{{ $course->title }}</h3>
                    <a href="{{ route('courses.show', $course->slug) }}" class="bg-blue-600 text-white text-center py-2 rounded-xl font-bold hover:bg-blue-700 transition mt-auto">Ver Curso</a>
                </div>
            </article>
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection
