<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Entity;
use App\Models\EntityContent;


class EntityModelTest extends TestCase
{
    use DatabaseMigrations;

    private $data = [
        'root' => ['id'=>'root', 'model'=>'root', 'view' =>'root'],
        'home' =>['id'=>'home', 'model'=>'home', 'view'=>'home', 'parent_entity_id'=>'root'],
        'root_without_model'=>['id'=>'root', 'view'=>'root'],
        'page' => ['id'=>'page', 'model'=>'page', 'view'=>'page', 'parent_entity_id'=>'home'],
        'page_with_content' => ['id'=>'page', 'model'=>'page', 'view'=>'page', 'parent_entity_id'=>'home', 'content'=>['title'=>['en'=>"The page", 'es'=>'La página']]],
    ];

    /* *
     * A basic test example.
     *
     * @return void
     */
    public function testCreateEntity()
    {
        $root = new Entity($this->data['root']);
        $root->save();
        $this->seeInDatabase('entities',$this->data['root']);
    }

    public function testEditEntity()
    {
        $root = new Entity($this->data['root']);
        $root->save();
        $root = Entity::where('id',"root")->update($this->data['home']);
        $this->seeInDatabase('entities',$this->data['home']);
    }

     public function testDeleteEntity()
    {
        $root = new Entity($this->data['root']);
        $root->save();
        $delete = Entity::where('id', 'home')->delete();
        $this->notSeeInDatabase('entities',['id' => 'home']);
    }

    public function testCreateEntityWithoutModel()
    {
        $this->expectExceptionMessage('A model name is requiered to create a new entity');
        $root = new Entity($this->data['root_without_model']);
        $root->save();
    }

    public function testAncestorsParentEntityId()
    {
        $root = new Entity($this->data['root']);
        $home = new Entity($this->data['home']);
        $page = new Entity($this->data['page']);
        $root->save();
        $home->save();
        $page->save();
        $this->seeInDatabase('entities', $this->data['page']);
        $this->seeInDatabase('relations', ['caller_entity_id'=>'page', 'kind'=>'ancestor', 'called_entity_id'=>'home', 'depth'=>1]);
        $this->seeInDatabase('relations', ['caller_entity_id'=>'page', 'kind'=>'ancestor', 'called_entity_id'=>'root', 'depth'=>2]);
        $ancestors = Entity::select('id')->ancestorOf('page')->orderBy('ancestor_relation_depth')->get()->toArray();
        $this->assertEquals(count($ancestors), 2);
        $this->assertEquals($ancestors[0]['id'], 'home');
        $this->assertEquals($ancestors[1]['id'], 'root');
    }

    public function testCreateEntityContent()
    {
        $page = new Entity($this->data['page_with_content']);
        $page->save();
        $this->seeInDatabase('entities', $this->data['page']);
    }

  /*   public function testEntityContentRoutes()
    {
        factory(Entity::class)->create($this->data['page']);
        $this->seeInDatabase('entities',['id'=>'root']);
        $this->seeInDatabase('contents',$this->data['content_data']);

        $this->assertTrue($modelOne->is($modelTwo));

    } */
}
