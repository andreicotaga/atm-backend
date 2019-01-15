<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Cards::class, function (Faker\Generator $faker) {
    return [
        'card_number' => $faker->unique()->creditCardNumber,
        'password'    => password_hash('1234', PASSWORD_BCRYPT),
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'balance' => $faker->numberBetween(120, 400),
        'blocked' => $faker->boolean
    ];
});
