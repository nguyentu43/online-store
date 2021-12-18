<?php

use Faker\Generator as Faker;

$factory->define(App\Product::class, function (Faker $faker) {
    return [
        'name' => 'Product '. random_int(0, 100),
        'weight' => '20x40',
        'description' => $faker->text,
        'enable' => true
    ];
});
