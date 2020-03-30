<?php

use Illuminate\Database\Seeder;
use App\Models\Entity;

class SampleSiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sections_count = 3;
        $subsections_count = 2;
        $pages_count = 3;
        $media_count = 3;
        $home = Entity::where('model', 'home')->first();
        for ($s = 0; $s < $sections_count; $s++) {
            $section = new Entity([
                "model" => "section",
                "parent_entity_id" => $home->id
            ]);
            $section->save();
            for ($p = 0; $p < $pages_count; $p++) {
                $page = new Entity([
                    "model" => "page",
                    "parent_entity_id" => $section->id,
                    "content" => json_encode([
                        "en" => [
                            "title" => "Page " . $p,
                            "url" => "/page-" . $p
                        ]
                    ])
                ]);
                $page->save();
                for ($m = 0; $m < $media_count; $m++) {
                    $media = Entity::where('model', 'media')->first();
                    $medium = new Entity([
                        "model" => "medium",
                        "parent_entity_id" => $media->id
                    ]);
                    $medium->save();
                }
            }
            for ($b = 0; $b < $subsections_count; $b++) {
                $subsection = new Entity([
                    "model" => "section",
                    "parent_entity_id" => $section->id
                ]);
                $subsection->save();
                for ($p = 0; $p < $pages_count; $p++) {
                    $page = new Entity([
                        "model" => "page",
                        "parent_entity_id" => $subsection->id
                    ]);
                    $page->save();
                }
            }
        }
    }
}
