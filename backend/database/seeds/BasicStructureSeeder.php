<?php

use Illuminate\Database\Seeder;
use App\Models\Entity;

class BasicStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $root = new Entity([
            "model" => "root",
            "short_id" => "root"
        ]);
        $root->save();

        $langs = config('cms.langs', ['en_US']);
        $titles = [];
        $welcomes = [];
        $slugs = [];
        foreach ($langs as $lang) {
            $titles[$lang] = "Kusikusi Website in " . $lang;
            $welcomes[$lang] = "Welcome in " . $lang;
            $slugs[$lang] = $lang === $langs[0] ? "" : $lang;
        }
        $home = new Entity([
            "model" => "home",
            "short_id" => "home",
            "parent_entity_id" => $root->id,
            "content" => [
                "title" => $titles,
                'welcome' => $welcomes,
                "slug" => $slugs,
            ]
        ]);
        $home->save();
        $collections = new Entity([
            "model" => "collections",
            "short_id" => "collections",
            "parent_entity_id" => $root->id
        ]);
        $collections->save();
        $media = new Entity([
            "model" => "media",
            "short_id" => "media",
            "parent_entity_id" => $root->id
        ]);
        $media->save();

    }
}
