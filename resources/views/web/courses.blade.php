@extends('layouts.web')

@section('title', 'FUDOUNAR - Cursos Disponibles')

@section('content')
@php
    $courses = \App\Models\Course::open()->latest()->paginate(10);
@endphp
<div class="space-y-12 max-w-7xl mx-auto px-4">
    <div class="text-center">
        <h2 id="titulo-cursos" class="text-4xl font-bold text-gray-800 border-b-4 border-blue-600 inline-block pb-2">Nuestra Oferta Académica</h2>
        <p class="text-gray-600 mt-4 max-w-2xl mx-auto">Capacítate con nosotros. Programas diseñados para fortalecer tus habilidades y abrir nuevas oportunidades laborales.</p>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse ($courses as $course)
            <article class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 flex flex-col hover:border-blue-300 transition-all group h-full">
                <div class="relative h-48">
                    <img src="{{ $course->image }}" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $course->title }}">
                    <div class="absolute top-4 right-4 bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg uppercase">{{ $course->modality->label() }}</div>
                </div>
                <div class="p-6 flex-grow flex flex-col">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 leading-tight">{{ $course->title }}</h3>
                    <p class="text-gray-600 text-sm mb-6 text-justify flex-grow line-clamp-3">{{ strip_tags($course->description) }}</p>
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-xs text-gray-500">
                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Duración: {{ $course->duration }} horas
                        </div>
                    </div>
                    <a href="{{ route('courses.show', $course->slug) }}" class="w-full bg-blue-600 text-white text-center font-bold py-3 rounded-xl hover:bg-blue-700 transition shadow-md">Ver Detalle del Curso</a>
                </div>
            </article>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 italic">No hay cursos con inscripciones abiertas en este momento.</p>
            </div>
        @endforelse
    </div>

    <!-- Paginación -->
    <div class="mt-12 mb-8">
        {{ $courses->links() }}
    </div>

    <!-- Banner de Dudas -->
    <div class="bg-blue-900 rounded-3xl p-10 text-white flex flex-col md:flex-row items-center justify-between shadow-2xl relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-blue-800 rounded-full opacity-50"></div>
        <div class="relative z-10 space-y-4 md:w-2/3">
            <h3 class="text-3xl font-bold">¿Tienes dudas sobre nuestros cursos?</h3>
            <p class="opacity-90">Nuestro equipo de formación está listo para asesorarte y ayudarte a elegir el programa que mejor se adapte a tus necesidades.</p>
        </div>
        <div class="relative z-10 mt-6 md:mt-0">
            <a href="{{ route('contact') }}" class="inline-block bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-10 rounded-full transition shadow-xl transform hover:scale-105">
                Consultar ahora
            </a>
        </div>
    </div>
</div>
@endsection
