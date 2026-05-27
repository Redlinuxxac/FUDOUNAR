<?php

namespace Database\Factories;

use App\Enums\ActivityStatus;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'image' => 'https://image.pollinations.ai/prompt/humanitarian-activity-realistic?width=800&height=600&seed='.$this->faker->numberBetween(1, 1000),
            'status' => $this->faker->randomElement(ActivityStatus::cases()),
            'started_at' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
        ];
    }

    /**
     * Indicate that the activity is upcoming.
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ActivityStatus::UPCOMING,
            'started_at' => now()->addDays(5),
        ]);
    }

    /**
     * Indicate that the activity is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ActivityStatus::ACTIVE,
            'started_at' => now()->subDays(1),
        ]);
    }
}
