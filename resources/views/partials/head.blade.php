<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

<link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
<script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
<style>
    /* 1. Simplificación: Ocultar botones no deseados */
    trix-toolbar [data-trix-button-group="file-tools"],
    trix-toolbar [data-trix-action="link"],
    trix-toolbar [data-trix-action="quote"],
    trix-toolbar [data-trix-action="code"] { 
        display: none !important; 
    }

    /* 2. Adaptación Visual Modo Claro */
    trix-toolbar {
        border-bottom: 1px solid #e5e7eb; /* neutral-200 */
        background-color: #f9fafb; /* gray-50 */
        padding: 10px;
        border-radius: 12px 12px 0 0;
    }
    trix-toolbar .trix-button-group {
        border-color: #e5e7eb;
        background-color: white;
        margin-bottom: 0;
        margin-right: 10px;
    }
    trix-editor.trix-content {
        border-color: #e5e7eb;
        border-top: none;
        border-radius: 0 0 12px 12px;
        padding: 1rem;
        min-height: 150px;
    }

    /* 3. Adaptación Visual Modo Oscuro */
    .dark trix-toolbar {
        border-bottom-color: #404040; /* neutral-700 */
        background-color: #262626; /* neutral-800 */
    }
    .dark trix-toolbar .trix-button-group {
        border-color: #525252; /* neutral-600 */
        background-color: transparent;
    }
    .dark trix-toolbar .trix-button {
        filter: brightness(0) invert(1);
        opacity: 0.6;
    }
    .dark trix-toolbar .trix-button:hover,
    .dark trix-toolbar .trix-button--active {
        background-color: #525252 !important;
        opacity: 1;
    }
    .dark trix-editor.trix-content {
        border-color: #404040;
        background-color: #171717; /* neutral-900 */
        color: white;
    }
    .dark trix-toolbar .trix-button::before {
        color: white !important;
    }
</style>
