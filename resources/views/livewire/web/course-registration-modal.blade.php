<div class="space-y-4">
    <!-- Indicador de aforo y cupos -->
    <div class="border-b border-gray-100 pb-4">
        <div class="flex items-center justify-between text-sm">
            <span class="text-gray-500 font-medium">Aforo disponible:</span>
            <span class="font-bold {{ $availableSlots <= 5 && $availableSlots > 0 ? 'text-amber-600' : ($isFull ? 'text-red-600' : 'text-blue-600') }}">
                {{ $availableSlots }} / {{ $course->capacity }} Cupos
            </span>
        </div>
        
        <!-- Barra visual de capacidad -->
        @php
            $occupied = max(0, $course->capacity - $availableSlots);
            $percentage = $course->capacity > 0 ? min(100, round(($occupied / $course->capacity) * 100)) : 100;
        @endphp
        <div class="w-full bg-gray-200 rounded-full h-2 mt-2 overflow-hidden">
            <div 
                class="h-2 rounded-full transition-all duration-500 {{ $isFull ? 'bg-red-500' : ($percentage >= 80 ? 'bg-amber-500' : 'bg-blue-600') }}"
                style="width: {{ $percentage }}%"
            ></div>
        </div>

        @if ($availableSlots <= 5 && $availableSlots > 0 && $canAccept)
            <div class="mt-2 text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-2 text-center">
                🔥 ¡Últimos {{ $availableSlots }} cupos disponibles!
            </div>
        @endif
    </div>

    <!-- Botón de acción según estado -->
    @if ($canAccept)
        <button 
            type="button"
            wire:click="openModal" 
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl transition transform hover:scale-[1.02] shadow-lg cursor-pointer flex items-center justify-center space-x-2"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            <span>Inscribirme ahora</span>
        </button>
        <p class="text-[11px] text-center text-gray-500 font-medium">
            Inscripciones abiertas • Reserva presencial válida por <strong>{{ $course->reservation_days }} días</strong> tras validar
        </p>
    @elseif ($isFull && $course->status === \App\Enums\CourseStatus::OPEN)
        <button 
            type="button" 
            disabled 
            class="w-full bg-gray-300 text-gray-600 font-bold py-4 rounded-xl cursor-not-allowed shadow-none"
        >
            Cupos Agotados
        </button>
        <p class="text-xs text-center text-red-600 font-medium bg-red-50 border border-red-100 rounded-lg p-2.5">
            El aforo para este curso se encuentra completo. Si un cupo es liberado por vencimiento, se habilitará nuevamente la inscripción.
        </p>
    @else
        <button 
            type="button" 
            disabled 
            class="w-full bg-gray-200 text-gray-400 font-bold py-4 rounded-xl cursor-not-allowed shadow-none"
        >
            Inscripciones Cerradas
        </button>
        <p class="text-[11px] text-center text-gray-400 uppercase tracking-wider font-bold">
            Estado: {{ $course->status->label() }}
        </p>
    @endif

    <!-- Modal de Preinscripción -->
    <div 
        x-data="{ show: @entangle('isOpen') }" 
        x-show="show" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title" 
        role="dialog" 
        aria-modal="true"
    >
        <!-- Backdrop -->
        <div 
            x-show="show" 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"
            wire:click="closeModal"
        ></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div 
                x-show="show" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100"
            >
                @if ($submitted)
                    @if ($isVerified)
                        <!-- Estado Validado en Tiempo Real -->
                        <div class="p-8 text-center space-y-4">
                            <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto shadow-inner">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="text-xs uppercase font-extrabold tracking-wider text-green-700 bg-green-50 px-3 py-1 rounded-full inline-block border border-green-200">
                                ✓ Correo Validado con Éxito
                            </span>
                            <h3 class="text-xl font-bold text-gray-900">¡Tu Reserva ha sido Activada!</h3>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                Hemos confirmado tu correo. Tu cupo para <strong>{{ $course->title }}</strong> está reservado por <strong>{{ $course->reservation_days }} días</strong>.
                            </p>
                            @if ($verificationToken)
                                <div class="pt-2">
                                    <a 
                                        href="{{ route('courses.registration.verify', ['token' => $verificationToken]) }}" 
                                        class="w-full inline-flex items-center justify-center space-x-2 bg-green-600 hover:bg-green-700 text-white font-bold py-3.5 px-6 rounded-xl transition shadow-lg"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <span>Ver Mi Comprobante de Reserva</span>
                                    </a>
                                </div>
                            @endif
                            <button 
                                type="button" 
                                wire:click="closeModal" 
                                class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2.5 px-6 rounded-xl transition text-sm"
                            >
                                Cerrar ventana
                            </button>
                        </div>
                    @else
                        <!-- Estado de espera con sondeo en tiempo real -->
                        <div wire:poll.3s="checkVerificationStatus" class="p-8 text-center space-y-4">
                            <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto shadow-inner relative">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <span class="absolute -top-1 -right-1 flex h-4 w-4">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-4 w-4 bg-blue-500"></span>
                                </span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">¡Solicitud Registrada con Éxito!</h3>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                {{ $successMessage }}
                            </p>

                            <!-- Indicador en vivo de espera de validación -->
                            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900 text-left space-y-2">
                                <div class="flex items-center space-x-2 font-bold text-amber-800">
                                    <svg class="w-4 h-4 animate-spin text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span>Esperando confirmación en tu correo...</span>
                                </div>
                                <p class="text-[11px] text-amber-700 leading-normal">
                                    Revisa tu bandeja de entrada o spam y haz clic en <strong>«Validar mi participación»</strong>. Esta ventana se actualizará automáticamente en cuanto lo hagas.
                                </p>
                            </div>

                            <button 
                                type="button" 
                                wire:click="closeModal" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl transition"
                            >
                                Entendido, cerrar
                            </button>
                        </div>
                    @endif
                @else
                    <!-- Formulario de reserva -->
                    <div class="p-6 sm:p-8 space-y-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="text-xs uppercase font-extrabold tracking-wider text-blue-600 bg-blue-50 px-2.5 py-1 rounded-md">
                                    Reserva Presencial
                                </span>
                                <h3 class="text-xl font-bold text-gray-900 mt-2" id="modal-title">
                                    Inscripción: {{ $course->title }}
                                </h3>
                                <p class="text-xs text-gray-500 mt-1">
                                    Ingresa tus datos de contacto para iniciar tu proceso de inscripción.
                                </p>
                            </div>
                            <button 
                                type="button" 
                                wire:click="closeModal" 
                                class="text-gray-400 hover:text-gray-600 transition"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <form wire:submit="submit" class="space-y-4">
                            <div>
                                <label for="full_name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                    Nombre y Apellido <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="full_name" 
                                    wire:model="full_name" 
                                    placeholder="Ej: Juan Pérez"
                                    class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition"
                                >
                                @error('full_name') 
                                    <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p> 
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                    Correo Electrónico <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    wire:model="email" 
                                    placeholder="ejemplo@correo.com"
                                    class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition"
                                >
                                @error('email') 
                                    <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p> 
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                    Teléfono / WhatsApp <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="tel" 
                                    id="phone" 
                                    wire:model="phone" 
                                    placeholder="Ej: +58 412 1234567"
                                    class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition"
                                >
                                @error('phone') 
                                    <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p> 
                                @enderror
                            </div>

                            <div class="bg-gray-50 rounded-xl p-3.5 border border-gray-200 text-xs text-gray-600 space-y-1">
                                <p class="font-bold text-gray-700">Importante:</p>
                                <p>• Te enviaremos un correo para verificar tu dirección.</p>
                                <p>• Al verificar, tendrás <strong>{{ $course->reservation_days }} días continuos</strong> para formalizar en sede antes de que venza el cupo.</p>
                            </div>

                            <div class="pt-2 flex items-center justify-end space-x-3">
                                <button 
                                    type="button" 
                                    wire:click="closeModal" 
                                    class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition"
                                >
                                    Cancelar
                                </button>
                                <button 
                                    type="submit" 
                                    wire:loading.attr="disabled"
                                    class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold transition flex items-center space-x-2 disabled:opacity-50"
                                >
                                    <span wire:loading.remove>Confirmar Solicitud</span>
                                    <span wire:loading class="flex items-center space-x-2">
                                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span>Procesando...</span>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
