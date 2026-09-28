<?php

namespace App\Livewire\Web;

use App\Enums\CourseRegistrationStatus;
use App\Mail\CourseRegistrationVerificationMail;
use App\Models\Course;
use App\Models\CourseRegistration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class CourseRegistrationModal extends Component
{
    public Course $course;

    public bool $isOpen = false;

    public string $full_name = '';

    public string $email = '';

    public string $phone = '';

    public bool $submitted = false;

    public string $successMessage = '';

    public ?int $registrationId = null;

    public bool $isVerified = false;

    public ?string $verificationToken = null;

    public function mount(Course $course): void
    {
        $this->course = $course;
    }

    #[On('open-registration-modal')]
    public function openModal(): void
    {
        if (! $this->course->canAcceptRegistrations()) {
            return;
        }

        $this->reset(['full_name', 'email', 'phone', 'submitted', 'successMessage', 'registrationId', 'isVerified', 'verificationToken']);
        $this->resetValidation();
        $this->isOpen = true;
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        if ($this->submitted) {
            $this->reset(['full_name', 'email', 'phone', 'submitted', 'successMessage', 'registrationId', 'isVerified', 'verificationToken']);
            $this->resetValidation();
        }
    }

    public function checkVerificationStatus(): void
    {
        if (! $this->registrationId || $this->isVerified) {
            return;
        }

        $registration = CourseRegistration::find($this->registrationId);
        if ($registration && $registration->status === CourseRegistrationStatus::VERIFIED) {
            $this->isVerified = true;
        }
    }

    public function submit(): void
    {
        $this->validate([
            'full_name' => 'required|string|min:3|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|min:7|max:30',
        ], [
            'full_name.required' => 'El nombre y apellido es obligatorio.',
            'full_name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa una dirección de correo válida.',
            'phone.required' => 'El teléfono de contacto es obligatorio.',
        ]);

        $freshCourse = Course::findOrFail($this->course->id);

        if (! $freshCourse->canAcceptRegistrations()) {
            $this->addError('email', 'Lo sentimos, los cupos para este curso acaban de agotarse o las inscripciones no están abiertas.');

            return;
        }

        $cleanEmail = strtolower(trim($this->email));

        // Verificar si ya tiene una reserva activa o formalizada
        $existing = CourseRegistration::where('course_id', $freshCourse->id)
            ->where('email', $cleanEmail)
            ->where(function ($query) {
                $query->where('status', CourseRegistrationStatus::ENROLLED)
                    ->orWhere('status', CourseRegistrationStatus::PENDING)
                    ->orWhere(function ($q) {
                        $q->where('status', CourseRegistrationStatus::VERIFIED)
                            ->where('expires_at', '>', now());
                    });
            })
            ->first();

        if ($existing) {
            if ($existing->status === CourseRegistrationStatus::ENROLLED) {
                $this->addError('email', 'Ya te encuentras formalmente inscrito en este curso.');
            } elseif ($existing->status === CourseRegistrationStatus::VERIFIED) {
                $this->addError('email', 'Ya dispones de una reserva de cupo activa para este curso. Acude a la sede antes de su vencimiento.');
            } else {
                $this->addError('email', 'Ya existe una solicitud pendiente con este correo electrónico para este curso. Revisa tu bandeja de entrada o carpeta de spam.');
            }

            return;
        }

        $token = Str::random(64);

        try {
            $createdRegistration = DB::transaction(function () use ($freshCourse, $cleanEmail, $token) {
                $registration = CourseRegistration::create([
                    'course_id' => $freshCourse->id,
                    'full_name' => trim($this->full_name),
                    'email' => $cleanEmail,
                    'phone' => trim($this->phone),
                    'status' => CourseRegistrationStatus::PENDING,
                    'verification_token' => $token,
                ]);

                Mail::to($registration->email)->send(new CourseRegistrationVerificationMail($registration));

                return $registration;
            });

            $this->registrationId = $createdRegistration->id;
            $this->verificationToken = $token;
        } catch (\Throwable $e) {
            Log::error('Error al registrar o enviar correo de preinscripción de curso: '.$e->getMessage());
            $this->addError('email', 'No se pudo procesar la preinscripción o enviar el correo de validación. Por favor intenta más tarde.');

            return;
        }

        $this->submitted = true;
        $this->successMessage = "¡Preinscripción recibida! Hemos enviado un enlace de validación a {$this->email}. Dispones de 24 horas para hacer clic y activar tu reserva de cupo temporal.";
    }

    public function render()
    {
        return view('livewire.web.course-registration-modal', [
            'availableSlots' => $this->course->availableSlots(),
            'isFull' => $this->course->isFull(),
            'canAccept' => $this->course->canAcceptRegistrations(),
        ]);
    }
}
