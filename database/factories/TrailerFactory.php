<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\Trailer\Trailer::class, function (Faker $faker) {
    return [
        'license_plate_number' => '粤B' . $faker->randomNumber(4) . '挂',
        'brand'                => $faker->randomElement(['豪沃', '重汽', '沃尔沃', '东风']),
        'engine_number'        => $faker->randomDigit,
        'axle_number'          => $faker->numberBetween(2, 6),
        'owner_name'           => $faker->randomNumber(5),
    ];
});
