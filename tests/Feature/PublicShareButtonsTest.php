<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Course;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicShareButtonsTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_show_page_renders_share_buttons(): void
    {
        $activity = Activity::factory()->active()->create([
            'title' => 'Jornada Comunitaria de Salud',
            'slug' => 'jornada-comunitaria-de-salud',
        ]);

        $response = $this->get(route('activities.show', $activity->slug));

        $response->assertOk();
        $response->assertSee('Compartir:');
        $response->assertSee('https://api.whatsapp.com/send?text=');
        $response->assertSee('https://www.facebook.com/sharer/sharer.php?u=');
        $response->assertSee('https://twitter.com/intent/tweet?');
        $response->assertSee('https://www.linkedin.com/sharing/share-offsite/?url=');
        $response->assertSee('Copiar enlace');
    }

    public function test_blog_show_page_renders_share_buttons(): void
    {
        $post = Post::factory()->published()->create([
            'title' => 'Avances de los Programas Sociales',
            'slug' => 'avances-de-los-programas-sociales',
        ]);

        $response = $this->get(route('blog.show', $post->slug));

        $response->assertOk();
        $response->assertSee('Compartir:');
        $response->assertSee('https://api.whatsapp.com/send?text=');
        $response->assertSee('https://www.facebook.com/sharer/sharer.php?u=');
        $response->assertSee('https://twitter.com/intent/tweet?');
        $response->assertSee('https://www.linkedin.com/sharing/share-offsite/?url=');
        $response->assertSee('Copiar enlace');
    }

    public function test_course_show_page_renders_share_buttons(): void
    {
        $course = Course::factory()->open()->create([
            'title' => 'Taller de Liderazgo y Desarrollo',
            'slug' => 'taller-de-liderazgo-y-desarrollo',
        ]);

        $response = $this->get(route('courses.show', $course->slug));

        $response->assertOk();
        $response->assertSee('Compartir:');
        $response->assertSee('https://api.whatsapp.com/send?text=');
        $response->assertSee('https://www.facebook.com/sharer/sharer.php?u=');
        $response->assertSee('https://twitter.com/intent/tweet?');
        $response->assertSee('https://www.linkedin.com/sharing/share-offsite/?url=');
        $response->assertSee('Copiar enlace');
    }
}
