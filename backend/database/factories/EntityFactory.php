<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Models\Entity::class, function (Faker $faker) {
    $title = $faker->lastName;
    return [
        "model" => "entity",
        "parent_entity_id" => null,
        "content" => [
            "title" => $title,
            "summary" => $faker->paragraph,
            "body" => $faker->paragraph(3),
            "url" => "/".\Illuminate\Support\Str::slug($title)
        ]
    ];
});
