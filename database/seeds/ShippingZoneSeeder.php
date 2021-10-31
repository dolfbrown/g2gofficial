<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ShippingZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\ShippingZone::class)->create(
                            [
                                'name' => 'Worldwide',
                                'tax_id' => 1,
                                'country_ids' => [],
                                'state_ids' => [],
                                'rest_of_the_world' => true,
                                'created_at' => Carbon::Now(),
                                'updated_at' => Carbon::Now(),
                            ]
                        );

        factory(App\ShippingRate::class)->create([
            'shipping_zone_id' => 1,
            'name' => 'Standard shipping',
            // 'carrier_id' => rand(1,5),
            'delivery_takes' => '2-3 days',
            'based_on' => 'price',
            'minimum' => 0,
            'maximum' => Null,
            'rate' => 0,
        ]);
    }
}