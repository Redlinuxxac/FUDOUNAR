<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SocialShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'shareable_type',
        'shareable_id',
        'platform',
        'ip_address',
    ];

    /**
     * Get the owning shareable model (Activity, Post, Course).
     */
    public function shareable(): MorphTo
    {
        return $this->morphTo();
    }
}
