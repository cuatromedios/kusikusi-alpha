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
            "model" => "root"
        ]);
        $root->save();
        $home = new Entity([
            "model" => "home",
            "parent_entity_id" => $root->id,
            "content" => json_encode([
                "en" => [
                    "title" => "Home",
                    "url" => "/"
                ]
            ])
        ]);
        $home->save();
        $collections = new Entity([
            "model" => "collections",
            "parent_entity_id" => $root->id
        ]);
        $collections->save();
        $users = new Entity([
            "model" => "users",
            "parent_entity_id" => $root->id
        ]);
        $users->save();
        $adminUser = new Entity([
            "model" => "user",
            "parent_entity_id" => $users->id
        ]);
        $adminUser->save();
        $media = new Entity([
            "model" => "media",
            "parent_entity_id" => $root->id
        ]);
        $media->save();

    }
}
