<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create specific states
        Activity::factory()->count(2)->upcoming()->create();
        Activity::factory()->count(2)->active()->create();

        // Create random states
        Activity::factory()->count(6)->create();
    }
}
