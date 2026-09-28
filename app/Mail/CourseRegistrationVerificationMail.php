<?php

namespace App\Mail;

use App\Models\ContactSetting;
use App\Models\CourseRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseRegistrationVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public CourseRegistration $registration
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $courseTitle = $this->registration->course?->title ?? 'Curso Presencial';

        return new Envelope(
            subject: "[FUDOUNAR] Activa tu reserva para el curso: {$courseTitle}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.course-registration-verification',
            with: [
                'registration' => $this->registration,
                'course' => $this->registration->course,
                'reservationDays' => $this->registration->course?->reservation_days ?? 5,
                'verificationUrl' => route('courses.registration.verify', ['token' => $this->registration->verification_token]),
                'contact' => ContactSetting::first(),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
