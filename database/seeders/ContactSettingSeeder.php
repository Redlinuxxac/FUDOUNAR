<?php

namespace Database\Seeders;

use App\Models\ContactSetting;
use Illuminate\Database\Seeder;

class ContactSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ContactSetting::create([
            'email' => 'contacto@fudounar.org',
            'phone' => '+1 809 000 0000',
            'address' => 'Santo Domingo, República Dominicana',
            'google_maps_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d121059.04710156006!2d-69.9885966!3d18.4718533!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8eaf89f110f28b5d%3A0x1d58223d6a457d54!2sSanto%20Domingo!5e0!3m2!1ses-419!2sdo!4v1716681600000!5m2!1ses-419!2sdo',
        ]);
    }
}
