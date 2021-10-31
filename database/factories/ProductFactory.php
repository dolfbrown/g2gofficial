<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(App\Product::class, function (Faker $faker) {
    $num = $faker->randomFloat($nbMaxDecimals = NULL, $min = 100, $max = 400);

    return [
        // 'manufacturer_id' => $faker->randomElement(\DB::table('manufacturers')->pluck('id')->toArray()),
        'brand' => $faker->word,
        'title' => $faker->sentence,
        'model_number' => $faker->word .' '.$faker->bothify('??###'),
        'mpn' => $faker->randomNumber(),
        'gtin' => $faker->ean13,
        'gtin_type' => $faker->randomElement(\DB::table('gtin_types')->pluck('name')->toArray()),
        'description' => $faker->text(1500),
        'origin_country' => $faker->randomElement(\DB::table('countries')->pluck('id')->toArray()),
        'slug' => $faker->slug,
        'sku' => $faker->word,
        'condition' => $faker->randomElement(['New','Used','Refurbished']),
        'condition_note' => $faker->realText,
        'key_features' => [$faker->sentence, $faker->sentence, $faker->sentence, $faker->sentence, $faker->sentence, $faker->sentence, $faker->sentence],
        'stock_quantity' => rand(9,99),
        'damaged_quantity' => 0,
        'supplier_id' => $faker->randomElement(\DB::table('suppliers')->pluck('id')->toArray()),
        'user_id' => 1,
        'purchase_price' => $num,
        'price' => $num+rand(50, 200),
        'offer_price' => rand(1, 0) ? $num+rand(1, 49) : Null,
        'offer_start' => Carbon::Now()->format('Y-m-d h:i a'),
        'offer_end' => date('Y-m-d h:i a', strtotime(rand(3, 22) . ' days')),
        'min_order_quantity' => 1,
        'shipping_weight' => rand(100,1999),
        'free_shipping' => $faker->boolean,
        'linked_items' => array_rand(range(1,50), rand(2,3)),
        'available_from' => Carbon::Now()->subDays(rand(1, 3))->format('Y-m-d h:i a'),
        'meta_title' => $faker->sentence,
        'meta_description' => $faker->realText,
        'stuff_pick' => $faker->boolean,
    	'sale_count' => $faker->randomDigit,
        'active' => 1,
        'created_at' => Carbon::Now()->subDays(rand(0, 15)),
        'updated_at' => Carbon::Now()->subDays(rand(0, 15)),
    ];
});
