<?php

namespace Tests\Feature\Settings;

use App\Models\ContactSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_settings_page_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('contact.edit'));

        $response->assertOk();
        $response->assertSee('Contact Info');
    }

    public function test_contact_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test('pages::settings.contact')
            ->set('email', 'new@example.com')
            ->set('phone', '123456789')
            ->set('address', 'New Address 123')
            ->set('google_maps_url', 'https://www.google.com/maps/embed?pb=test')
            ->set('facebook_url', 'https://facebook.com/fudounar')
            ->set('is_facebook_active', false)
            ->set('instagram_url', 'https://instagram.com/fudounar')
            ->set('is_instagram_active', true)
            ->set('twitter_url', 'https://x.com/fudounar')
            ->set('is_twitter_active', true)
            ->set('youtube_url', 'https://youtube.com/@fudounar')
            ->set('is_youtube_active', false)
            ->set('tiktok_url', 'https://tiktok.com/@fudounar')
            ->set('is_tiktok_active', true)
            ->set('whatsapp_url', '+1 809 123 4567')
            ->set('is_whatsapp_active', false)
            ->set('linkedin_url', 'https://linkedin.com/company/fudounar')
            ->set('is_linkedin_active', true)
            ->call('updateContactInformation')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('contact_settings', [
            'email' => 'new@example.com',
            'phone' => '123456789',
            'address' => 'New Address 123',
            'google_maps_url' => 'https://www.google.com/maps/embed?pb=test',
            'facebook_url' => 'https://facebook.com/fudounar',
            'is_facebook_active' => false,
            'instagram_url' => 'https://instagram.com/fudounar',
            'is_instagram_active' => true,
            'twitter_url' => 'https://x.com/fudounar',
            'is_twitter_active' => true,
            'youtube_url' => 'https://youtube.com/@fudounar',
            'is_youtube_active' => false,
            'tiktok_url' => 'https://tiktok.com/@fudounar',
            'is_tiktok_active' => true,
            'whatsapp_url' => '+1 809 123 4567',
            'is_whatsapp_active' => false,
            'linkedin_url' => 'https://linkedin.com/company/fudounar',
            'is_linkedin_active' => true,
        ]);
    }

    public function test_public_contact_page_renders_social_media_links(): void
    {
        ContactSetting::create([
            'email' => 'contacto@fudounar.org',
            'phone' => '+1 809 000 0000',
            'facebook_url' => 'https://facebook.com/fudounar',
            'is_facebook_active' => true,
            'instagram_url' => 'https://instagram.com/fudounar',
            'is_instagram_active' => true,
            'whatsapp_url' => '+1 809 123 4567',
            'is_whatsapp_active' => true,
        ]);

        $response = $this->get(route('contact'));

        $response->assertOk();
        $response->assertSee('https://facebook.com/fudounar');
        $response->assertSee('https://instagram.com/fudounar');
        $response->assertSee('https://wa.me/18091234567');
        $response->assertSee('Síguenos en nuestras Redes Sociales');
    }

    public function test_public_contact_page_hides_deactivated_social_media_links(): void
    {
        ContactSetting::create([
            'email' => 'contacto@fudounar.org',
            'phone' => '+1 809 000 0000',
            'facebook_url' => 'https://facebook.com/fudounar',
            'is_facebook_active' => false,
            'instagram_url' => 'https://instagram.com/fudounar',
            'is_instagram_active' => true,
            'whatsapp_url' => '+1 809 123 4567',
            'is_whatsapp_active' => false,
        ]);

        $response = $this->get(route('contact'));

        $response->assertOk();
        $response->assertDontSee('https://facebook.com/fudounar');
        $response->assertSee('https://instagram.com/fudounar');
        $response->assertDontSee('https://wa.me/18091234567');
    }

    public function test_has_social_links_returns_false_and_hides_section_when_all_are_deactivated(): void
    {
        $contact = ContactSetting::create([
            'email' => 'contacto@fudounar.org',
            'phone' => '+1 809 000 0000',
            'facebook_url' => 'https://facebook.com/fudounar',
            'is_facebook_active' => false,
            'instagram_url' => 'https://instagram.com/fudounar',
            'is_instagram_active' => false,
            'whatsapp_url' => '+1 809 123 4567',
            'is_whatsapp_active' => false,
        ]);

        $this->assertFalse($contact->hasSocialLinks());

        $response = $this->get(route('contact'));

        $response->assertOk();
        $response->assertDontSee('Síguenos en nuestras Redes Sociales');
    }

    public function test_map_preview_url_updates_dynamically_with_google_maps_url(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $url = 'https://www.google.com/maps/embed?pb=test';

        Livewire::test('pages::settings.contact')
            ->set('google_maps_url', $url)
            ->assertSet('mapPreviewUrl', $url);
    }

    public function test_map_preview_url_updates_dynamically_with_address_when_url_is_empty(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $address = 'Santo Domingo, RD';
        $expectedUrl = 'https://maps.google.com/maps?q='.urlencode($address).'&output=embed';

        Livewire::test('pages::settings.contact')
            ->set('google_maps_url', '')
            ->set('address', $address)
            ->assertSet('mapPreviewUrl', $expectedUrl);
    }

    public function test_footer_renders_only_active_social_links(): void
    {
        ContactSetting::create([
            'email' => 'contacto@fudounar.org',
            'phone' => '+1 809 000 0000',
            'facebook_url' => 'https://facebook.com/fudounar',
            'is_facebook_active' => false,
            'instagram_url' => 'https://instagram.com/fudounar',
            'is_instagram_active' => true,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('https://facebook.com/fudounar');
        $response->assertSee('https://instagram.com/fudounar');
    }
}
