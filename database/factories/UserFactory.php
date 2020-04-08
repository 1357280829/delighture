<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

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


$factory->define(User::class, function (Faker $faker) {
    return [
        'account' => Str::random(10),
        'nickname' => $faker->unique()->name,
        'phone' => $faker->unique()->numberBetween(10000000000, 20000000000),
        'email' => $faker->unique()->safeEmail,
        'last_login_at' => $faker->dateTimeBetween('-2 years', '-1 years'),
    ];
});
