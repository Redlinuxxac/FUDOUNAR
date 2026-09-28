@extends('layouts.web')

@section('title', 'Comprobante de Reserva - FUDOUNAR')

@section('content')
<div class="max-w-3xl mx-auto space-y-8 py-6">
    <!-- Breadcrumb -->
    <nav class="flex text-sm text-gray-500 print:hidden" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ route('home') }}" class="hover:text-blue-600">Inicio</a></li>
            <li>
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg>
                    <a href="{{ route('courses') }}" class="hover:text-blue-600">Cursos</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg>
                    <span class="text-gray-900 font-medium">Comprobante de Reserva</span>
                </div>
            </li>
        </ol>
    </nav>

    @if ($alert)
        <div class="p-4 rounded-2xl print:hidden {{ $alert['type'] === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : ($alert['type'] === 'warning' ? 'bg-amber-50 border border-amber-200 text-amber-800' : 'bg-red-50 border border-red-200 text-red-800') }}">
            <div class="flex items-center space-x-3">
                @if ($alert['type'] === 'success')
                    <svg class="w-6 h-6 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                @else
                    <svg class="w-6 h-6 shrink-0 {{ $alert['type'] === 'warning' ? 'text-amber-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                @endif
                <p class="text-sm font-medium">{{ $alert['message'] }}</p>
            </div>
        </div>
    @endif

    <!-- Comprobante Card -->
    <div id="comprobante" class="bg-white border-2 border-gray-200 rounded-3xl shadow-xl overflow-hidden print:border-none print:shadow-none print:p-0">
        <!-- Header del comprobante -->
        <div class="bg-gradient-to-r from-blue-900 to-blue-700 text-white p-6 sm:p-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="bg-white/20 text-white text-xs font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
                        FUDOUNAR Presencial
                    </span>
                    <span class="text-blue-200 text-xs font-semibold">Reserva Digital</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black mt-2">Comprobante de Reserva de Cupo</h1>
                <p class="text-blue-100 text-sm mt-1">Presenta este documento al formalizar tu inscripción en sede.</p>
            </div>
            <div class="bg-white/10 backdrop-blur-md px-4 py-3 rounded-2xl border border-white/20 text-center sm:text-right w-full sm:w-auto">
                <span class="text-[11px] text-blue-200 uppercase font-bold tracking-wider block">Código de Reserva</span>
                <span class="text-xl sm:text-2xl font-mono font-black tracking-widest text-white">
                    {{ $registration->formattedCode() }}
                </span>
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-8">
            <!-- Estado y Plazo de Vencimiento -->
            @if ($registration->status === \App\Enums\CourseRegistrationStatus::VERIFIED && $registration->expires_at && $registration->expires_at->isFuture())
                <div class="bg-amber-50 border-2 border-amber-300 rounded-2xl p-5 space-y-2">
                    <div class="flex items-center space-x-2 text-amber-900 font-bold">
                        <svg class="w-6 h-6 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-base sm:text-lg">Fecha Límite para Formalizar en Sede:</span>
                    </div>
                    <p class="text-lg sm:text-xl font-extrabold text-amber-950 pl-8">
                        {{ $registration->expires_at->translatedFormat('l, d \d\e F \d\e Y \a \l\a\s h:i A') }}
                    </p>
                    <p class="text-sm font-semibold text-amber-800 pl-8">
                        ⏳ Te quedan <strong>{{ now()->diffForHumans($registration->expires_at, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}</strong> para acudir con tus recaudos. Transcurrido este plazo, el cupo se liberará automáticamente.
                    </p>
                </div>
            @elseif ($registration->status === \App\Enums\CourseRegistrationStatus::ENROLLED)
                <div class="bg-green-50 border-2 border-green-300 rounded-2xl p-5 flex items-center space-x-3 text-green-900">
                    <svg class="w-8 h-8 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <h3 class="text-lg font-bold">¡Inscripción Formalizada Exitosamente!</h3>
                        <p class="text-sm text-green-800">
                            Tu cupo ha sido asegurado formalmente en sede el {{ $registration->enrolled_at?->translatedFormat('d/m/Y h:i A') ?? 'recientemente' }}.
                        </p>
                    </div>
                </div>
            @elseif ($registration->isExpired())
                <div class="bg-red-50 border-2 border-red-300 rounded-2xl p-5 flex items-center space-x-3 text-red-900">
                    <svg class="w-8 h-8 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <h3 class="text-lg font-bold">Reserva Vencida (Cupo Liberado)</h3>
                        <p class="text-sm text-red-700">
                            El plazo para formalizar presencialmente esta reserva venció. Si deseas participar, verifica si hay nuevos cupos disponibles en el curso.
                        </p>
                    </div>
                </div>
            @endif

            <!-- Datos del Estudiante y Curso -->
            <div class="grid sm:grid-cols-2 gap-6 pt-2">
                <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">Datos del Participante</h3>
                    <div>
                        <span class="text-xs text-gray-400 block">Nombre completo:</span>
                        <span class="text-base font-bold text-gray-900">{{ $registration->full_name }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 block">Correo electrónico:</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $registration->email }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 block">Teléfono / WhatsApp:</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $registration->phone }}</span>
                    </div>
                </div>

                <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">Detalles del Programa</h3>
                    <div>
                        <span class="text-xs text-gray-400 block">Curso:</span>
                        <span class="text-base font-bold text-blue-900">{{ $course->title }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs text-gray-400 block">Modalidad:</span>
                            <span class="text-sm font-semibold text-gray-800">{{ $course->modality->label() }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-400 block">Duración:</span>
                            <span class="text-sm font-semibold text-gray-800">{{ $course->duration }} Horas</span>
                        </div>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 block">Fecha de inicio:</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $course->started_at?->format('d/m/Y') ?? 'Próximamente' }}</span>
                    </div>
                </div>
            </div>

            <!-- Recaudos a consignar -->
            <div class="bg-blue-50/60 border border-blue-200 rounded-2xl p-6 space-y-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-blue-950 flex items-center space-x-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Recaudos requeridos en la sede</span>
                </h3>
                <ul class="space-y-2 text-sm text-blue-900">
                    <li class="flex items-start space-x-2">
                        <span class="text-blue-600 font-bold">•</span>
                        <span>Original y fotocopia del <strong>Documento de Identidad</strong> (Cédula / DNI).</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <span class="text-blue-600 font-bold">•</span>
                        <span>Código de Reserva: <strong>{{ $registration->formattedCode() }}</strong> (puedes mostrar este comprobante en tu teléfono o impreso).</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <span class="text-blue-600 font-bold">•</span>
                        <span>Comprobante de aporte o arancel administrativo (en caso de aplicar).</span>
                    </li>
                </ul>
            </div>

            <!-- Información de la Sede y Atención -->
            <div class="border-t border-gray-200 pt-6 grid sm:grid-cols-2 gap-4 text-xs text-gray-600">
                <div>
                    <span class="font-bold text-gray-800 block mb-1">📍 Dirección de la Sede:</span>
                    <p>{{ $contact?->address ?? 'Sede Principal FUDOUNAR' }}</p>
                </div>
                <div>
                    <span class="font-bold text-gray-800 block mb-1">⏰ Horario de Atención Presencial:</span>
                    <p>Lunes a Viernes de 8:00 AM a 4:00 PM</p>
                    @if ($contact?->phone)
                        <p class="mt-1">Contacto: {{ $contact->phone }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Botones de Acción (ocultos al imprimir) -->
        <div class="bg-gray-50 border-t border-gray-100 p-6 flex flex-col sm:flex-row justify-between items-center gap-4 print:hidden">
            <a 
                href="{{ route('courses.show', $course->slug) }}" 
                class="text-sm text-gray-600 hover:text-gray-900 font-bold flex items-center space-x-1"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Volver al Curso</span>
            </a>

            <div class="flex items-center space-x-3 w-full sm:w-auto">
                @if ($contact?->maps_url)
                    <a 
                        href="{{ $contact->maps_url }}" 
                        target="_blank" 
                        class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-white transition flex items-center justify-center space-x-2"
                    >
                        <span>Cómo llegar</span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </a>
                @endif

                <button 
                    type="button" 
                    onclick="window.print()" 
                    class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold shadow transition flex items-center justify-center space-x-2 cursor-pointer w-full sm:w-auto"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Imprimir Comprobante</span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    header, footer, nav, .print\:hidden {
        display: none !important;
    }
    body {
        background-color: white !important;
        color: black !important;
        padding: 0 !important;
    }
    main {
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
}
</style>
@endsection
