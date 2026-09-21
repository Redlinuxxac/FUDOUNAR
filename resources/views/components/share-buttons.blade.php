@props([
    'url' => url()->current(),
    'title' => config('app.name', 'FUDOUNAR'),
    'description' => '',
])

@php
    $shareUrl = $url;
    $encodedUrl = urlencode($shareUrl);
    $encodedTitle = urlencode($title);
    $whatsappText = urlencode($title . ' ' . $shareUrl);
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-2']) }} x-data="{ copied: false }">
    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mr-1 flex items-center gap-1.5">
        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
        </svg>
        Compartir:
    </span>

    <!-- WhatsApp -->
    <a href="https://api.whatsapp.com/send?text={{ $whatsappText }}" 
       target="_blank" 
       rel="noopener noreferrer" 
       title="Compartir en WhatsApp"
       class="inline-flex items-center justify-center p-2 sm:px-3 sm:py-1.5 rounded-xl bg-[#25D366]/10 text-[#25D366] hover:bg-[#25D366] hover:text-white transition-all transform hover:-translate-y-0.5 text-xs font-bold shadow-xs">
        <svg class="w-4 h-4 sm:mr-1.5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2zm.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 012.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.42 0-2.82-.37-4.05-1.07l-.29-.17-3.12.82.83-3.04-.19-.3a8.216 8.216 0 01-1.26-4.47c0-4.54 3.7-8.24 8.24-8.24zm4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.02-1.24-.74-.66-1.24-1.48-1.39-1.73-.14-.25-.02-.39.11-.51.11-.11.25-.29.37-.43.13-.14.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.13-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.13.17 1.78 2.72 4.31 3.81.6.26 1.07.42 1.44.53.61.2 1.16.17 1.6.11.49-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.14-1.18-.06-.11-.23-.17-.48-.29z"/>
        </svg>
        <span class="hidden sm:inline">WhatsApp</span>
    </a>

    <!-- Facebook -->
    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedUrl }}" 
       target="_blank" 
       rel="noopener noreferrer" 
       title="Compartir en Facebook"
       class="inline-flex items-center justify-center p-2 sm:px-3 sm:py-1.5 rounded-xl bg-[#1877F2]/10 text-[#1877F2] hover:bg-[#1877F2] hover:text-white transition-all transform hover:-translate-y-0.5 text-xs font-bold shadow-xs">
        <svg class="w-4 h-4 sm:mr-1.5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
        </svg>
        <span class="hidden sm:inline">Facebook</span>
    </a>

    <!-- X (Twitter) -->
    <a href="https://twitter.com/intent/tweet?url={{ $encodedUrl }}&text={{ $encodedTitle }}" 
       target="_blank" 
       rel="noopener noreferrer" 
       title="Compartir en X"
       class="inline-flex items-center justify-center p-2 sm:px-3 sm:py-1.5 rounded-xl bg-black/10 text-gray-900 hover:bg-black hover:text-white transition-all transform hover:-translate-y-0.5 text-xs font-bold shadow-xs">
        <svg class="w-4 h-4 sm:mr-1.5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
        </svg>
        <span class="hidden sm:inline">X</span>
    </a>

    <!-- LinkedIn -->
    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $encodedUrl }}" 
       target="_blank" 
       rel="noopener noreferrer" 
       title="Compartir en LinkedIn"
       class="inline-flex items-center justify-center p-2 sm:px-3 sm:py-1.5 rounded-xl bg-[#0A66C2]/10 text-[#0A66C2] hover:bg-[#0A66C2] hover:text-white transition-all transform hover:-translate-y-0.5 text-xs font-bold shadow-xs">
        <svg class="w-4 h-4 sm:mr-1.5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
        </svg>
        <span class="hidden sm:inline">LinkedIn</span>
    </a>

    <!-- Copiar enlace -->
    <button type="button"
            @click="navigator.clipboard.writeText('{{ $shareUrl }}'); copied = true; setTimeout(() => copied = false, 2500)"
            title="Copiar enlace"
            class="inline-flex items-center justify-center p-2 sm:px-3 sm:py-1.5 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-800 hover:text-white transition-all transform hover:-translate-y-0.5 text-xs font-bold shadow-xs cursor-pointer">
        <span x-show="!copied" class="inline-flex items-center">
            <svg class="w-4 h-4 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
            </svg>
            <span class="hidden sm:inline">Copiar enlace</span>
        </span>
        <span x-show="copied" x-cloak class="inline-flex items-center text-emerald-600 font-bold">
            <svg class="w-4 h-4 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="hidden sm:inline">¡Copiado!</span>
        </span>
    </button>
</div>
