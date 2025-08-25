<?php

namespace Database\Seeders;

use App\Models\Box;
use App\Models\PriceList;
use Illuminate\Database\Seeder;

class PriceListsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentMonth = now()->format('Y-m');
        
        $boxes = Box::where('active', true)->get();
        
        foreach ($boxes as $box) {
            // Crear precio para el mes actual con un pequeño incremento
            $priceIncrease = 2.00; // $2 USD de incremento
            $newPrice = $box->base_price_usd + $priceIncrease;
            
            PriceList::updateOrCreate(
                [
                    'box_id' => $box->id,
                    'valid_from' => $currentMonth . '-01',
                ],
                [
                    'price_usd' => $newPrice,
                ]
            );
        }
    }
}
