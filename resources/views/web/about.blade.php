@extends('layouts.web')

@section('title', 'FUDOUNAR - Quiénes Somos')

@section('content')
<div class="space-y-12">
    <section id="quienes-somos" class="space-y-8">
        <h2 class="text-3xl font-bold border-b-2 border-red-600 inline-block pb-2">Quiénes Somos</h2>
        
        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div class="space-y-4">
                <h1 class="text-2xl font-bold text-blue-800">Nuestra misión es</h1>
                <p class="text-lg text-gray-700 leading-relaxed italic">"{{ $about->mission_text }}"</p>
            </div>
            <div>
                <img src="{{ $about->mission_image }}" alt="Misión" class="rounded-lg shadow-md w-full h-64 object-cover">
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div class="order-2 md:order-1">
                <img src="{{ $about->vision_image }}" alt="Visión" class="rounded-lg shadow-md w-full h-64 object-cover">
            </div>
            <div class="space-y-4 order-1 md:order-2">
                <h1 class="text-2xl font-bold text-blue-800">Nuestra visión es</h1>
                <p class="text-lg text-gray-700 leading-relaxed italic">"{{ $about->vision_text }}"</p>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div class="space-y-4">
                <h1 class="text-2xl font-bold text-blue-800">Nuestros valores son</h1>
                <p class="text-lg text-gray-700 leading-relaxed font-semibold">{{ $about->values_text }}</p>
            </div>
            <div>
                <img src="{{ $about->values_image }}" alt="Valores" class="rounded-lg shadow-md w-full h-64 object-cover">
            </div>
        </div>
    </section>

    @if($about->history_text)
    <section id="nuestra-history" class="bg-gray-50 p-8 rounded-xl border border-gray-200">
        <h2 class="text-3xl font-bold mb-4">Nuestra Historia</h2>
        <div class="text-gray-700 leading-relaxed text-justify">
            {!! nl2br(e($about->history_text)) !!}
        </div>
    </section>
    @endif

    <section class="grid md:grid-cols-2 gap-12 items-center">
        <div>
            <h1 class="text-3xl font-bold mb-4">Nuestro Equipo</h1>
            <div class="text-gray-700 leading-relaxed">
                {!! nl2br(e($about->team_text)) !!}
            </div>
        </div>
        <div>
            <img src="{{ $about->team_image }}" alt="Nuestro equipo" class="rounded-lg shadow-xl">
        </div>
    </section>

    <div class="grid md:grid-cols-2 gap-8">
        <section id="areas-de-impacto" class="bg-blue-50 p-6 rounded-lg border border-blue-100">
            <h2 class="text-2xl font-bold text-blue-900 mb-4">Áreas de Impacto</h2>
            <div class="space-y-2 text-blue-800 [&_ul]:list-disc [&_ul]:list-inside [&_li]:mt-1">
                {!! $about->impact_text !!}
            </div>
        </section>

        <section id="logros" class="bg-red-50 p-6 rounded-lg border border-red-100">
            <h2 class="text-2xl font-bold text-red-900 mb-4">Nuestros Logros</h2>
            <div class="space-y-2 text-red-800 [&_ul]:list-disc [&_ul]:list-inside [&_li]:mt-1">
                {!! $about->achievements_text !!}
            </div>
        </section>
    </div>

    <section class="text-center py-12 bg-gray-900 text-white rounded-3xl shadow-2xl">
        <h2 class="text-3xl font-bold mb-4 italic">¿Por qué Donar?</h2>
        <p class="max-w-2xl mx-auto mb-8 px-4 opacity-90">
            {{ $about->why_donate_text }}
        </p>
        <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-10 rounded-full transition transform hover:scale-105 shadow-lg">
            ¡Quiero Donar!
        </button>
    </section>
</div>
@endsection
