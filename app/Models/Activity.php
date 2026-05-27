<?php

namespace App\Models;

use App\Enums\ActivityStatus;
use Database\Factories\ActivityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'started_at',
    ];

    protected $casts = [
        'status' => ActivityStatus::class,
        'started_at' => 'datetime',
    ];

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
}
