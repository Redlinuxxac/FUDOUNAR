<?php

namespace Database\Factories;

use App\Enums\CourseRegistrationStatus;
use App\Models\Course;
use App\Models\CourseRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CourseRegistration>
 */
class CourseRegistrationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'full_name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'status' => CourseRegistrationStatus::PENDING,
            'verification_token' => Str::random(64),
            'verified_at' => null,
            'expires_at' => null,
            'enrolled_at' => null,
            'admin_notes' => null,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CourseRegistrationStatus::VERIFIED,
            'verified_at' => now(),
            'expires_at' => now()->addDays(5),
        ]);
    }

    public function enrolled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CourseRegistrationStatus::ENROLLED,
            'verified_at' => now()->subDays(2),
            'expires_at' => now()->addDays(3),
            'enrolled_at' => now(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CourseRegistrationStatus::EXPIRED,
            'verified_at' => now()->subDays(10),
            'expires_at' => now()->subDays(5),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CourseRegistrationStatus::CANCELLED,
        ]);
    }
}
