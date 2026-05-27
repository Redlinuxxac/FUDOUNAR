<?php

namespace Database\Factories;

use App\Enums\CourseModality;
use App\Enums\CourseStatus;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraphs(2, true),
            'image' => 'https://image.pollinations.ai/prompt/educational-training-course-professional?width=800&height=600&seed='.$this->faker->numberBetween(1, 1000),
            'duration' => $this->faker->randomElement([20, 30, 40, 60]),
            'modality' => $this->faker->randomElement(CourseModality::cases()),
            'status' => $this->faker->randomElement(CourseStatus::cases()),
            'started_at' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
        ];
    }

    /**
     * Indicate that the course is scheduled.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CourseStatus::UPCOMING,
            'started_at' => now()->addDays(10),
        ]);
    }

    /**
     * Indicate that the course is open.
     */
    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CourseStatus::OPEN,
            'started_at' => now()->subDays(2),
        ]);
    }
}
