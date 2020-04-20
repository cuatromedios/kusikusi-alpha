<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Entity;

class EntityModelTest extends TestCase
{
    //use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateEntity()
    {
        $root = new Entity([
           "model" => "root",
           "id" => "root"
        ]);
        $root->save();
        $this->seeInDatabase('entities', [
            "id" => "root"
        ]);
    }  

    public function testEditEntity()
    {
        $edit = Entity::where('id',"root")->update(["id" => "home"]);
        $this->assertTrue(true);
        $this->seeInDatabase('entities', [
            "id" => "home"
        ]);
    } 

     public function testDeleteEntity()
    {
        $delete = Entity::where('id', 'home')->delete();
        $this->assertTrue(true);
    } 
}
