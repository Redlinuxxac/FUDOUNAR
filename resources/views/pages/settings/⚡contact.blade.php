<?php

use App\Models\ContactSetting;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Contact Settings')] class extends Component {
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public string $google_maps_url = '';
    public string $adsense_id = '';
    public string $google_analytics_id = '';
    public string $google_search_console_id = '';
    public string $facebook_url = '';
    public bool $is_facebook_active = true;
    public string $instagram_url = '';
    public bool $is_instagram_active = true;
    public string $twitter_url = '';
    public bool $is_twitter_active = true;
    public string $youtube_url = '';
    public bool $is_youtube_active = true;
    public string $tiktok_url = '';
    public bool $is_tiktok_active = true;
    public string $whatsapp_url = '';
    public bool $is_whatsapp_active = true;
    public string $linkedin_url = '';
    public bool $is_linkedin_active = true;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $settings = ContactSetting::first() ?? new ContactSetting();
        
        $this->email = $settings->email ?? '';
        $this->phone = $settings->phone ?? '';
        $this->address = $settings->address ?? '';
        $this->google_maps_url = $settings->google_maps_url ?? '';
        $this->adsense_id = $settings->adsense_id ?? '';
        $this->google_analytics_id = $settings->google_analytics_id ?? '';
        $this->google_search_console_id = $settings->google_search_console_id ?? '';
        $this->facebook_url = $settings->facebook_url ?? '';
        $this->is_facebook_active = (bool) ($settings->is_facebook_active ?? true);
        $this->instagram_url = $settings->instagram_url ?? '';
        $this->is_instagram_active = (bool) ($settings->is_instagram_active ?? true);
        $this->twitter_url = $settings->twitter_url ?? '';
        $this->is_twitter_active = (bool) ($settings->is_twitter_active ?? true);
        $this->youtube_url = $settings->youtube_url ?? '';
        $this->is_youtube_active = (bool) ($settings->is_youtube_active ?? true);
        $this->tiktok_url = $settings->tiktok_url ?? '';
        $this->is_tiktok_active = (bool) ($settings->is_tiktok_active ?? true);
        $this->whatsapp_url = $settings->whatsapp_url ?? '';
        $this->is_whatsapp_active = (bool) ($settings->is_whatsapp_active ?? true);
        $this->linkedin_url = $settings->linkedin_url ?? '';
        $this->is_linkedin_active = (bool) ($settings->is_linkedin_active ?? true);
    }

    /**
     * Update the contact settings.
     */
    public function updateContactInformation(): void
    {
        $validated = $this->validate([
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'required|string',
            'google_maps_url' => 'nullable|url',
            'adsense_id' => 'nullable|string|regex:/^pub-\d+$/',
            'google_analytics_id' => 'nullable|string|max:50',
            'google_search_console_id' => 'nullable|string|max:150',
            'facebook_url' => 'nullable|url',
            'is_facebook_active' => 'boolean',
            'instagram_url' => 'nullable|url',
            'is_instagram_active' => 'boolean',
            'twitter_url' => 'nullable|url',
            'is_twitter_active' => 'boolean',
            'youtube_url' => 'nullable|url',
            'is_youtube_active' => 'boolean',
            'tiktok_url' => 'nullable|url',
            'is_tiktok_active' => 'boolean',
            'whatsapp_url' => 'nullable|string',
            'is_whatsapp_active' => 'boolean',
            'linkedin_url' => 'nullable|url',
            'is_linkedin_active' => 'boolean',
        ]);

        $settings = ContactSetting::first() ?? new ContactSetting();
        $settings->fill($validated);
        $settings->save();

        Flux::toast(variant: 'success', text: __('Contact information updated.'));
    }

    /**
     * Get the map preview URL.
     */
    public function getMapPreviewUrlProperty(): string
    {
        if ($this->google_maps_url) {
            return $this->google_maps_url;
        }

        if ($this->address) {
            return "https://maps.google.com/maps?q=" . urlencode($this->address) . "&output=embed";
        }

        return '';
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Contact settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Contact Info')" :subheading="__('Update the public contact information for the foundation')">
        <form wire:submit="updateContactInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="email" :label="__('Email Address')" type="email" required placeholder="contacto@fudounar.org" />
            
            <flux:input wire:model="phone" :label="__('Phone Number')" type="text" required placeholder="+1 809 000 0000" />
            
            <flux:textarea wire:model.live.debounce.500ms="address" :label="__('Physical Address')" required placeholder="Calle #, Ciudad, País" rows="3" />

            <flux:input wire:model.live.debounce.500ms="google_maps_url" :label="__('Google Maps Embed URL')" type="url" placeholder="https://www.google.com/maps/embed?..." />
            
            <flux:text size="xs" class="text-neutral-500 italic">
                * {{ __('Copy the "Embed map" URL from Google Maps to show it on the contact page.') }}
            </flux:text>

            <flux:separator variant="subtle" />

            <flux:input wire:model="adsense_id" :label="__('Google AdSense Publisher ID')" placeholder="pub-xxxxxxxxxxxxxxxx" />
            <flux:text size="xs" class="text-neutral-500 italic">
                * {{ __('Your Publisher ID (e.g., pub-1234567890123456). This will enable Google Ads on the website.') }}
            </flux:text>

            <flux:input wire:model="google_analytics_id" :label="__('Google Analytics ID (GA4)')" placeholder="G-XXXXXXXXXX" />
            <flux:text size="xs" class="text-neutral-500 italic">
                * {{ __('ID de medición de Google Analytics (ejemplo: G-1234567890) para medir el tráfico y visitantes.') }}
            </flux:text>

            <flux:input wire:model="google_search_console_id" :label="__('Google Search Console (Código de verificación)')" placeholder="ej: dXRhLXBs... o etiqueta de verificación" />
            <flux:text size="xs" class="text-neutral-500 italic">
                * {{ __('Código del meta tag de verificación de propiedad de Google Search Console.') }}
            </flux:text>

            @if($this->mapPreviewUrl)
                <div class="mt-4 rounded-xl overflow-hidden border border-neutral-200 dark:border-neutral-700 h-64 bg-gray-100 dark:bg-neutral-900 shadow-sm relative">
                    <div wire:loading wire:target="address, google_maps_url" class="absolute inset-0 bg-white/50 dark:bg-black/50 flex items-center justify-center z-10 backdrop-blur-sm">
                        <flux:spacer />
                        <flux:icon name="arrow-path" class="animate-spin" />
                    </div>
                    <iframe 
                        src="{{ $this->mapPreviewUrl }}" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                @if(!$google_maps_url && $address)
                    <flux:text size="xs" class="text-blue-500 mt-2">
                        {{ __('Previewing based on physical address. For better accuracy, provide a Google Maps Embed URL.') }}
                    </flux:text>
                @endif
            @endif

            <flux:separator variant="subtle" />

            <div class="space-y-4">
                <div>
                    <flux:heading size="lg">{{ __('Social Media') }}</flux:heading>
                    <flux:subheading>{{ __('Official social media profiles and messaging channels for the foundation. You can enable or disable any network.') }}</flux:subheading>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Facebook -->
                    <div class="p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/40 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-sm text-neutral-800 dark:text-neutral-200 flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                {{ __('Facebook') }}
                            </span>
                            <flux:switch wire:model="is_facebook_active" :label="__('Active')" />
                        </div>
                        <flux:input wire:model="facebook_url" type="url" placeholder="https://facebook.com/fudounar" />
                    </div>

                    <!-- Instagram -->
                    <div class="p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/40 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-sm text-neutral-800 dark:text-neutral-200 flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#E4405F]" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                                {{ __('Instagram') }}
                            </span>
                            <flux:switch wire:model="is_instagram_active" :label="__('Active')" />
                        </div>
                        <flux:input wire:model="instagram_url" type="url" placeholder="https://instagram.com/fudounar" />
                    </div>

                    <!-- Twitter / X -->
                    <div class="p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/40 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-sm text-neutral-800 dark:text-neutral-200 flex items-center gap-2">
                                <svg class="w-4 h-4 text-black dark:text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                                {{ __('X (Twitter)') }}
                            </span>
                            <flux:switch wire:model="is_twitter_active" :label="__('Active')" />
                        </div>
                        <flux:input wire:model="twitter_url" type="url" placeholder="https://x.com/fudounar" />
                    </div>

                    <!-- YouTube -->
                    <div class="p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/40 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-sm text-neutral-800 dark:text-neutral-200 flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#FF0000]" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                                {{ __('YouTube') }}
                            </span>
                            <flux:switch wire:model="is_youtube_active" :label="__('Active')" />
                        </div>
                        <flux:input wire:model="youtube_url" type="url" placeholder="https://youtube.com/@fudounar" />
                    </div>

                    <!-- TikTok -->
                    <div class="p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/40 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-sm text-neutral-800 dark:text-neutral-200 flex items-center gap-2">
                                <svg class="w-4 h-4 text-black dark:text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-1.01v8.42c0 1.63-.44 3.28-1.34 4.62-.89 1.34-2.18 2.37-3.69 2.93-1.51.56-3.18.59-4.71.09-1.53-.5-2.89-1.49-3.83-2.79-1.25-1.72-1.74-3.94-1.33-6.04.41-2.1 1.74-3.9 3.65-4.89 1.25-.65 2.69-.94 4.09-.85v4.14c-.67-.09-1.36.03-1.95.34-.6.31-1.05.85-1.25 1.5-.2.65-.13 1.37.19 1.96.32.59.88 1.01 1.53 1.17.65.16 1.35.05 1.92-.3.57-.35.95-.94 1.04-1.61.08-.62.07-1.25.07-1.87V.02h-.44z"/>
                                </svg>
                                {{ __('TikTok') }}
                            </span>
                            <flux:switch wire:model="is_tiktok_active" :label="__('Active')" />
                        </div>
                        <flux:input wire:model="tiktok_url" type="url" placeholder="https://tiktok.com/@fudounar" />
                    </div>

                    <!-- WhatsApp -->
                    <div class="p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/40 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-sm text-neutral-800 dark:text-neutral-200 flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2zm.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 012.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.42 0-2.82-.37-4.05-1.07l-.29-.17-3.12.82.83-3.04-.19-.3a8.216 8.216 0 01-1.26-4.47c0-4.54 3.7-8.24 8.24-8.24zm4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.02-1.24-.74-.66-1.24-1.48-1.39-1.73-.14-.25-.02-.39.11-.51.11-.11.25-.29.37-.43.13-.14.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.13-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.13.17 1.78 2.72 4.31 3.81.6.26 1.07.42 1.44.53.61.2 1.16.17 1.6.11.49-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.14-1.18-.06-.11-.23-.17-.48-.29z"/>
                                </svg>
                                {{ __('WhatsApp') }}
                            </span>
                            <flux:switch wire:model="is_whatsapp_active" :label="__('Active')" />
                        </div>
                        <flux:input wire:model="whatsapp_url" type="text" placeholder="+1 809 000 0000 o https://wa.me/..." />
                    </div>

                    <!-- LinkedIn -->
                    <div class="p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/40 space-y-3 md:col-span-2">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-sm text-neutral-800 dark:text-neutral-200 flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#0A66C2]" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                </svg>
                                {{ __('LinkedIn') }}
                            </span>
                            <flux:switch wire:model="is_linkedin_active" :label="__('Active')" />
                        </div>
                        <flux:input wire:model="linkedin_url" type="url" placeholder="https://linkedin.com/company/fudounar" />
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit" class="w-full">
                    {{ __('Save Changes') }}
                </flux:button>
            </div>
        </form>
    </x-pages::settings.layout>
</section>
