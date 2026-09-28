<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSetting extends Model
{
    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_facebook_active' => 'boolean',
            'is_instagram_active' => 'boolean',
            'is_twitter_active' => 'boolean',
            'is_youtube_active' => 'boolean',
            'is_tiktok_active' => 'boolean',
            'is_whatsapp_active' => 'boolean',
            'is_linkedin_active' => 'boolean',
        ];
    }

    /**
     * Get the formatted WhatsApp link.
     */
    public function getWhatsappLinkAttribute(): ?string
    {
        if (blank($this->whatsapp_url) || ! ($this->is_whatsapp_active ?? true)) {
            return null;
        }

        if (str_starts_with($this->whatsapp_url, 'http://') || str_starts_with($this->whatsapp_url, 'https://')) {
            return $this->whatsapp_url;
        }

        $digits = preg_replace('/[^\d]/', '', $this->whatsapp_url);

        return $digits ? "https://wa.me/{$digits}" : null;
    }

    /**
     * Determine if Facebook is active and configured.
     */
    public function isFacebookVisible(): bool
    {
        return (bool) ($this->is_facebook_active ?? true) && ! empty($this->facebook_url);
    }

    /**
     * Determine if Instagram is active and configured.
     */
    public function isInstagramVisible(): bool
    {
        return (bool) ($this->is_instagram_active ?? true) && ! empty($this->instagram_url);
    }

    /**
     * Determine if Twitter/X is active and configured.
     */
    public function isTwitterVisible(): bool
    {
        return (bool) ($this->is_twitter_active ?? true) && ! empty($this->twitter_url);
    }

    /**
     * Determine if YouTube is active and configured.
     */
    public function isYoutubeVisible(): bool
    {
        return (bool) ($this->is_youtube_active ?? true) && ! empty($this->youtube_url);
    }

    /**
     * Determine if TikTok is active and configured.
     */
    public function isTiktokVisible(): bool
    {
        return (bool) ($this->is_tiktok_active ?? true) && ! empty($this->tiktok_url);
    }

    /**
     * Determine if WhatsApp is active and configured.
     */
    public function isWhatsappVisible(): bool
    {
        return (bool) ($this->is_whatsapp_active ?? true) && ! empty($this->whatsapp_url);
    }

    /**
     * Determine if LinkedIn is active and configured.
     */
    public function isLinkedinVisible(): bool
    {
        return (bool) ($this->is_linkedin_active ?? true) && ! empty($this->linkedin_url);
    }

    /**
     * Determine if Facebook sharing is active.
     */
    public function isFacebookShareActive(): bool
    {
        return (bool) ($this->is_facebook_active ?? true);
    }

    /**
     * Determine if Twitter/X sharing is active.
     */
    public function isTwitterShareActive(): bool
    {
        return (bool) ($this->is_twitter_active ?? true);
    }

    /**
     * Determine if WhatsApp sharing is active.
     */
    public function isWhatsappShareActive(): bool
    {
        return (bool) ($this->is_whatsapp_active ?? true);
    }

    /**
     * Determine if LinkedIn sharing is active.
     */
    public function isLinkedinShareActive(): bool
    {
        return (bool) ($this->is_linkedin_active ?? true);
    }

    /**
     * Determine if any social sharing network is active.
     */
    public function hasActiveShareNetworks(): bool
    {
        return $this->isWhatsappShareActive()
            || $this->isFacebookShareActive()
            || $this->isTwitterShareActive()
            || $this->isLinkedinShareActive();
    }

    /**
     * Determine if any social media link is set and active.
     */
    public function hasSocialLinks(): bool
    {
        return $this->isFacebookVisible()
            || $this->isInstagramVisible()
            || $this->isTwitterVisible()
            || $this->isYoutubeVisible()
            || $this->isTiktokVisible()
            || $this->isWhatsappVisible()
            || $this->isLinkedinVisible();
    }
}
