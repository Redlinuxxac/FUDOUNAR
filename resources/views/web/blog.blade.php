@extends('layouts.web')

@section('title', 'FUDOUNAR - Blog')

@section('content')
@php
    $posts = \App\Models\Post::published()->latest()->paginate(9);
@endphp
<div class="space-y-12 max-w-7xl mx-auto px-4">
    <div class="text-center">
        <h2 id="titulo-blog" class="text-4xl font-bold text-gray-800 border-b-4 border-red-600 inline-block pb-2">Nuestro Blog</h2>
        <p class="text-gray-600 mt-4 max-w-2xl mx-auto">Noticias, historias de impacto y actualizaciones sobre nuestra labor en la comunidad.</p>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse ($posts as $post)
            <article class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 hover:border-blue-300 transition-all group flex flex-col h-full">
                <div class="relative overflow-hidden h-48 bg-gray-100 flex items-center justify-center">
                    @if($post->image)
                        <img src="{{ $post->image }}" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $post->title }}">
                    @else
                        <flux:icon name="newspaper" class="w-12 h-12 text-gray-300" />
                    @endif
                </div>
                <div class="p-6 flex-grow flex flex-col">
                    <div class="flex items-center text-xs text-blue-600 font-bold uppercase mb-2">Noticia</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2">{{ $post->title }}</h3>
                    <p class="text-gray-600 text-sm mb-4 flex-grow text-justify line-clamp-3">{{ strip_tags($post->content) }}</p>
                    <div class="pt-4 border-t border-gray-50 flex justify-between items-center text-xs text-gray-400">
                        <span>{{ $post->published_at?->format('d M, Y') ?? $post->created_at->format('d M, Y') }}</span>
                        <a href="{{ route('blog.show', $post->slug) }}" class="text-red-600 font-bold hover:underline">Leer más &rarr;</a>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 italic">No hay publicaciones en el blog en este momento.</p>
            </div>
        @endforelse
    </div>

    <!-- Paginación -->
    <div class="mt-12 mb-8">
        {{ $posts->links() }}
    </div>
</div>
@endsection
