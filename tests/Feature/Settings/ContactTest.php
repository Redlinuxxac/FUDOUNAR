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
            ->set('instagram_url', 'https://instagram.com/fudounar')
            ->set('twitter_url', 'https://x.com/fudounar')
            ->set('youtube_url', 'https://youtube.com/@fudounar')
            ->set('tiktok_url', 'https://tiktok.com/@fudounar')
            ->set('whatsapp_url', '+1 809 123 4567')
            ->set('linkedin_url', 'https://linkedin.com/company/fudounar')
            ->call('updateContactInformation')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('contact_settings', [
            'email' => 'new@example.com',
            'phone' => '123456789',
            'address' => 'New Address 123',
            'google_maps_url' => 'https://www.google.com/maps/embed?pb=test',
            'facebook_url' => 'https://facebook.com/fudounar',
            'instagram_url' => 'https://instagram.com/fudounar',
            'twitter_url' => 'https://x.com/fudounar',
            'youtube_url' => 'https://youtube.com/@fudounar',
            'tiktok_url' => 'https://tiktok.com/@fudounar',
            'whatsapp_url' => '+1 809 123 4567',
            'linkedin_url' => 'https://linkedin.com/company/fudounar',
        ]);
    }

    public function test_public_contact_page_renders_social_media_links(): void
    {
        ContactSetting::create([
            'email' => 'contacto@fudounar.org',
            'phone' => '+1 809 000 0000',
            'facebook_url' => 'https://facebook.com/fudounar',
            'instagram_url' => 'https://instagram.com/fudounar',
            'whatsapp_url' => '+1 809 123 4567',
        ]);

        $response = $this->get(route('contact'));

        $response->assertOk();
        $response->assertSee('https://facebook.com/fudounar');
        $response->assertSee('https://instagram.com/fudounar');
        $response->assertSee('https://wa.me/18091234567');
        $response->assertSee('Síguenos en nuestras Redes Sociales');
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
}
