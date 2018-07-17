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

$factory->define(App\Models\Driver\Driver::class, function (Faker $faker) {
    return [
        'name'                => $faker->randomNumber(4),
        'job_number'          => $faker->randomNumber(4),
        'driver_license_type' => 'A' . $faker->randomNumber(1),
    ];
});
