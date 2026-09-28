<?php

use App\Enums\CourseRegistrationStatus;
use App\Mail\CourseRegistrationVerificationMail;
use App\Models\Course;
use App\Models\CourseRegistration;
use Illuminate\Support\Facades\Mail;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $selectedCourseId = '';
    public string $selectedStatus = '';

    // Modal de notas y detalles
    public bool $showDetailModal = false;
    public ?CourseRegistration $selectedRegistration = null;
    public string $adminNotes = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedCourseId(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedStatus(): void
    {
        $this->resetPage();
    }

    public function formalize(int $id): void
    {
        $registration = CourseRegistration::findOrFail($id);
        $registration->update([
            'status' => CourseRegistrationStatus::ENROLLED,
            'enrolled_at' => now(),
        ]);

        Flux::toast('Inscripción formalizada exitosamente en sede.');
    }

    public function cancelRegistration(int $id): void
    {
        $registration = CourseRegistration::findOrFail($id);
        $registration->update([
            'status' => CourseRegistrationStatus::CANCELLED,
        ]);

        Flux::toast('La reserva ha sido cancelada.');
    }

    public function delete(int $id): void
    {
        $registration = CourseRegistration::findOrFail($id);
        $registration->delete();

        Flux::toast('Registro eliminado correctamente.');
    }

    public function resendVerification(int $id): void
    {
        $registration = CourseRegistration::with('course')->findOrFail($id);

        if ($registration->status !== CourseRegistrationStatus::PENDING) {
            Flux::toast('Solo se puede reenviar el correo a solicitudes pendientes de validación.', variant: 'warning');

            return;
        }

        try {
            Mail::to($registration->email)->send(new CourseRegistrationVerificationMail($registration));
            Flux::toast("Correo de validación reenviado a {$registration->email}.");
        } catch (\Throwable $e) {
            Flux::toast('No se pudo enviar el correo: '.$e->getMessage(), variant: 'danger');
        }
    }

    public function openDetail(int $id): void
    {
        $this->selectedRegistration = CourseRegistration::with('course')->findOrFail($id);
        $this->adminNotes = $this->selectedRegistration->admin_notes ?? '';
        $this->showDetailModal = true;
    }

    public function saveNotes(): void
    {
        if ($this->selectedRegistration) {
            $this->selectedRegistration->update([
                'admin_notes' => trim($this->adminNotes),
            ]);
            Flux::toast('Notas actualizadas.');
        }

        $this->showDetailModal = false;
    }

    public function with(): array
    {
        $query = CourseRegistration::query()->with('course');

        if ($this->selectedCourseId !== '') {
            $query->where('course_id', $this->selectedCourseId);
        }

        if ($this->selectedStatus !== '') {
            $query->where('status', $this->selectedStatus);
        }

        if ($this->search !== '') {
            $term = trim($this->search);
            $query->where(function ($q) use ($term) {
                $q->where('full_name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                    ->orWhere('id', 'like', "%{$term}%")
                    ->orWhere('verification_token', 'like', "%{$term}%");
            });
        }

        // Métricas rápidas
        $metricsBase = CourseRegistration::query();
        if ($this->selectedCourseId !== '') {
            $metricsBase->where('course_id', $this->selectedCourseId);
        }

        $totalRegistrations = (clone $metricsBase)->count();
        $verifiedActive = (clone $metricsBase)
            ->where('status', CourseRegistrationStatus::VERIFIED)
            ->where('expires_at', '>', now())
            ->count();
        $enrolled = (clone $metricsBase)
            ->where('status', CourseRegistrationStatus::ENROLLED)
            ->count();
        $expired = (clone $metricsBase)
            ->where('status', CourseRegistrationStatus::EXPIRED)
            ->count();
        $pending = (clone $metricsBase)
            ->where('status', CourseRegistrationStatus::PENDING)
            ->count();

        return [
            'registrations' => $query->latest()->paginate(15),
            'courses' => Course::orderBy('title')->get(),
            'totalRegistrations' => $totalRegistrations,
            'verifiedActive' => $verifiedActive,
            'enrolled' => $enrolled,
            'expired' => $expired,
            'pending' => $pending,
        ];
    }
}; ?>

<div class="p-6 space-y-6" wire:poll.4s>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <flux:heading size="xl" level="1">Inscripciones y Reservas</flux:heading>
            <flux:subheading>Control de cupos, validaciones y formalización presencial en sede.</flux:subheading>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                En vivo
            </span>
            <flux:button :href="route('admin.courses')" variant="ghost" icon="academic-cap" wire:navigate>
                Ver Cursos
            </flux:button>
        </div>
    </div>

    <!-- Métricas -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-neutral-800 p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm">
            <span class="text-xs font-semibold text-neutral-400 uppercase tracking-wider block">Total Solicitudes</span>
            <span class="text-2xl font-black text-neutral-800 dark:text-neutral-100">{{ $totalRegistrations }}</span>
        </div>
        <div class="bg-blue-50 dark:bg-blue-950/40 p-4 rounded-xl border border-blue-200 dark:border-blue-900 shadow-sm">
            <span class="text-xs font-semibold text-blue-700 dark:text-blue-300 uppercase tracking-wider block">Reservas Activas</span>
            <span class="text-2xl font-black text-blue-900 dark:text-blue-100">{{ $verifiedActive }}</span>
            <span class="text-[11px] text-blue-600 dark:text-blue-400 block mt-0.5">En plazo por formalizar</span>
        </div>
        <div class="bg-green-50 dark:bg-green-950/40 p-4 rounded-xl border border-green-200 dark:border-green-900 shadow-sm">
            <span class="text-xs font-semibold text-green-700 dark:text-green-300 uppercase tracking-wider block">Formalizados</span>
            <span class="text-2xl font-black text-green-900 dark:text-green-100">{{ $enrolled }}</span>
            <span class="text-[11px] text-green-600 dark:text-green-400 block mt-0.5">Asistieron a sede</span>
        </div>
        <div class="bg-amber-50 dark:bg-amber-950/40 p-4 rounded-xl border border-amber-200 dark:border-amber-900 shadow-sm">
            <span class="text-xs font-semibold text-amber-700 dark:text-amber-300 uppercase tracking-wider block">Por Validar</span>
            <span class="text-2xl font-black text-amber-900 dark:text-amber-100">{{ $pending }}</span>
            <span class="text-[11px] text-amber-600 dark:text-amber-400 block mt-0.5">Correo sin confirmar</span>
        </div>
        <div class="bg-neutral-100 dark:bg-neutral-800 p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm">
            <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider block">Vencidas</span>
            <span class="text-2xl font-black text-neutral-600 dark:text-neutral-300">{{ $expired }}</span>
            <span class="text-[11px] text-neutral-400 block mt-0.5">Cupo liberado</span>
        </div>
    </div>

    <!-- Filtros y Tabla -->
    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden">
        <div class="p-4 border-b border-neutral-200 dark:border-neutral-700 grid grid-cols-1 md:grid-cols-4 gap-4">
            <flux:input 
                wire:model.live.debounce.300ms="search" 
                placeholder="Buscar por alumno, correo, teléfono..." 
                icon="magnifying-glass" 
            />

            <flux:select wire:model.live="selectedCourseId">
                <option value="">Todos los cursos</option>
                @foreach ($courses as $c)
                    <option value="{{ $c->id }}">{{ $c->title }}</option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="selectedStatus">
                <option value="">Todos los estados</option>
                @foreach (CourseRegistrationStatus::cases() as $st)
                    <option value="{{ $st->value }}">{{ $st->label() }}</option>
                @endforeach
            </flux:select>

            @if ($search !== '' || $selectedCourseId !== '' || $selectedStatus !== '')
                <div class="flex items-center">
                    <flux:button size="sm" variant="ghost" wire:click="$set('search', ''); $set('selectedCourseId', ''); $set('selectedStatus', '');">
                        Limpiar filtros
                    </flux:button>
                </div>
            @endif
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Código</flux:table.column>
                <flux:table.column>Estudiante</flux:table.column>
                <flux:table.column>Curso</flux:table.column>
                <flux:table.column>Estado</flux:table.column>
                <flux:table.column>Plazo / Vigencia</flux:table.column>
                <flux:table.column>Acciones</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($registrations as $reg)
                    <flux:table.row :key="$reg->id">
                        <flux:table.cell class="font-mono text-xs font-bold text-neutral-600 dark:text-neutral-400">
                            {{ $reg->formattedCode() }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <div>
                                <span class="font-bold text-neutral-900 dark:text-neutral-100 block">{{ $reg->full_name }}</span>
                                <span class="text-xs text-neutral-500 block">{{ $reg->email }}</span>
                                <span class="text-xs text-neutral-400 block">{{ $reg->phone }}</span>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            <span class="font-medium text-xs text-neutral-800 dark:text-neutral-200 block">{{ $reg->course?->title }}</span>
                            <span class="text-[11px] text-neutral-400">{{ $reg->course?->duration }} hrs • {{ $reg->course?->modality->label() }}</span>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge :color="$reg->status->color()" size="sm">
                                {{ $reg->status->label() }}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell>
                            @if ($reg->status === CourseRegistrationStatus::VERIFIED && $reg->expires_at)
                                @if ($reg->expires_at->isFuture())
                                    <div class="text-xs">
                                        <span class="font-bold text-amber-600 dark:text-amber-400 block">
                                            ⏳ Vence en {{ now()->diffForHumans($reg->expires_at, ['parts' => 1, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}
                                        </span>
                                        <span class="text-[11px] text-neutral-400">{{ $reg->expires_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                @else
                                    <span class="text-xs font-semibold text-red-500 block">Plazo expirado</span>
                                @endif
                            @elseif ($reg->status === CourseRegistrationStatus::ENROLLED)
                                <div class="text-xs text-neutral-500">
                                    <span class="text-green-600 font-semibold block">Formalizado</span>
                                    <span class="text-[11px]">{{ $reg->enrolled_at?->format('d/m/Y H:i') }}</span>
                                </div>
                            @elseif ($reg->status === CourseRegistrationStatus::PENDING)
                                <span class="text-xs text-neutral-400">Solicitado: {{ $reg->created_at->format('d/m/Y') }}</span>
                            @else
                                <span class="text-xs text-neutral-400">—</span>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell>
                            <div class="flex items-center space-x-1.5">
                                @if ($reg->status === CourseRegistrationStatus::PENDING)
                                    <flux:button 
                                        wire:click="resendVerification({{ $reg->id }})" 
                                        size="xs" 
                                        variant="ghost" 
                                        icon="envelope"
                                        title="Reenviar correo de validación" 
                                    />
                                @endif

                                @if ($reg->status === CourseRegistrationStatus::VERIFIED || $reg->status === CourseRegistrationStatus::PENDING)
                                    <flux:button 
                                        wire:click="formalize({{ $reg->id }})" 
                                        wire:confirm="¿Confirmas que el estudiante acudió a la sede presencial y presentó sus recaudos?"
                                        size="xs" 
                                        variant="primary" 
                                        icon="check-circle"
                                        title="Formalizar en Sede"
                                    >
                                        Formalizar
                                    </flux:button>
                                @endif

                                <flux:button 
                                    wire:click="openDetail({{ $reg->id }})" 
                                    size="xs" 
                                    variant="ghost" 
                                    icon="document-text"
                                    title="Ver Detalles y Notas" 
                                />

                                @if ($reg->status !== CourseRegistrationStatus::CANCELLED && $reg->status !== CourseRegistrationStatus::ENROLLED)
                                    <flux:button 
                                        wire:click="cancelRegistration({{ $reg->id }})" 
                                        wire:confirm="¿Deseas cancelar esta reserva?"
                                        size="xs" 
                                        variant="ghost" 
                                        color="red" 
                                        icon="x-circle"
                                        title="Cancelar Reserva" 
                                    />
                                @endif

                                <flux:button 
                                    wire:click="delete({{ $reg->id }})" 
                                    wire:confirm="¿Eliminar definitivamente este registro?"
                                    size="xs" 
                                    variant="ghost" 
                                    color="red" 
                                    icon="trash"
                                    title="Eliminar" 
                                />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center py-12">
                            <flux:text>No se encontraron registros de inscripción.</flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="p-4 border-t border-neutral-200 dark:border-neutral-700">
            {{ $registrations->links() }}
        </div>
    </div>

    <!-- Modal Detalle y Notas de Administración -->
    @if ($showDetailModal && $selectedRegistration)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-neutral-900/60 backdrop-blur-sm" wire:click="$set('showDetailModal', false)"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-neutral-800 p-6 shadow-2xl border border-neutral-200 dark:border-neutral-700 space-y-5">
                    <div class="flex justify-between items-center border-b border-neutral-100 dark:border-neutral-700 pb-3">
                        <div>
                            <span class="text-xs uppercase font-extrabold text-blue-600 dark:text-blue-400">Detalles de Inscripción</span>
                            <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">
                                {{ $selectedRegistration->formattedCode() }}
                            </h3>
                        </div>
                        <button type="button" wire:click="$set('showDetailModal', false)" class="text-neutral-400 hover:text-neutral-600">
                            <flux:icon name="x-mark" class="size-5" />
                        </button>
                    </div>

                    <div class="space-y-3 text-sm">
                        <div class="grid grid-cols-2 gap-3 bg-neutral-50 dark:bg-neutral-900 p-3.5 rounded-xl">
                            <div>
                                <span class="text-xs text-neutral-400 block">Estudiante:</span>
                                <span class="font-bold text-neutral-800 dark:text-neutral-200">{{ $selectedRegistration->full_name }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-neutral-400 block">Teléfono:</span>
                                <span class="font-medium text-neutral-800 dark:text-neutral-200">{{ $selectedRegistration->phone }}</span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-xs text-neutral-400 block">Correo Electrónico:</span>
                                <span class="font-medium text-neutral-800 dark:text-neutral-200">{{ $selectedRegistration->email }}</span>
                            </div>
                        </div>

                        <div class="bg-neutral-50 dark:bg-neutral-900 p-3.5 rounded-xl space-y-1">
                            <span class="text-xs text-neutral-400 block">Curso:</span>
                            <span class="font-bold text-blue-600 dark:text-blue-400">{{ $selectedRegistration->course?->title }}</span>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="text-xs text-neutral-400">Estado actual:</span>
                                <flux:badge :color="$selectedRegistration->status->color()" size="sm">
                                    {{ $selectedRegistration->status->label() }}
                                </flux:badge>
                            </div>
                        </div>

                        <div>
                            <flux:textarea 
                                wire:model="adminNotes" 
                                label="Notas y Observaciones de la Sede" 
                                placeholder="Anotar recaudos consignados, comprobantes o comentarios..." 
                                rows="3"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t border-neutral-100 dark:border-neutral-700">
                        <flux:button variant="ghost" wire:click="$set('showDetailModal', false)">Cerrar</flux:button>
                        <flux:button variant="primary" wire:click="saveNotes">Guardar Notas</flux:button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
