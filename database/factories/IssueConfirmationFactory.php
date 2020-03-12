<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use App\Report;
use App\IssueConfirmation;
use Faker\Generator as Faker;

$factory->define(IssueConfirmation::class, function (Faker $faker) {
    return [
        'report_id' => factory(Report::class)->create()->id, 
        'reporter_id' => factory(User::class)->create()->id, 
    ];
});
