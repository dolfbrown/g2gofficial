<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(App\Wishlist::class, function (Faker $faker) {
    $products   = \DB::table('products')->pluck('id')->toArray();
    $time = Carbon::Now()->subDays(rand(0, 15));
    return [
        'customer_id' => 1,
        'product_id' => $products[array_rand($products)],
        'created_at' => $time,
        'updated_at' => $time,
    ];
});
