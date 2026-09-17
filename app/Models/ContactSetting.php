<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSetting extends Model
{
    protected $guarded = [];

    /**
     * Get the formatted WhatsApp link.
     */
    public function getWhatsappLinkAttribute(): ?string
    {
        if (blank($this->whatsapp_url)) {
            return null;
        }

        if (str_starts_with($this->whatsapp_url, 'http://') || str_starts_with($this->whatsapp_url, 'https://')) {
            return $this->whatsapp_url;
        }

        $digits = preg_replace('/[^\d]/', '', $this->whatsapp_url);

        return $digits ? "https://wa.me/{$digits}" : null;
    }

    /**
     * Determine if any social media link is set.
     */
    public function hasSocialLinks(): bool
    {
        return ! empty($this->facebook_url)
            || ! empty($this->instagram_url)
            || ! empty($this->twitter_url)
            || ! empty($this->youtube_url)
            || ! empty($this->tiktok_url)
            || ! empty($this->whatsapp_url)
            || ! empty($this->linkedin_url);
    }
}
