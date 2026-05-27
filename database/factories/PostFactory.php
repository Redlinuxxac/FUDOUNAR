<?php

namespace Database\Factories;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence();

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $this->faker->paragraphs(3, true),
            'image' => 'https://image.pollinations.ai/prompt/blog-news-humanitarian?width=800&height=600&seed='.$this->faker->numberBetween(1, 1000),
            'status' => $this->faker->randomElement(PostStatus::cases()),
            'published_at' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
        ];
    }

    /**
     * Indicate that the post is scheduled.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::UPCOMING,
            'published_at' => now()->addDays(2),
        ]);
    }

    /**
     * Indicate that the post is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::PUBLISHED,
            'published_at' => now()->subDays(1),
        ]);
    }
}
