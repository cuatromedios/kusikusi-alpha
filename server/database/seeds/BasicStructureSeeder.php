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
            "properties" => [],
            "contents"=> [
                "title" => $titles,
                "welcome" => $welcomes,
                "slug" => $slugs,
            ]
        ]);
        $home->save();

        $collections = new Entity([
            "model" => "collections",
            "short_id" => "collections",
        ]);
        $collections->save();
        $media = new Entity([
            "model" => "media",
            "short_id" => "media",
        ]);
        $media->save();

        $user_name = "Administrator";
        $user_email = "admin@example.com";
        $user_profile = "admin";
        print("*** Generated user:\n");
        print("{\n");
        print("  \"email\": \"{$user_email}\",\n");
        $user = factory(App\Models\User::class)->make([
            "name" => $user_name,
            "email" => $user_email,
            "profile" => $user_profile
        ]);
        print("}\n");

        $user->save();

    }
}
