<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Entity;

class ExampleTest extends TestCase
{
    // use DatabaseMigrations;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $root = new Entity([
           "model" => "root",
           "short_id" => "root"
        ]);
        $root->save();
        $this->seeInDatabase('entities', [
            "short_id" => "root"
        ]);
    }
}
