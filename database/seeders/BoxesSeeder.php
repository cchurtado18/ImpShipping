<?php

namespace Database\Seeders;

use App\Models\Box;
use Illuminate\Database\Seeder;

class BoxesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $boxes = [
            [
                'code' => 'S',
                'length_in' => 12.0,
                'width_in' => 10.0,
                'height_in' => 8.0,
                'base_price_usd' => 25.00,
                'active' => true,
            ],
            [
                'code' => 'M',
                'length_in' => 16.0,
                'width_in' => 12.0,
                'height_in' => 10.0,
                'base_price_usd' => 35.00,
                'active' => true,
            ],
            [
                'code' => 'L',
                'length_in' => 20.0,
                'width_in' => 16.0,
                'height_in' => 12.0,
                'base_price_usd' => 45.00,
                'active' => true,
            ],
            [
                'code' => 'XL',
                'length_in' => 24.0,
                'width_in' => 20.0,
                'height_in' => 16.0,
                'base_price_usd' => 60.00,
                'active' => true,
            ],
        ];

        foreach ($boxes as $box) {
            Box::updateOrCreate(['code' => $box['code']], $box);
        }
    }
}
