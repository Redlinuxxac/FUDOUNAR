<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            AboutPageSeeder::class,
            ContactSettingSeeder::class,
            UserSeeder::class,
            RolesAndPermissionsSeeder::class,
        ]);
    }
}
