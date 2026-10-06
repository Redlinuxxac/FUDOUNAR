<?php

namespace Tests\Feature;

use App\Enums\ActivityStatus;
use App\Enums\CourseRegistrationStatus;
use App\Enums\CourseStatus;
use App\Enums\PostStatus;
use App\Models\Activity;
use App\Models\ContactSetting;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Post;
use App\Models\SocialShare;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AnalyticsDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_sees_real_database_metrics_on_dashboard(): void
    {
        $user = User::factory()->create();

        // 1. Crear actividad real
        $activity = Activity::create([
            'title' => 'Taller Real de Inteligencia Artificial',
            'slug' => 'taller-real-ia',
            'description' => 'Descripción de prueba',
            'status' => ActivityStatus::ACTIVE,
            'views' => 1420,
        ]);

        // 2. Crear blog real
        $post = Post::create([
            'title' => 'Articulo Real sobre Integracion',
            'slug' => 'articulo-real-integracion',
            'content' => 'Contenido de prueba',
            'status' => PostStatus::PUBLISHED,
            'views' => 2450,
            'published_at' => now(),
        ]);

        // 3. Crear curso real e inscripciones
        $course = Course::create([
            'title' => 'Master Real en Programacion Web',
            'slug' => 'master-real-programacion-web',
            'description' => 'Curso de prueba',
            'duration' => 60,
            'capacity' => 30,
            'status' => CourseStatus::OPEN,
            'views' => 1800,
        ]);

        CourseRegistration::create([
            'course_id' => $course->id,
            'full_name' => 'Carlos Rodriguez',
            'email' => 'carlos@example.com',
            'phone' => '+297 594 1122',
            'status' => CourseRegistrationStatus::ENROLLED,
            'verification_token' => Str::random(40),
            'verified_at' => now(),
        ]);

        // 4. Crear compartidos reales en redes
        SocialShare::create([
            'shareable_type' => Activity::class,
            'shareable_id' => $activity->id,
            'platform' => 'whatsapp',
        ]);

        SocialShare::create([
            'shareable_type' => Post::class,
            'shareable_id' => $post->id,
            'platform' => 'facebook',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Dashboard Analítico');
        $response->assertSee('Taller Real de Inteligencia Artificial');
        $response->assertSee('Articulo Real sobre Integracion');
        $response->assertSee('Master Real en Programacion Web');
    }

    public function test_visiting_activity_detail_increments_views(): void
    {
        $activity = Activity::create([
            'title' => 'Actividad para Contar Vistas',
            'slug' => 'actividad-contar-vistas',
            'description' => 'Prueba de contador de visitas',
            'status' => ActivityStatus::ACTIVE,
            'views' => 10,
        ]);

        $this->get(route('activities.show', $activity->slug))->assertOk();

        $this->assertEquals(11, $activity->fresh()->views);
    }

    public function test_visiting_blog_detail_increments_views(): void
    {
        $post = Post::create([
            'title' => 'Post para Contar Vistas',
            'slug' => 'post-contar-vistas',
            'content' => 'Prueba de contador de visitas',
            'status' => PostStatus::PUBLISHED,
            'views' => 25,
            'published_at' => now(),
        ]);

        $this->get(route('blog.show', $post->slug))->assertOk();

        $this->assertEquals(26, $post->fresh()->views);
    }

    public function test_visiting_course_detail_increments_views(): void
    {
        $course = Course::create([
            'title' => 'Curso para Contar Vistas',
            'slug' => 'curso-contar-vistas',
            'description' => 'Prueba de contador de visitas',
            'duration' => 40,
            'capacity' => 20,
            'status' => CourseStatus::OPEN,
            'views' => 50,
        ]);

        $this->get(route('courses.show', $course->slug))->assertOk();

        $this->assertEquals(51, $course->fresh()->views);
    }

    public function test_track_share_endpoint_stores_social_share(): void
    {
        $activity = Activity::create([
            'title' => 'Actividad para Compartir',
            'slug' => 'actividad-para-compartir',
            'description' => 'Prueba de compartidos',
            'status' => ActivityStatus::ACTIVE,
        ]);

        $response = $this->postJson(route('track.share'), [
            'type' => 'activity',
            'id' => $activity->id,
            'platform' => 'whatsapp',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('social_shares', [
            'shareable_type' => Activity::class,
            'shareable_id' => $activity->id,
            'platform' => 'whatsapp',
        ]);
    }

    public function test_dashboard_can_be_filtered_by_preset_period(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard', ['period' => '30_days']));

        $response->assertOk();
        $response->assertSee('Filtro Activo');
        $response->assertSee('Último mes (30 días)');
        $response->assertSee('Limpiar Filtro');
    }

    public function test_dashboard_can_be_filtered_by_custom_date_interval(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard', [
            'period' => 'custom',
            'from' => '2026-09-01',
            'to' => '2026-10-05',
        ]));

        $response->assertOk();
        $response->assertSee('Filtro Activo');
        $response->assertSee('01/09/2026');
        $response->assertSee('05/10/2026');
        $response->assertSee('Limpiar Filtro');
    }

    public function test_dashboard_filter_correctly_counts_shares_and_registrations_in_interval(): void
    {
        $user = User::factory()->create();

        $course = Course::create([
            'title' => 'Curso de Marketing Digital',
            'slug' => 'curso-marketing-digital',
            'description' => 'Curso para prueba de filtros',
            'duration' => 30,
            'capacity' => 20,
            'status' => CourseStatus::OPEN,
        ]);

        // Inscripción DENTRO del rango (hace 5 días)
        $regInside = CourseRegistration::create([
            'course_id' => $course->id,
            'full_name' => 'Ana Gomez',
            'email' => 'ana@example.com',
            'phone' => '+297 594 3344',
            'status' => CourseRegistrationStatus::ENROLLED,
            'verification_token' => Str::random(40),
            'verified_at' => now()->subDays(5),
        ]);
        $regInside->created_at = now()->subDays(5);
        $regInside->save();

        // Inscripción FUERA del rango (hace 50 días)
        $regOutside = CourseRegistration::create([
            'course_id' => $course->id,
            'full_name' => 'Pedro Perez',
            'email' => 'pedro@example.com',
            'phone' => '+297 594 5566',
            'status' => CourseRegistrationStatus::ENROLLED,
            'verification_token' => Str::random(40),
            'verified_at' => now()->subDays(50),
        ]);
        $regOutside->created_at = now()->subDays(50);
        $regOutside->save();

        // Compartido DENTRO del rango (hace 3 días)
        $shareInside = SocialShare::create([
            'shareable_type' => Course::class,
            'shareable_id' => $course->id,
            'platform' => 'whatsapp',
        ]);
        $shareInside->created_at = now()->subDays(3);
        $shareInside->save();

        // Compartido FUERA del rango (hace 40 días)
        $shareOutside = SocialShare::create([
            'shareable_type' => Course::class,
            'shareable_id' => $course->id,
            'platform' => 'facebook',
        ]);
        $shareOutside->created_at = now()->subDays(40);
        $shareOutside->save();

        // Filtrar por los últimos 7 días
        $response = $this->actingAs($user)->get(route('dashboard', ['period' => '7_days']));

        $response->assertOk();
        // El total de solicitudes en los últimos 7 días debe ser 1 (Ana), no 2 (Pedro fue hace 50 días)
        $response->assertSee('1 solicitudes');
        $response->assertSee('Curso de Marketing Digital');
    }

    public function test_dashboard_only_measures_and_displays_active_social_networks(): void
    {
        $user = User::factory()->create();

        // Configurar solo Facebook y WhatsApp como activos
        ContactSetting::create([
            'email' => 'info@fudounar.org',
            'phone' => '+297 123 4567',
            'address' => 'Oranjestad, Aruba',
            'is_facebook_active' => true,
            'is_whatsapp_active' => true,
            'is_twitter_active' => false,
            'is_linkedin_active' => false,
        ]);

        $post = Post::create([
            'title' => 'Blog con metricas de redes',
            'slug' => 'blog-metricas-redes',
            'content' => 'Contenido',
            'status' => PostStatus::PUBLISHED,
            'views' => 100,
            'published_at' => now(),
        ]);

        // Crear compartidos en las 4 plataformas
        SocialShare::create([
            'shareable_type' => Post::class,
            'shareable_id' => $post->id,
            'platform' => 'facebook',
        ]);
        SocialShare::create([
            'shareable_type' => Post::class,
            'shareable_id' => $post->id,
            'platform' => 'whatsapp',
        ]);
        SocialShare::create([
            'shareable_type' => Post::class,
            'shareable_id' => $post->id,
            'platform' => 'twitter', // Inactiva
        ]);
        SocialShare::create([
            'shareable_type' => Post::class,
            'shareable_id' => $post->id,
            'platform' => 'linkedin', // Inactiva
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        // Las activas deben tener sus badges
        $response->assertSee('FB');
        $response->assertSee('WA');
        // Las inactivas NO deben mostrar sus badges
        $response->assertDontSee('>X</span>', false);
        $response->assertDontSee('>In</span>', false);
    }

    public function test_dashboard_handles_all_social_networks_inactive(): void
    {
        $user = User::factory()->create();

        ContactSetting::create([
            'email' => 'info@fudounar.org',
            'phone' => '+297 123 4567',
            'address' => 'Oranjestad, Aruba',
            'is_facebook_active' => false,
            'is_whatsapp_active' => false,
            'is_twitter_active' => false,
            'is_linkedin_active' => false,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Sin redes sociales activas');
    }
}
