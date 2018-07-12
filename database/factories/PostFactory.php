<?php

use Faker\Generator as Faker;

$factory->define(App\Post::class, function (Faker $faker) {
    return [
        'user_id' => auth()->user()->id ?? factory(\App\User::class)->create()->id,
        'title' => $faker->sentence(3, true),
        'content' => $faker->realText(),
    ];
});
