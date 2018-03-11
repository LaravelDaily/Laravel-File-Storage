<?php

$factory->define(App\File::class, function (Faker\Generator $faker) {
    return [
        "uuid" => $faker->name,
        "folder_id" => factory('App\Folder')->create(),
        "created_by_id" => factory('App\User')->create(),
    ];
});
