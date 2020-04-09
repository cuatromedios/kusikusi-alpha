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
        $home = new Entity([
            "model" => "home",
            "short_id" => "home",
            "parent_entity_id" => $root->id,
            "content" => [
                "title" => ["en" => "Home"],
                'welcome' => "Welcome to your new website",
                "url" => ["en" => "/", "es" => "/es"],
            ]
        ]);
        $home->save();
        $collections = new Entity([
            "model" => "collections",
            "short_id" => "collections",
            "parent_entity_id" => $root->id
        ]);
        $collections->save();
        $users = new Entity([
            "model" => "users",
            "short_id" => "users",
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
            "short_id" => "media",
            "parent_entity_id" => $root->id
        ]);
        $media->save();

    }
}
