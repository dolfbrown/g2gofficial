<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'role_id' => $faker->randomElement(\DB::table('roles')->whereNotIn('id', [1,2])->pluck('id')->toArray()),
    	'nice_name' => $faker->lastName,
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(123456),
        'dob' => $faker->date,
        'sex' => $faker->randomElement(['app.male', 'app.female']),
        'description' => $faker->text(500),
        'active' => $faker->boolean,
        'remember_token' => str_random(10),
        'created_at' => Carbon::Now()->subDays(rand(0, 15)),
        'updated_at' => Carbon::Now()->subDays(rand(0, 15)),
    ];
});
