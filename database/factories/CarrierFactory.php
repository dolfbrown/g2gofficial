<?php

use Faker\Generator as Faker;

$factory->define(App\Carrier::class, function (Faker $faker) {
    return [
        'tax_id' => $faker->randomElement(\DB::table('taxes')->pluck('id')->toArray()),
        'name' => $faker->company,
        'email' => $faker->email,
        'phone' => $faker->phoneNumber,
        'tracking_url' => $faker->url.'/@',
        'active' => 1,
    ];
});
