<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'cutoff_day' => '12',
            'departure_day' => '13',
            'default_times' => json_encode([
                'collection' => '08:00',
                'cutoff' => '18:00',
                'departure' => '09:00',
            ]),
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
