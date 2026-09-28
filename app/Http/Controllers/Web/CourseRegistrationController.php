<?php

namespace App\Http\Controllers\Web;

use App\Enums\CourseRegistrationStatus;
use App\Http\Controllers\Controller;
use App\Models\ContactSetting;
use App\Models\CourseRegistration;
use Illuminate\Contracts\View\View;

class CourseRegistrationController extends Controller
{
    /**
     * Valida el token del correo y muestra el comprobante digital de la reserva.
     */
    public function verify(string $token): View
    {
        $registration = CourseRegistration::with('course')
            ->where('verification_token', $token)
            ->firstOrFail();

        $course = $registration->course;
        $alert = null;

        if ($registration->status === CourseRegistrationStatus::PENDING) {
            // Verificar aforo al momento de validar
            if ($course->isFull()) {
                $alert = [
                    'type' => 'error',
                    'message' => 'Lamentablemente, el aforo para este curso se ha completado antes de que pudieras validar tu enlace. Tu cupo no pudo ser reservado.',
                ];
            } else {
                $reservationDays = $course->reservation_days ?? 5;
                $registration->update([
                    'status' => CourseRegistrationStatus::VERIFIED,
                    'verified_at' => now(),
                    'expires_at' => now()->addDays($reservationDays),
                ]);

                $alert = [
                    'type' => 'success',
                    'message' => "¡Correo validado correctamente! Tu cupo presencial ha quedado reservado por un plazo de {$reservationDays} días.",
                ];
            }
        } elseif ($registration->status === CourseRegistrationStatus::VERIFIED) {
            if ($registration->expires_at && $registration->expires_at->isPast()) {
                $registration->update([
                    'status' => CourseRegistrationStatus::EXPIRED,
                ]);

                $alert = [
                    'type' => 'warning',
                    'message' => 'El plazo límite para formalizar presencialmente esta reserva ha vencido y el cupo ha sido liberado.',
                ];
            }
        }

        $contact = ContactSetting::first();

        return view('web.course-registration-status', [
            'registration' => $registration,
            'course' => $course,
            'contact' => $contact,
            'alert' => $alert,
        ]);
    }
}
