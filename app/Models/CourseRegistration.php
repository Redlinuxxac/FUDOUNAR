<?php

namespace App\Models;

use App\Enums\CourseRegistrationStatus;
use Database\Factories\CourseRegistrationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseRegistration extends Model
{
    /** @use HasFactory<CourseRegistrationFactory> */
    use HasFactory;

    protected $fillable = [
        'course_id',
        'full_name',
        'email',
        'phone',
        'status',
        'verification_token',
        'verified_at',
        'expires_at',
        'enrolled_at',
        'admin_notes',
    ];

    protected $casts = [
        'status' => CourseRegistrationStatus::class,
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
        'enrolled_at' => 'datetime',
    ];

    /**
     * Relación con el curso.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Código formateado de la reserva (ej. #FUD-12-A3B9CD).
     */
    public function formattedCode(): string
    {
        $tokenPrefix = strtoupper(substr($this->verification_token ?? '', 0, 6));

        return "#FUD-{$this->id}-{$tokenPrefix}";
    }

    /**
     * Determina si la reserva está pendiente de validación por correo.
     */
    public function isPending(): bool
    {
        return $this->status === CourseRegistrationStatus::PENDING;
    }

    /**
     * Determina si la reserva está activa y dentro del plazo.
     */
    public function isVerifiedActive(): bool
    {
        return $this->status === CourseRegistrationStatus::VERIFIED
            && $this->expires_at !== null
            && $this->expires_at->isFuture();
    }

    /**
     * Determina si el plazo de la reserva ya expiró.
     */
    public function isExpired(): bool
    {
        if ($this->status === CourseRegistrationStatus::EXPIRED) {
            return true;
        }

        return $this->status === CourseRegistrationStatus::VERIFIED
            && $this->expires_at !== null
            && $this->expires_at->isPast();
    }

    /**
     * Determina si la inscripción fue formalizada en sede.
     */
    public function isEnrolled(): bool
    {
        return $this->status === CourseRegistrationStatus::ENROLLED;
    }

    /**
     * Scope para reservas verificadas activas en plazo.
     */
    public function scopeActiveReservations($query)
    {
        return $query->where('status', CourseRegistrationStatus::VERIFIED)
            ->where('expires_at', '>', now());
    }
}
