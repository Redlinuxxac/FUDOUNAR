<?php

namespace Tests\Feature\Admin;

use App\Models\Slide;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;
use Tests\TestCase;

class SlideManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_guests_cannot_access_slides_admin(): void
    {
        $slide = Slide::factory()->create();

        $this->get(route('admin.slides'))->assertRedirect(route('login'));
        $this->get(route('admin.slides.create'))->assertRedirect(route('login'));
        $this->get(route('admin.slides.edit', $slide))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_slides_list(): void
    {
        $slide = Slide::factory()->create([
            'title' => 'Slide de Prueba Comunitario',
        ]);

        $this->actingAs($this->user)
            ->get(route('admin.slides'))
            ->assertOk()
            ->assertSee('Gestión del Carrusel (Slides)')
            ->assertSee('Slide de Prueba Comunitario');
    }

    public function test_authenticated_user_can_create_slide(): void
    {
        $this->actingAs($this->user);

        Volt::test('admin.slides.create')
            ->set('title', 'Nuevo Slide 2026')
            ->set('subtitle', 'Transformando el futuro juntos')
            ->set('button_text', 'Conoce Más')
            ->set('button_link', '/quienes-somos')
            ->set('button_secondary_text', 'Contacto')
            ->set('button_secondary_link', '/contacto')
            ->set('order', 1)
            ->set('is_active', true)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.slides'));

        $this->assertDatabaseHas('slides', [
            'title' => 'Nuevo Slide 2026',
            'subtitle' => 'Transformando el futuro juntos',
            'button_text' => 'Conoce Más',
            'order' => 1,
            'is_active' => true,
        ]);
    }

    public function test_authenticated_user_can_create_slide_with_image_upload(): void
    {
        Storage::fake('public');
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->image('banner.jpg', 1200, 600);

        Volt::test('admin.slides.create')
            ->set('title', 'Slide con Imagen Subida')
            ->set('image', $file)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.slides'));

        $slide = Slide::where('title', 'Slide con Imagen Subida')->first();
        $this->assertNotNull($slide);
        $this->assertStringContainsString('/storage/slides/', $slide->image);
    }

    public function test_authenticated_user_can_update_slide(): void
    {
        $slide = Slide::factory()->create([
            'title' => 'Título Inicial',
            'order' => 5,
        ]);

        $this->actingAs($this->user);

        Volt::test('admin.slides.edit', ['slide' => $slide])
            ->set('title', 'Título Modificado')
            ->set('order', 10)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.slides'));

        $this->assertDatabaseHas('slides', [
            'id' => $slide->id,
            'title' => 'Título Modificado',
            'order' => 10,
        ]);
    }

    public function test_authenticated_user_can_toggle_slide_status(): void
    {
        $slide = Slide::factory()->create([
            'is_active' => true,
        ]);

        $this->actingAs($this->user);

        Volt::test('admin.slides.index')
            ->call('toggleStatus', $slide->id);

        $this->assertFalse($slide->fresh()->is_active);

        Volt::test('admin.slides.index')
            ->call('toggleStatus', $slide->id);

        $this->assertTrue($slide->fresh()->is_active);
    }

    public function test_authenticated_user_can_reorder_slides(): void
    {
        $slide1 = Slide::factory()->create(['order' => 1]);
        $slide2 = Slide::factory()->create(['order' => 2]);

        $this->actingAs($this->user);

        Volt::test('admin.slides.index')
            ->call('moveDown', $slide1->id);

        $this->assertEquals(2, $slide1->fresh()->order);
        $this->assertEquals(1, $slide2->fresh()->order);

        Volt::test('admin.slides.index')
            ->call('moveUp', $slide1->id);

        $this->assertEquals(1, $slide1->fresh()->order);
        $this->assertEquals(2, $slide2->fresh()->order);
    }

    public function test_authenticated_user_can_delete_slide(): void
    {
        $slide = Slide::factory()->create();

        $this->actingAs($this->user);

        Volt::test('admin.slides.index')
            ->call('delete', $slide->id);

        $this->assertDatabaseMissing('slides', [
            'id' => $slide->id,
        ]);
    }

    public function test_home_page_displays_active_slides_in_order_and_ignores_inactive(): void
    {
        $slideActive1 = Slide::factory()->create([
            'title' => 'Primer Slide Activo',
            'order' => 1,
            'is_active' => true,
        ]);

        $slideActive2 = Slide::factory()->create([
            'title' => 'Segundo Slide Activo',
            'order' => 2,
            'is_active' => true,
        ]);

        $slideInactive = Slide::factory()->inactive()->create([
            'title' => 'Slide Oculto Inactivo',
            'order' => 0,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Primer Slide Activo');
        $response->assertSee('Segundo Slide Activo');
        $response->assertDontSee('Slide Oculto Inactivo');
    }
}
