<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Specific states
        Course::factory()->count(2)->scheduled()->create();
        Course::factory()->count(3)->open()->create();

        // Random states
        Course::factory()->count(5)->create();
    }
}
