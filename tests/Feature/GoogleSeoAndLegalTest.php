<?php

namespace Tests\Feature;

use App\Models\ContactSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GoogleSeoAndLegalTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_xml_returns_valid_xml_with_all_urls(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));
        $response->assertSee(route('home'));
        $response->assertSee(route('about'));
        $response->assertSee(route('activities'));
        $response->assertSee(route('blog'));
        $response->assertSee(route('courses'));
        $response->assertSee(route('contact'));
        $response->assertSee(route('privacy'));
        $response->assertSee(route('terms'));
    }

    public function test_privacy_policy_page_returns_ok_and_contains_google_adsense_disclosures(): void
    {
        $response = $this->get(route('privacy'));

        $response->assertOk();
        $response->assertSee('Política de Privacidad');
        $response->assertSee('Google AdSense');
        $response->assertSee('aboutads.info');
    }

    public function test_terms_and_conditions_page_returns_ok(): void
    {
        $response = $this->get(route('terms'));

        $response->assertOk();
        $response->assertSee('Términos y Condiciones');
    }

    public function test_home_page_contains_meta_description_canonical_and_json_ld(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('<meta name="description"', false);
        $response->assertSee('<link rel="canonical"', false);
        $response->assertSee('"@type": "NGO"', false);
        $response->assertSee('Aviso sobre Cookies');
    }

    public function test_google_analytics_and_search_console_are_rendered_when_configured(): void
    {
        ContactSetting::create([
            'email' => 'info@fudounar.org',
            'phone' => '809-000-0000',
            'address' => 'Santo Domingo',
            'google_analytics_id' => 'G-TEST123456',
            'google_search_console_id' => 'test-verification-code',
            'adsense_id' => 'pub-1234567890123456',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('G-TEST123456');
        $response->assertSee('test-verification-code');
        $response->assertSee('pub-1234567890123456');
    }

    public function test_google_analytics_and_search_console_can_be_updated_in_settings(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test('pages::settings.contact')
            ->set('email', 'admin@fudounar.org')
            ->set('phone', '809-555-5555')
            ->set('address', 'Calle Central #1')
            ->set('adsense_id', 'pub-9999999999999999')
            ->set('google_analytics_id', 'G-NEWANALYTICS')
            ->set('google_search_console_id', 'new-search-console-code')
            ->call('updateContactInformation')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('contact_settings', [
            'adsense_id' => 'pub-9999999999999999',
            'google_analytics_id' => 'G-NEWANALYTICS',
            'google_search_console_id' => 'new-search-console-code',
        ]);
    }
}
