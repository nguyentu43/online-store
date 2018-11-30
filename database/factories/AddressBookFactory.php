<?php

use Faker\Generator as Faker;

$factory->define(App\AddressBook::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'address' => $faker->address,
        'city' => $faker->city,
        'phone' => '0901234567'
    ];
});
