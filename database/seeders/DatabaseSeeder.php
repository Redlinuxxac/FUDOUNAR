<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Edwin Rosario',
            'email' => 'RosarioEdwinAC@gmail.com',
            'password' => static::$password ??= Hash::make('PPsae5938'),
        ]);

        User::factory()->create([
            'name' => 'Kendry Rosario',
            'email' => 'kendry.rosario@fudounar.org',
            'password' => static::$password ??= Hash::make('Mybebe25'),
        ]);

        $this->call(RolesAndPermissionsSeeder::class);
    }
}
