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
    public string $facebook_url = '';
    public string $instagram_url = '';
    public string $twitter_url = '';
    public string $youtube_url = '';
    public string $tiktok_url = '';
    public string $whatsapp_url = '';
    public string $linkedin_url = '';

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
        $this->facebook_url = $settings->facebook_url ?? '';
        $this->instagram_url = $settings->instagram_url ?? '';
        $this->twitter_url = $settings->twitter_url ?? '';
        $this->youtube_url = $settings->youtube_url ?? '';
        $this->tiktok_url = $settings->tiktok_url ?? '';
        $this->whatsapp_url = $settings->whatsapp_url ?? '';
        $this->linkedin_url = $settings->linkedin_url ?? '';
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
            'facebook_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'youtube_url' => 'nullable|url',
            'tiktok_url' => 'nullable|url',
            'whatsapp_url' => 'nullable|string',
            'linkedin_url' => 'nullable|url',
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
                    <flux:subheading>{{ __('Official social media profiles and messaging channels for the foundation.') }}</flux:subheading>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input wire:model="facebook_url" :label="__('Facebook')" type="url" placeholder="https://facebook.com/fudounar" />
                    <flux:input wire:model="instagram_url" :label="__('Instagram')" type="url" placeholder="https://instagram.com/fudounar" />
                    <flux:input wire:model="twitter_url" :label="__('X (Twitter)')" type="url" placeholder="https://x.com/fudounar" />
                    <flux:input wire:model="youtube_url" :label="__('YouTube')" type="url" placeholder="https://youtube.com/@fudounar" />
                    <flux:input wire:model="tiktok_url" :label="__('TikTok')" type="url" placeholder="https://tiktok.com/@fudounar" />
                    <flux:input wire:model="whatsapp_url" :label="__('WhatsApp (Phone or Link)')" type="text" placeholder="+1 809 000 0000 o https://wa.me/..." />
                    <flux:input wire:model="linkedin_url" :label="__('LinkedIn')" type="url" placeholder="https://linkedin.com/company/fudounar" class="md:col-span-2" />
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
