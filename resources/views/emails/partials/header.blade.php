<div class="header">
    <div class="logo-container" style="text-align: center; margin-bottom: 12px;">
        <a href="{{ config('app.url') }}" target="_blank" style="text-decoration: none; display: inline-block;">
            @if(isset($message) && method_exists($message, 'embed') && file_exists(public_path('img/LogoMejorado.png')))
                <img src="{{ $message->embed(public_path('img/LogoMejorado.png')) }}" width="170" alt="Logo FUDOUNAR" class="logo" style="width: 170px; max-width: 170px; height: auto; display: block; margin: 0 auto; border: 0;">
            @else
                <img src="{{ asset('img/LogoMejorado.png') }}" width="170" alt="Logo FUDOUNAR" class="logo" style="width: 170px; max-width: 170px; height: auto; display: block; margin: 0 auto; border: 0;">
            @endif
        </a>
    </div>
    <h1 style="margin: 8px 0 0; font-size: 20px; font-weight: 800; color: #1e3a8a; letter-spacing: 0.5px;">FUDOUNAR</h1>
    <p class="subtitle" style="margin: 4px 0 0; font-size: 13px; color: #6b7280; font-weight: 500;">Fundación Dominicanos Unidos en Aruba</p>
    @if(isset($headerBadge) && $headerBadge)
        <div class="badge-header" style="display: inline-block; margin-top: 10px; background-color: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; padding: 4px 14px; border-radius: 9999px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
            {{ $headerBadge }}
        </div>
    @endif
</div>
