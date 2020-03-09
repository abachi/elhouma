<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Report;
use Faker\Generator as Faker;

$factory->define(Report::class, function (Faker $faker) {
    return [
        'reporter_id' => 1,
        'description' => $faker->sentence,
        'picture' => 'https://via.placeholder.com/150',
        'lat' => $faker->latitude(),
        'lng' => $faker->longitude(),
    ];
});
