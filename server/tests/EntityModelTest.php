<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Entity;

class EntityModelTest extends TestCase
{
    use DatabaseMigrations;

    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateEntity()
    {
        $root = new Entity([
           "id" => "root",
           "model" => "root",
           "view" => "root",
           "parent_entity_id" => "root"
        ]);
        $root->save();
        $this->seeInDatabase('entities', [
            "id" => "root",
            "model" => "root",
           "view" => "root",
           "parent_entity_id" => "root"
        ]);
    }  

    public function testEditEntity()
    {
        $ent = new Entity([
            "id" => "root",
            "model" => "root",
            "view" => "root",
         ]);
        $ent->save();
        $edit = Entity::where('id',"root")->update([
            "id" => "home",
            "model" => "home",
            "view" => "home"]);
        $this->seeInDatabase('entities', [
            "id" => "home",
            "model" => "home",
            "view" => "home"
        ]);
    } 

     public function testDeleteEntity()
    {
        $en1 = new Entity([
            "model" => "home",
            "id" => "home",
            "view" => "home"
         ]);
        $en1->save();
        $delete = Entity::where('id', 'home')->delete();
        $this->notSeeInDatabase('entities',[
            'id' => 'home'
        ]);
    } 
}
