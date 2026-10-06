<?php

namespace App\Models;

use App\Enums\PostStatus;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'image',
        'status',
        'views',
        'published_at',
    ];

    protected $casts = [
        'status' => PostStatus::class,
        'views' => 'integer',
        'published_at' => 'datetime',
    ];

    public function shares(): MorphMany
    {
        return $this->morphMany(SocialShare::class, 'shareable');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = static::generateUniqueSlug($post->title);
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('title')) {
                $post->slug = static::generateUniqueSlug($post->title);
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
     * Scope a query to only include published posts.
     */
    public function scopePublished($query)
    {
        return $query->where('status', PostStatus::PUBLISHED);
    }

    /**
     * Get the resolved public URL for the post image.
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
