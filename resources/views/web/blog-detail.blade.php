@extends('layouts.web')

@section('title', 'FUDOUNAR - ' . $post->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Breadcrumb -->
    <nav class="flex text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="hover:text-blue-600">Inicio</a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg>
                    <a href="{{ route('blog') }}" class="hover:text-blue-600">Blog</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg>
                    <span class="text-gray-900 font-medium">Noticia</span>
                </div>
            </li>
        </ol>
    </nav>

    <header class="space-y-4">
        <div class="inline-block bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Noticia</div>
        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight">{{ $post->title }}</h1>
        <div class="flex items-center space-x-4 text-gray-600">
            <div class="flex items-center">
                <img src="https://ui-avatars.com/api/?name=Redacción+Fudounar&background=E11D48&color=fff" class="w-10 h-10 rounded-full mr-2" alt="Autor">
                <span class="font-medium">Redacción FUDOUNAR</span>
            </div>
            <span>•</span>
            <time datetime="{{ $post->published_at?->toDateString() ?? $post->created_at->toDateString() }}">
                {{ $post->published_at?->format('d \d\e M, Y') ?? $post->created_at->format('d \d\e M, Y') }}
            </time>
        </div>
    </header>

    @if($post->image)
        <div class="rounded-3xl overflow-hidden shadow-2xl">
            <img src="{{ $post->image }}" class="w-full h-auto object-cover max-h-[500px]" alt="{{ $post->title }}">
        </div>
    @endif

    <article class="prose prose-lg max-w-none text-gray-700 leading-relaxed text-justify">
        {!! $post->content !!}
    </article>

    <footer class="pt-8 border-t border-gray-100 flex justify-between items-center">
        <div class="flex space-x-4">
            <a href="#" class="text-gray-400 hover:text-blue-600 transition"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 3.656 10.995 9 11.83v-8.369h-3.037v-3.461h3.037v-2.639c0-3.006 1.791-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.246h3.328l-.532 3.461h-2.796v8.369c5.344-.835 9-5.84 9-11.83z"/></svg></a>
        </div>
        <a href="{{ route('blog') }}" class="text-red-600 font-bold hover:underline">Volver al Blog</a>
    </footer>
</div>
@endsection
