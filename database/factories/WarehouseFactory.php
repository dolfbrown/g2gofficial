<?php

use Faker\Generator as Faker;

$factory->define(App\Warehouse::class, function (Faker $faker) {
    return [
        'incharge' => $faker->randomElement(\DB::table('users')->pluck('id')->toArray()),
        'name' => $faker->company,
        'email' => $faker->email,
        'description' => $faker->text(500),
        'active' => 1,
    ];
});
