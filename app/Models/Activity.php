<?php

namespace App\Models;

use App\Enums\ActivityStatus;
use Database\Factories\ActivityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Activity extends Model
{
    /** @use HasFactory<ActivityFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'status',
        'views',
        'started_at',
    ];

    protected $casts = [
        'status' => ActivityStatus::class,
        'views' => 'integer',
        'started_at' => 'datetime',
    ];

    public function shares(): MorphMany
    {
        return $this->morphMany(SocialShare::class, 'shareable');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($activity) {
            if (empty($activity->slug)) {
                $activity->slug = static::generateUniqueSlug($activity->title);
            }
        });

        static::updating(function ($activity) {
            if ($activity->isDirty('title')) {
                $activity->slug = static::generateUniqueSlug($activity->title);
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
     * Scope a query to only include active activities.
     */
    public function scopeActive($query)
    {
        return $query->where('status', ActivityStatus::ACTIVE);
    }

    /**
     * Get the resolved public URL for the activity image.
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
