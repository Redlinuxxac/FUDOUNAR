<?php

namespace Tests\Feature;

use App\Models\AboutPage;
use App\Models\ContactSetting;
use App\Models\User;
use Database\Seeders\ProductionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductionSeederTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that production seeder seeds all necessary production data correctly.
     */
    public function test_production_seeder_executes_all_required_seeders(): void
    {
        $this->seed(ProductionSeeder::class);

        // Check AboutPage seeded
        $this->assertDatabaseCount('about_pages', 1);
        $aboutPage = AboutPage::first();
        $this->assertNotNull($aboutPage);
        $this->assertNotEmpty($aboutPage->mission_text);

        // Check ContactSetting seeded
        $this->assertDatabaseCount('contact_settings', 1);
        $contact = ContactSetting::first();
        $this->assertNotNull($contact);
        $this->assertEquals('contacto@fudounar.org', $contact->email);

        // Check Users seeded
        $this->assertDatabaseHas('users', [
            'email' => 'RosarioEdwinAC@gmail.com',
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'kendry.rosario@fudounar.org',
        ]);

        // Check Roles and Permissions
        $this->assertDatabaseHas('roles', [
            'name' => 'root',
        ]);
        $this->assertDatabaseHas('roles', [
            'name' => 'admin',
        ]);
        $this->assertDatabaseHas('roles', [
            'name' => 'editor',
        ]);

        // Check root user has root role
        $rootUser = User::where('email', 'like', 'rosarioedwinac%')->first();
        $this->assertNotNull($rootUser);
        $this->assertTrue($rootUser->hasRole('root'));
    }
}
