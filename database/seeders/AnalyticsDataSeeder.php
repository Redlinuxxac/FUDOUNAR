<?php

namespace Database\Seeders;

use App\Enums\CourseRegistrationStatus;
use App\Models\Activity;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Post;
use App\Models\SocialShare;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AnalyticsDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Actividades: Asignar vistas iniciales
        $activities = Activity::orderBy('id')->get();
        $activityViews = [1420, 1180, 950, 780, 620, 490, 380, 290, 210, 150];
        foreach ($activities as $index => $activity) {
            $activity->update([
                'views' => $activityViews[$index] ?? rand(100, 300),
            ]);
        }

        // 2. Posts (Nuestra Voz): Asignar vistas iniciales
        $posts = Post::orderBy('id')->get();
        $postViews = [2450, 1920, 1740, 1510, 1320, 1150, 980, 850, 720, 610];
        foreach ($posts as $index => $post) {
            $post->update([
                'views' => $postViews[$index] ?? rand(200, 500),
            ]);
        }

        // 3. Cursos: Asignar vistas e inscripciones/solicitudes
        $courses = Course::orderBy('id')->get();
        $courseViews = [1850, 1620, 1400, 1210, 980, 840, 690, 520, 410, 300];
        $courseRegistrationsDistribution = [22, 18, 15, 12, 10, 8, 6, 5, 4, 2];

        foreach ($courses as $index => $course) {
            $course->update([
                'views' => $courseViews[$index] ?? rand(150, 400),
            ]);

            // Generar solicitudes reales de cursos si aún no tiene suficientes
            $currentRegistrationsCount = $course->registrations()->count();
            $targetCount = $courseRegistrationsDistribution[$index] ?? 2;
            $needed = max(0, $targetCount - $currentRegistrationsCount);

            for ($i = 1; $i <= $needed; $i++) {
                CourseRegistration::create([
                    'course_id' => $course->id,
                    'full_name' => 'Estudiante '.($currentRegistrationsCount + $i)." - Curso {$course->id}",
                    'email' => "estudiante{$course->id}_{$i}@fudounar-aruba.org",
                    'phone' => '+297 '.rand(500, 599).' '.rand(1000, 9999),
                    'status' => $i % 2 === 0 ? CourseRegistrationStatus::ENROLLED : CourseRegistrationStatus::VERIFIED,
                    'verification_token' => Str::random(40),
                    'verified_at' => now(),
                    'expires_at' => now()->addDays(5),
                ]);
            }
        }

        // 4. Compartidos en Redes Sociales (SocialShare)
        // Distribución realista entre plataformas
        $platforms = ['facebook', 'twitter', 'linkedin', 'whatsapp'];

        // Solo generar si no hay compartidos registrados previamente
        if (SocialShare::count() === 0) {
            // Actividades: ~450 compartidos
            $this->seedSharesForCollection($activities, 450, [
                'facebook' => 150,
                'twitter' => 110,
                'linkedin' => 80,
                'whatsapp' => 110,
            ]);

            // Blogs: ~384 compartidos
            $this->seedSharesForCollection($posts, 384, [
                'facebook' => 130,
                'twitter' => 100,
                'linkedin' => 64,
                'whatsapp' => 90,
            ]);

            // Cursos: ~400 compartidos
            $this->seedSharesForCollection($courses, 400, [
                'facebook' => 140,
                'twitter' => 100,
                'linkedin' => 70,
                'whatsapp' => 90,
            ]);
        }
    }

    /**
     * Crear registros de compartidos proporcionales para una colección de modelos.
     */
    protected function seedSharesForCollection($items, int $total, array $platformQuota): void
    {
        if ($items->isEmpty()) {
            return;
        }

        foreach ($platformQuota as $platform => $count) {
            for ($i = 0; $i < $count; $i++) {
                // Mayor probabilidad para los primeros elementos (top)
                $itemIndex = min(count($items) - 1, (int) floor(abs(sin($i)) * count($items)));
                $item = $items[$itemIndex];

                SocialShare::create([
                    'shareable_type' => get_class($item),
                    'shareable_id' => $item->id,
                    'platform' => $platform,
                    'ip_address' => '127.0.0.1',
                    'created_at' => now()->subDays(rand(0, 25))->subHours(rand(0, 23)),
                ]);
            }
        }
    }
}
