<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Specific states
        Post::factory()->count(2)->scheduled()->create();
        Post::factory()->count(3)->published()->create();

        // Random states
        Post::factory()->count(5)->create();
    }
}
