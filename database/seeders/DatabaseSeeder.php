<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AboutPageSeeder::class);
        $this->call(ActivitySeeder::class);
        $this->call(AiPostSeeder::class);
        $this->call(ContactSettingSeeder::class);
        $this->call(CourseSeeder::class);
        $this->call(SlideSeeder::class);
        $this->call(PostSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(RolesAndPermissionsSeeder::class);
    }
}
