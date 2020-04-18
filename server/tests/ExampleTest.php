<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Entity;

class ExampleTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $testEntity = [
            "model" => "root",
            "short_id" => "root",
            "view" => "root"
        ];
        $root = new Entity($testEntity);
        $root->save();
        $this->seeInDatabase('entities', $testEntity);
    }
}
