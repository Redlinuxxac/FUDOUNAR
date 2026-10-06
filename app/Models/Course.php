<?php

namespace App\Models;

use App\Enums\CourseModality;
use App\Enums\CourseRegistrationStatus;
use App\Enums\CourseStatus;
use Database\Factories\CourseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Course extends Model
{
    /** @use HasFactory<CourseFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'duration',
        'capacity',
        'reservation_days',
        'modality',
        'status',
        'views',
        'started_at',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'reservation_days' => 'integer',
        'status' => CourseStatus::class,
        'modality' => CourseModality::class,
        'views' => 'integer',
        'started_at' => 'datetime',
    ];

    public function shares(): HasMany|MorphMany
    {
        return $this->morphMany(SocialShare::class, 'shareable');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($course) {
            if (empty($course->slug)) {
                $course->slug = static::generateUniqueSlug($course->title);
            }
        });

        static::updating(function ($course) {
            if ($course->isDirty('title')) {
                $course->slug = static::generateUniqueSlug($course->title);
            }
        });
    }

    public static function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    /**
     * Scope a query to only include open courses.
     */
    public function scopeOpen($query)
    {
        return $query->where('status', CourseStatus::OPEN);
    }

    /**
     * Relación con las inscripciones del curso.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(CourseRegistration::class);
    }

    /**
     * Cantidad de cupos actualmente ocupados (formalizados o reservas activas no vencidas).
     */
    public function activeRegistrationsCount(): int
    {
        return $this->registrations()
            ->where(function ($query) {
                $query->where('status', CourseRegistrationStatus::ENROLLED)
                    ->orWhere(function ($q) {
                        $q->where('status', CourseRegistrationStatus::VERIFIED)
                            ->where('expires_at', '>', now());
                    });
            })
            ->count();
    }

    /**
     * Cupos restantes disponibles.
     */
    public function availableSlots(): int
    {
        return max(0, (int) ($this->capacity ?? 25) - $this->activeRegistrationsCount());
    }

    /**
     * Determina si el curso está completamente lleno.
     */
    public function isFull(): bool
    {
        return $this->availableSlots() <= 0;
    }

    /**
     * Determina si se admiten nuevas inscripciones.
     */
    public function canAcceptRegistrations(): bool
    {
        return $this->status === CourseStatus::OPEN && ! $this->isFull();
    }

    /**
     * Get the resolved public URL for the course image.
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->image)) {
            return asset('img/LogoMejorado.png');
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        if (str_starts_with($this->image, '/')) {
            return asset(ltrim($this->image, '/'));
        }

        if (str_starts_with($this->image, 'storage/')) {
            return asset($this->image);
        }

        return asset(ltrim(Storage::url($this->image), '/'));
    }
}
