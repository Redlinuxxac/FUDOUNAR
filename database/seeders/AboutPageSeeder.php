<?php

namespace Database\Seeders;

use App\Models\AboutPage;
use Illuminate\Database\Seeder;

class AboutPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AboutPage::create([
            'mission_text' => 'Nuestra misión es mejorar la calidad de vida de las comunidades marginadas a través de la educación, la salud y el desarrollo sostenible.',
            'mission_image' => 'https://fudounar.org/img/Mision.jpeg',
            'vision_text' => 'Vislumbramos un mundo donde todas las personas tengan acceso a oportunidades equitativas y puedan alcanzar su máximo potencial.',
            'vision_image' => 'https://fudounar.org/img/Vision.jpeg',
            'values_text' => 'Integridad, solidaridad, excelencia, respeto, innovación.',
            'values_image' => 'https://fudounar.org/img/Valores.jpeg',
            'history_text' => 'Breve relato de cómo surgió la fundación, sus hitos más importantes y su evolución a lo largo del tiempo. Incluir anécdotas o testimonios que humanicen la historia y conecten con el lector.',
            'team_text' => 'Presentaciones breves de los miembros clave del equipo, destacando sus roles, experiencia y pasiones. Fotos de los miembros del equipo para crear un ambiente más cercano.',
            'team_image' => 'https://fudounar.org/img/imagen_equipo.jpg',
            'impact_text' => '<ul><li>Descripción detallada de los programas y proyectos.</li><li>Utilizar imágenes y gráficos para ilustrar resultados.</li><li>Destacar los beneficios generados en las comunidades.</li></ul>',
            'achievements_text' => '<ul><li>Lista de los principales logros alcanzados.</li><li>Utilizar métricas y datos cuantitativos.</li><li>Testimonios de beneficiarios e impacto real.</li></ul>',
            'why_donate_text' => 'Explicar por qué es importante apoyar a la fundación. Destacar el impacto que cada donación puede generar. Facilitar el proceso de donación con botones o enlaces claros.',
        ]);
    }
}
