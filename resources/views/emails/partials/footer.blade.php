@php
    $contact = $contact ?? \App\Models\ContactSetting::first();
@endphp
<div class="footer" style="background-color: #f9fafb; border-top: 1px solid #e5e7eb; padding: 24px; font-size: 12px; color: #6b7280; text-align: center; line-height: 1.6;">
    @if($contact?->address)
        <p style="margin: 0 0 4px;"><strong>Sede:</strong> {{ $contact->address }}</p>
    @endif
    @if($contact?->phone)
        <p style="margin: 0 0 4px;"><strong>Atención:</strong> Lunes a Viernes de 8:00 AM a 4:00 PM | Tel: {{ $contact->phone }}</p>
    @endif
    @if($contact?->email)
        <p style="margin: 0 0 4px;"><strong>Correo:</strong> <a href="mailto:{{ $contact->email }}" style="color: #2563eb; text-decoration: none;">{{ $contact->email }}</a></p>
    @endif
    <p style="margin: 10px 0 0; color: #9ca3af; font-size: 11px;">
        © {{ date('Y') }} FUDOUNAR. Todos los derechos reservados.<br>
        Si no solicitaste este correo, puedes ignorarlo de manera segura.
    </p>
</div>
