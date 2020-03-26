<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Report;
use Faker\Generator as Faker;
use Illuminate\Http\UploadedFile;

$factory->define(Report::class, function (Faker $faker) {
    return [
        'reporter_id' => 1,
        'confirmed' => false,
        'fixed' => false,
        'description' => $faker->sentence,
        'picture' => 'https://via.placeholder.com/150',
        'lat' => $faker->latitude($min = 31.600842, $max = 31.6056320), // make it arround bechar at least
        'lng' => $faker->longitude($min = -2.229613, $max =-2.220613),
    ];
});

$factory->state(Report::class, 'confirmed', function (Faker $faker) {
    return [
        'reporter_id' => 1,
        'confirmed' => true,
        'fixed' => false,
        'description' => $faker->sentence,
        'picture' => 'https://via.placeholder.com/150',
        'lat' => $faker->latitude($min = 31.600842, $max = 31.6056320), // make it arround bechar at least
        'lng' => $faker->longitude($min = -2.229613, $max =-2.220613),
    ];
});

$factory->state(Report::class, 'fixed', function (Faker $faker) {
    return [
        'reporter_id' => 1,
        'confirmed' => false,
        'fixed' => true,
        'description' => $faker->sentence,
        'picture' => 'https://via.placeholder.com/150',
        'lat' => $faker->latitude($min = 31.600842613762393, $max = 31.60563201335843), // make it arround bechar at least
        'lng' => $faker->longitude($min = -2.229613065719605, $max =-2.220613065719705),
    ];
});


