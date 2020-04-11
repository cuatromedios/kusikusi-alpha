<?php

use Illuminate\Database\Seeder;
use App\Models\Entity;
use Faker\Generator as Faker;

class SampleSiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sections_count = 2;
        $pages_count = 2;
        $media_count = 0;

        $home = Entity::where('short_id', 'home')->first();
        for ($s = 0; $s < $sections_count; $s++) {
            $section = factory(App\Models\Entity::class)->make([
                "model" => "section",
                "parent_entity_id" => $home->id
            ]);
            $section->save();
            for ($p = 0; $p < $pages_count; $p++) {
                $page = factory(App\Models\Entity::class)->make([
                    "model" => "page",
                    "parent_entity_id" => $section->id
                ]);
                $page->save();
                $media = Entity::where('short_id', 'media')->first();
                for ($m = 0; $m < $media_count; $m++) {
                    $medium = factory(App\Models\Entity::class)->states('medium')->make([
                        "model" => "medium",
                        "parent_entity_id" => $media->id
                    ]);
                    $medium->save();
                    rename("storage/media/".$medium->properties['path'], "storage/media/".$medium->id.".jpg");
                    $page->addRelation([
                        "called_entity_id" => $medium->id,
                        "kind" => \App\models\EntityRelation::RELATION_MEDIA,
                        "tags" => $m == 0 ? ['icon'] : ['gallery'],
                        "position" => $m
                    ]);
                }
            }
        }
    }
}
