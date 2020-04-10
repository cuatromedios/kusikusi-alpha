<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Models\Entity::class, function (Faker $faker) {
    $title = $faker->sentence;
    return [
        "model" => "entity",
        "parent_entity_id" => null,
        "content" => [
            "title" => $title,
            "summary" => $faker->paragraph,
            "body" => $faker->paragraph(3),
            "slug" => \Illuminate\Support\Str::slug($title)
        ]
    ];
});
$factory->state(\App\Models\Entity::class, 'medium', function (Faker $faker) {
    $title = $faker->lastName;
    $image = $faker->image('storage/media', rand(320,640),rand(320,640), 'nature', false);
    return [
        "model" => "entity",
        "parent_entity_id" => null,
        "content" => [
            "title" => $title,
            "format" => "jpg",
            "slug" => \Illuminate\Support\Str::slug($title),
            "path" =>  $image
        ]
    ];
});
