<?php

namespace Database\Seeders;

use App\Models\Offer;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    public function run(): void
    {
        $offers = [
            [
                'name'                 => 'Happy Hour',
                'description'          => '20% off all orders during happy hour.',
                'type'                 => 'percentage',
                'value'                => 20,
                'min_order_amount'     => null,
                'max_discount_amount'  => 50,
                'is_active'            => true,
                'starts_at'            => now()->startOfDay(),
                'ends_at'              => now()->addMonths(3)->endOfDay(),
                'applicable_days'      => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                'applicable_from_time' => '15:00',
                'applicable_to_time'   => '18:00',
            ],
            [
                'name'                 => 'Weekend Flat Discount',
                'description'          => 'Flat 5.00 off on weekend orders above 30.00.',
                'type'                 => 'fixed',
                'value'                => 5,
                'min_order_amount'     => 30,
                'max_discount_amount'  => null,
                'is_active'            => true,
                'starts_at'            => now()->startOfDay(),
                'ends_at'              => now()->addMonths(6)->endOfDay(),
                'applicable_days'      => ['saturday', 'sunday'],
                'applicable_from_time' => null,
                'applicable_to_time'   => null,
            ],
            [
                'name'                 => 'Grand Opening 15%',
                'description'          => 'Celebrate our opening with 15% off everything.',
                'type'                 => 'percentage',
                'value'                => 15,
                'min_order_amount'     => null,
                'max_discount_amount'  => null,
                'is_active'            => false,
                'starts_at'            => now()->subMonth(),
                'ends_at'              => now()->subDay(),
                'applicable_days'      => null,
                'applicable_from_time' => null,
                'applicable_to_time'   => null,
            ],
            [
                'name'                 => 'Lunch Special',
                'description'          => '10% off during lunch hours, Mon–Fri.',
                'type'                 => 'percentage',
                'value'                => 10,
                'min_order_amount'     => 15,
                'max_discount_amount'  => 20,
                'is_active'            => true,
                'starts_at'            => now()->startOfDay(),
                'ends_at'              => now()->addYear()->endOfDay(),
                'applicable_days'      => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                'applicable_from_time' => '12:00',
                'applicable_to_time'   => '14:30',
            ],
        ];

        foreach ($offers as $offer) {
            Offer::updateOrCreate(['name' => $offer['name']], $offer);
        }
    }
}
