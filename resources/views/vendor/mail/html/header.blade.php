@props(['url'])
<tr>
<td class="header" style="text-align: center; padding: 25px 0;">
<a href="{{ $url }}" style="display: inline-block;">
@if (file_exists(public_path('img/LogoMejorado.png')))
<img src="{{ asset('img/LogoMejorado.png') }}" class="logo" alt="{{ config('app.name', 'FUDOUNAR') }}" style="max-height: 80px; width: auto; border: 0;">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
