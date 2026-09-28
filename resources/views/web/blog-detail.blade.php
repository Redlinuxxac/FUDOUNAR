@extends('layouts.web')

@section('title', 'FUDOUNAR - ' . $post->title)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($post->content), 160))
@section('canonical_url', route('blog.show', $post->slug))
@section('og_type', 'article')
@section('og_title', $post->title)
@section('og_description', \Illuminate\Support\Str::limit(strip_tags($post->content), 160))
@section('og_url', route('blog.show', $post->slug))
@section('og_image', $post->image_url)

@section('schema')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BlogPosting',
    'headline' => $post->title,
    'image' => $post->image_url ?? asset('img/LogoMejorado.png'),
    'datePublished' => ($post->published_at ?? $post->created_at)->toIso8601String(),
    'dateModified' => $post->updated_at->toIso8601String(),
    'author' => [
        '@type' => 'Organization',
        'name' => 'FUDOUNAR',
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => 'FUDOUNAR',
        'logo' => [
            '@type' => 'ImageObject',
            'url' => asset('img/LogoMejorado.png'),
        ],
    ],
    'description' => \Illuminate\Support\Str::limit(strip_tags($post->content), 160),
    'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id' => route('blog.show', $post->slug),
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
            'name' => 'Blog',
            'item' => route('blog'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $post->title,
            'item' => route('blog.show', $post->slug),
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endsection

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
            <img src="{{ $post->image_url }}" class="w-full h-auto object-cover max-h-[500px]" alt="{{ $post->title }}">
        </div>
    @endif

    <article class="prose prose-lg max-w-none text-gray-700 leading-relaxed text-justify">
        {!! $post->content !!}
    </article>

    <footer class="pt-8 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
        <x-share-buttons :title="$post->title" :url="route('blog.show', $post->slug)" :image="$post->image_url" />
        <a href="{{ route('blog') }}" class="bg-gray-100 hover:bg-gray-200 text-red-600 font-bold py-2 px-6 rounded-lg transition flex items-center shrink-0">
            &larr; Volver al Blog
        </a>
    </footer>
</div>
@endsection
