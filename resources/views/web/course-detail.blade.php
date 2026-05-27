@extends('layouts.web')

@section('title', 'FUDOUNAR - ' . $course->title)

@section('content')
<div class="max-w-5xl mx-auto space-y-12">
    <!-- Breadcrumb -->
    <nav class="flex text-sm text-gray-500" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="hover:text-blue-600">Inicio</a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg>
                    <a href="{{ route('courses') }}" class="hover:text-blue-600">Cursos</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg>
                    <span class="text-gray-900 font-medium">Detalle del Curso</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid lg:grid-cols-3 gap-12">
        <!-- Columna Izquierda: Información del Curso -->
        <div class="lg:col-span-2 space-y-8">
            <header class="space-y-4">
                <div class="flex items-center space-x-2">
                    <span class="bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full uppercase">{{ $course->modality->label() }}</span>
                    <span class="text-gray-400 text-sm">Estado: {{ $course->status->label() }}</span>
                </div>
                <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900">{{ $course->title }}</h1>
            </header>

            <div class="rounded-3xl overflow-hidden shadow-xl">
                <img src="{{ $course->image }}" class="w-full h-auto object-cover" alt="{{ $course->title }}">
            </div>

            <section class="space-y-6">
                <h2 class="text-2xl font-bold text-gray-800">Descripción del Curso</h2>
                <div class="prose prose-lg text-gray-700 max-w-none text-justify">
                    {!! $course->description !!}
                </div>
            </section>
        </div>

        <!-- Columna Derecha: Sidebar de Inscripción -->
        <div class="space-y-6">
            <div class="bg-white p-8 rounded-3xl shadow-2xl border border-gray-100 sticky top-8">
                <div class="space-y-6">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <span class="text-gray-500">Duración:</span>
                        <span class="font-bold text-gray-900">{{ $course->duration }} Horas</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <span class="text-gray-500">Fecha de inicio:</span>
                        <span class="font-bold text-gray-900">{{ $course->started_at?->format('d/m/Y') ?? 'Próximamente' }}</span>
                    </div>
                    
                    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl transition transform hover:scale-[1.02] shadow-lg">
                        Inscribirme ahora
                    </button>
                    
                    <p class="text-[10px] text-center text-gray-400 uppercase tracking-widest font-bold">Inscripciones abiertas</p>
                </div>
            </div>

            <div class="bg-red-50 p-6 rounded-3xl border border-red-100">
                <h4 class="font-bold text-red-900 mb-2">¿Necesitas ayuda?</h4>
                <p class="text-sm text-red-700 mb-4">Si tienes dudas sobre los requisitos o el proceso de inscripción, contáctanos.</p>
                <a href="{{ route('contact') }}" class="text-red-600 font-bold hover:underline">Hablar con un asesor &rarr;</a>
            </div>
        </div>
    </div>
</div>
@endsection
