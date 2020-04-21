<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Entity;
use App\Models\EntityContent;


class EntityModelTest extends TestCase
{
    use DatabaseMigrations;

    private $data = [ 
        'data' => ['id' =>'root','model' =>'root','view' =>'root'],
        'edit_data' =>['id' => 'home','model' => 'home','view' => 'home'],
        'without_model' =>['id' => 'root','view' => 'root'],
        'with_parent_entity_id' => ['id' =>'root','model' =>'root','view' =>'root','parent_entity_id'=>'root'],
    ];
    
    /* *
     * A basic test example.
     *
     * @return void
     */
    public function testCreateEntity()
    {
        $root = new Entity($this->data['data']);
        $root->save();
        $this->seeInDatabase('entities',$this->data['data']);
    } 
   
    public function testEditEntity()
    {
        $root = new Entity($this->data['data']);
        $root->save();
        $root = Entity::where('id',"root")->update($this->data['edit_data']);
        $this->seeInDatabase('entities',$this->data['edit_data']);
    } 

     public function testDeleteEntity()
    {
        $root = new Entity($this->data['data']);
        $root->save();
        $delete = Entity::where('id', 'home')->delete();
        $this->notSeeInDatabase('entities',['id' => 'home']);
    } 

    public function testCreateEntityWithoutModel()
    {
        $this->expectExceptionMessage('A model name is requiered to create a new entity');
        $root = new Entity($this->data['without_model']);
        $root->save();
    } 

    public function testAncestorsParentEntityId()
    {
        $root = new Entity($this->data['with_parent_entity_id']);
        $root->save();
        $this->seeInDatabase('entities',['id'=>'root']);
        $this->seeInDatabase('relations',['caller_entity_id'=>'root','kind'=>'ancestor']);
    }


    /* public function testCreateEntityContent()
    {
        factory(Entity::class)->create([
            'id' =>'root',
            'model' =>'root',
            'view' =>'root',
            'parent_entity_id' =>'root'
        ]);
        
        $this->assertTrue(true);
    }  */
}
