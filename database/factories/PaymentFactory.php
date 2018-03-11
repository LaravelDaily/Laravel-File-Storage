<?php

$factory->define(App\Payment::class, function (Faker\Generator $faker) {
    return [
        "user_id" => factory('App\User')->create(),
        "role_id" => factory('App\Role')->create(),
        "payment_amount" => $faker->randomNumber(2),
    ];
});
