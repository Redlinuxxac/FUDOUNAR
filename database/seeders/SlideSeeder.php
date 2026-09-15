<?php

namespace Database\Seeders;

use App\Models\Slide;
use Illuminate\Database\Seeder;

class SlideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $slides = [
            [
                'title' => 'Uniendo Culturas',
                'subtitle' => 'República Dominicana y Aruba trabajando juntas.',
                'image' => '/slider/slide1.jpg',
                'button_text' => 'Saber más',
                'button_link' => '/quienes-somos',
                'button_secondary_text' => 'Contáctanos',
                'button_secondary_link' => '/contacto',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Educación para Todos',
                'subtitle' => 'Programas de alfabetización digital transforman vidas.',
                'image' => '/slider/slide2.jpg',
                'button_text' => 'Saber más',
                'button_link' => '/quienes-somos',
                'button_secondary_text' => 'Contáctanos',
                'button_secondary_link' => '/contacto',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Voluntariado con Corazón',
                'subtitle' => 'Sé parte del cambio. Únete hoy mismo.',
                'image' => '/slider/slide3.jpg',
                'button_text' => 'Saber más',
                'button_link' => '/quienes-somos',
                'button_secondary_text' => 'Contáctanos',
                'button_secondary_link' => '/contacto',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Salud Infantil',
                'subtitle' => 'Jornadas médicas para los más pequeños.',
                'image' => '/slider/slide4.jpg',
                'button_text' => 'Saber más',
                'button_link' => '/quienes-somos',
                'button_secondary_text' => 'Contáctanos',
                'button_secondary_link' => '/contacto',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Capacitación Continua',
                'subtitle' => 'Cursos técnicos para el crecimiento profesional.',
                'image' => '/slider/slide5.jpg',
                'button_text' => 'Saber más',
                'button_link' => '/quienes-somos',
                'button_secondary_text' => 'Contáctanos',
                'button_secondary_link' => '/contacto',
                'order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($slides as $slide) {
            Slide::create($slide);
        }
    }
}
