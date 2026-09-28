@extends('layouts.web')

@section('title', 'FUDOUNAR - ' . $course->title)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($course->description), 160))
@section('canonical_url', route('courses.show', $course->slug))
@section('og_type', 'article')
@section('og_title', $course->title)
@section('og_description', \Illuminate\Support\Str::limit(strip_tags($course->description), 160))
@section('og_url', route('courses.show', $course->slug))
@section('og_image', $course->image_url)

@section('schema')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Course',
    'name' => $course->title,
    'description' => \Illuminate\Support\Str::limit(strip_tags($course->description), 200),
    'provider' => [
        '@type' => 'Organization',
        'name' => 'FUDOUNAR',
        'sameAs' => url('/'),
    ],
    'hasCourseInstance' => [
        '@type' => 'CourseInstance',
        'courseMode' => $course->modality?->label() ?? 'Presencial',
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Inicio',
            'item' => route('home'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => 'Cursos',
            'item' => route('courses'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $course->title,
            'item' => route('courses.show', $course->slug),
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endsection

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
                <img src="{{ $course->image_url }}" class="w-full h-auto object-cover" alt="{{ $course->title }}">
            </div>

            <section class="space-y-6">
                <h2 class="text-2xl font-bold text-gray-800">Descripción del Curso</h2>
                <div class="prose prose-lg text-gray-700 max-w-none text-justify">
                    {!! $course->description !!}
                </div>
            </section>

            <footer class="pt-8 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                <x-share-buttons :title="$course->title" :url="route('courses.show', $course->slug)" :image="$course->image_url" />
                <a href="{{ route('courses') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 px-6 rounded-lg transition flex items-center shrink-0">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Volver a Cursos
                </a>
            </footer>
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
                    
                    <livewire:web.course-registration-modal :course="$course" />

                    <div class="pt-4 border-t border-gray-100">
                        <x-share-buttons :title="$course->title" :url="route('courses.show', $course->slug)" :image="$course->image_url" />
                    </div>
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
