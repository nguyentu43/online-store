<?php

use Faker\Generator as Faker;

$factory->define(App\Product::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'weight' => '20x40',
        'description' => $faker->text
    ];
});
