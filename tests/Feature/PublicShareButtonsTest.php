<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\ContactSetting;
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

    public function test_share_buttons_hide_when_specific_social_networks_are_deactivated(): void
    {
        ContactSetting::create([
            'email' => 'info@fudounar.org',
            'phone' => '+1 809 000 0000',
            'address' => 'Santo Domingo',
            'is_facebook_active' => false,
            'is_twitter_active' => false,
            'is_whatsapp_active' => true,
            'is_linkedin_active' => true,
        ]);

        $post = Post::factory()->published()->create();

        $response = $this->get(route('blog.show', $post->slug));

        $response->assertOk();
        $response->assertSee('Compartir:');
        $response->assertSee('https://api.whatsapp.com/send?text=');
        $response->assertSee('https://www.linkedin.com/sharing/share-offsite/?url=');
        $response->assertDontSee('https://www.facebook.com/sharer/sharer.php');
        $response->assertDontSee('https://twitter.com/intent/tweet');
    }

    public function test_share_buttons_hidden_completely_when_all_social_networks_are_inactive(): void
    {
        ContactSetting::create([
            'email' => 'info@fudounar.org',
            'phone' => '+1 809 000 0000',
            'address' => 'Santo Domingo',
            'is_facebook_active' => false,
            'is_twitter_active' => false,
            'is_whatsapp_active' => false,
            'is_linkedin_active' => false,
        ]);

        $post = Post::factory()->published()->create();

        $response = $this->get(route('blog.show', $post->slug));

        $response->assertOk();
        $response->assertDontSee('Compartir:');
        $response->assertDontSee('https://api.whatsapp.com/send?text=');
        $response->assertDontSee('https://www.facebook.com/sharer/sharer.php');
        $response->assertDontSee('https://twitter.com/intent/tweet');
        $response->assertDontSee('https://www.linkedin.com/sharing/share-offsite/?url=');
    }

    public function test_activity_open_graph_image_is_rendered(): void
    {
        $activity = Activity::factory()->active()->create([
            'title' => 'Jornada Urológica',
            'image' => 'https://example.com/images/actividad.jpg',
        ]);

        $response = $this->get(route('activities.show', $activity->slug));

        $response->assertOk();
        $response->assertSee('<meta property="og:image" content="'.$activity->image_url.'">', false);
        $response->assertSee('<meta name="twitter:image" content="'.$activity->image_url.'">', false);
    }

    public function test_blog_open_graph_image_is_rendered(): void
    {
        $post = Post::factory()->published()->create([
            'title' => 'Noticia de Salud',
            'image' => 'https://example.com/images/noticia.jpg',
        ]);

        $response = $this->get(route('blog.show', $post->slug));

        $response->assertOk();
        $response->assertSee('<meta property="og:image" content="'.$post->image_url.'">', false);
        $response->assertSee('<meta name="twitter:image" content="'.$post->image_url.'">', false);
    }

    public function test_course_open_graph_image_is_rendered(): void
    {
        $course = Course::factory()->open()->create([
            'title' => 'Curso de Cirugía',
            'image' => 'https://example.com/images/curso.jpg',
        ]);

        $response = $this->get(route('courses.show', $course->slug));

        $response->assertOk();
        $response->assertSee('<meta property="og:image" content="'.$course->image_url.'">', false);
        $response->assertSee('<meta name="twitter:image" content="'.$course->image_url.'">', false);
    }
}
