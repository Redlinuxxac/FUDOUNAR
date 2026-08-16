<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ejecutar seeders para roles y permisos
        $this->seed(RolesAndPermissionsSeeder::class);

        // Asegurarnos de que el admin existe en la base de datos de pruebas
        $admin = User::firstOrCreate([
            'email' => 'admin@fudounar.org',
        ], [
            'name' => 'Test Admin',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('admin');
    }

    public function test_non_authorized_user_cannot_access_user_crud()
    {
        $user = User::factory()->create();
        // Asignarle rol editor (no tiene permiso manage-users)
        $user->assignRole('editor');

        $this->actingAs($user);

        $response = $this->get(route('admin.users'));
        $response->assertStatus(403);
    }

    public function test_root_user_can_access_user_crud()
    {
        $rootUser = User::where('email', 'rosarioedwinac@gmail.com')->first();
        $this->actingAs($rootUser);

        $response = $this->get(route('admin.users'));
        $response->assertStatus(200);
    }

    public function test_root_user_can_create_admin()
    {
        $rootUser = User::where('email', 'rosarioedwinac@gmail.com')->first();
        $this->actingAs($rootUser);

        Volt::test('admin.users.create')
            ->set('name', 'New Admin')
            ->set('email', 'new_admin@fudounar.org')
            ->set('password', 'password123')
            ->set('role', 'admin')
            ->call('save')
            ->assertRedirect(route('admin.users'));

        $newAdmin = User::where('email', 'new_admin@fudounar.org')->first();
        $this->assertNotNull($newAdmin);
        $this->assertTrue($newAdmin->hasRole('admin'));
    }

    public function test_admin_user_cannot_create_admin()
    {
        $adminUser = User::where('email', 'admin@fudounar.org')->first();
        $this->actingAs($adminUser);

        Volt::test('admin.users.create')
            ->set('name', 'Another Admin')
            ->set('email', 'another_admin@fudounar.org')
            ->set('password', 'password123')
            ->set('role', 'admin')
            ->call('save')
            ->assertStatus(403);

        $notCreated = User::where('email', 'another_admin@fudounar.org')->first();
        $this->assertNull($notCreated);
    }

    public function test_admin_user_can_create_editor()
    {
        $adminUser = User::where('email', 'admin@fudounar.org')->first();
        $this->actingAs($adminUser);

        Volt::test('admin.users.create')
            ->set('name', 'New Editor')
            ->set('email', 'editor@fudounar.org')
            ->set('password', 'password123')
            ->set('role', 'editor')
            ->call('save')
            ->assertRedirect(route('admin.users'));

        $newEditor = User::where('email', 'editor@fudounar.org')->first();
        $this->assertNotNull($newEditor);
        $this->assertTrue($newEditor->hasRole('editor'));
    }

    public function test_admin_user_cannot_delete_another_admin()
    {
        $adminUser = User::where('email', 'admin@fudounar.org')->first();
        $otherAdmin = User::factory()->create([
            'email' => 'otheradmin@fudounar.org',
        ]);
        $otherAdmin->assignRole('admin');

        $this->actingAs($adminUser);

        Volt::test('admin.users.index')
            ->call('delete', $otherAdmin->id)
            ->assertStatus(403);

        $this->assertDatabaseHas('users', ['id' => $otherAdmin->id]);
    }

    public function test_root_user_can_delete_admin()
    {
        $rootUser = User::where('email', 'rosarioedwinac@gmail.com')->first();
        $adminToDelete = User::where('email', 'admin@fudounar.org')->first();

        $this->actingAs($rootUser);

        Volt::test('admin.users.index')
            ->call('delete', $adminToDelete->id);

        $this->assertDatabaseMissing('users', ['id' => $adminToDelete->id]);
    }

    public function test_no_one_can_delete_root()
    {
        $rootUser = User::where('email', 'rosarioedwinac@gmail.com')->first();

        // Intentar borrarlo como admin
        $adminUser = User::where('email', 'admin@fudounar.org')->first();
        $this->actingAs($adminUser);

        Volt::test('admin.users.index')
            ->call('delete', $rootUser->id)
            ->assertStatus(403);

        $this->assertDatabaseHas('users', ['id' => $rootUser->id]);
    }

    public function test_admin_user_cannot_see_root_user_in_list()
    {
        $adminUser = User::where('email', 'admin@fudounar.org')->first();
        $this->actingAs($adminUser);

        // El componente index de usuarios no debe devolver al usuario root en su lista de usuarios
        Volt::test('admin.users.index')
            ->assertViewHas('users', function ($users) {
                foreach ($users as $user) {
                    if ($user->hasRole('root') || str_contains(strtolower($user->email), 'rosarioedwinac')) {
                        return false;
                    }
                }

                return true;
            });
    }

    public function test_admin_user_cannot_access_root_edit_form()
    {
        $rootUser = User::where('email', 'rosarioedwinac@gmail.com')->first();
        $adminUser = User::where('email', 'admin@fudounar.org')->first();

        $this->actingAs($adminUser);

        $response = $this->get(route('admin.users.edit', $rootUser));
        $response->assertStatus(403);
    }

    public function test_non_authorized_user_cannot_access_roles_and_permissions()
    {
        $user = User::factory()->create();
        $user->assignRole('editor');
        $this->actingAs($user);

        $response = $this->get(route('admin.roles'));
        $response->assertStatus(403);

        $response = $this->get(route('admin.permissions'));
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_roles_and_permissions()
    {
        $adminUser = User::where('email', 'admin@fudounar.org')->first();
        $this->actingAs($adminUser);

        $response = $this->get(route('admin.roles'));
        $response->assertStatus(200);

        $response = $this->get(route('admin.permissions'));
        $response->assertStatus(200);
    }
}
