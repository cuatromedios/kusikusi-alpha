<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\Models\Entity;

$factory->define(Entity::class, function (Faker $faker) {
    $langs = config('cms.langs', ['en_US']);
    $titles = [];
    $summaries = [];
    $bodies = [];
    $slugs = [];
    foreach ($langs as $lang) {
        $personProviderClass = "\\Faker\\Provider\\{$lang}\\Person";
        $companyProviderClass = "\\Faker\\Provider\\{$lang}\\Company";
        $textProviderClass = "\\Faker\\Provider\\{$lang}\\Text";
        $faker->addProvider(new $personProviderClass($faker));
        $faker->addProvider(new  $companyProviderClass($faker));
        $faker->addProvider(new  $textProviderClass($faker));
        $titles[$lang] = $faker->name;
        $summaries[$lang] = $faker->jobTitle;
        $bodies[$lang] = $faker->text(100);
        $slugs[$lang] = Str::slug($faker->name);
    }
    return [
        "model" => "entity",
        "parent_entity_id" => null,
        "content" => [
            "title" => $titles,
            "summary" => $summaries,
            "body" => $bodies,
            "slug" => $slugs
        ]
    ];
});
$factory->state(Entity::class, 'medium', function (Faker $faker) {
    $title = $faker->lastName;
    echo "Downloading image...";
    $image = $faker->image('storage/media', rand(320,640),rand(320,640), 'nature', false);
    echo " done.\n";
    return [
        "model" => "entity",
        "parent_entity_id" => null,
        "content" => [
            "title" => [ "en" => $title ],
            "format" => "jpg",
            "slug" => [ "en" => Str::slug($title)],
            "path" =>  $image
        ]
    ];
});
