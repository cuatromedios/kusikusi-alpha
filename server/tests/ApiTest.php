<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Illuminate\Database\Seeder;
use Illuminate\Eloquent\Model;
use App\Models\Entity;

class ApiTest extends TestCase
{
    private $data = [
        'root' => ['id'=>'root', 'model'=>'root', 'view'=>'root'],
        'home' => ['id'=>'home', 'model'=>'home', 'view' =>'home', 'parent_entity_id'=>'home', 'properties'=>'"price":50.4','published_at'=>'2015-05-02T00:00:00', 'unpublished_at'=>'2018-05-01T00:00:00'],
        'page_with_content' => ['id'=>'page', 'model'=>'page', 'view'=>'page', 'parent_entity_id'=>'home', 'contents'=>['title'=>['en'=>'The page', 'es'=>'La pagina']]],
        'section_with_content_slug' => ['id'=>'section', 'model'=>'section', 'view'=>'section', 'parent_entity_id'=>'section', 'contents'=>['title'=>['en'=>'The page', 'es'=>'La pagina'], 'section'=>['en'=>'The page', 'es'=>'La página'], 'slug'=>['en'=>'Hello', 'es'=>'Hola']]],
        'medium' => ['id'=>'medium', 'parent_entity_id'=>'medium', 'model'=>'medium']
    ];
    /**
     * A basic test example.
     *
     * @return void
     */
    /* public function setUp() :void {
        parent::setUp();
        $this->artisan('migrate:reset');
        $this->artisan('migrate');
        (new DatabaseSeeder())->call(ApiTestSeeder::class);
    } */

    public function testDatabaseSeeder()
    {
        $this->artisan('migrate:reset');
        $this->artisan('migrate');
        (new DatabaseSeeder())->call(ApiTestSeeder::class);
        $this->assertTrue(true);
    }

    public function testLoginWithCorrectData()
    {
        $json = [
            'email'=>'admin@example.com',
            'password'=>'Hello123'
        ];
        $user = $this->POST('/api/user/login', $json)
        ->seeStatusCode(200)->response->getContent();
        $auth = json_decode($user, true);
        $authorizationToken = $auth['token'];
        return $authorizationToken;
    }

    public function testLoginWithIncorrectData()
    {
        $json = [
            'email'=>'kusikusi',
            'password'=>'IncorrectPassword'
        ];
        $this->POST('/api/user/login', $json)
        ->seeStatusCode(401);

    }

    public function testCreateEntityWithInvalidToken()
    {
        $json = ['model'=>'root'];
        $response = $this->json('POST', '/api/entity', $json, ['HTTP_Authorization' => 'Bearer '])
        ->seeStatusCode(401);
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testCreateEntityWithOnlyModel($authorizationToken)
    {
        $json = ['model'=>'media'];
        $response = $this->json('POST', '/api/entity', $json, ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['model'=>'media'])
        ->seeStatusCode(200);
        $auth = json_decode($response->response->getContent(), true);
        $entity_id = $auth['id'];
         print("\nENTITY CREATED ON testCreateEntityWithOnlyModel:\nEntity Id: ".$entity_id."\n");
        return $entity_id;
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testCreateEntityWithoutModel($authorizationToken)
    {
        $json = ['id'=>'root'];
        $response = $this->json('POST', '/api/entity', $json, ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeStatusCode(422);
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testCreateEntityWithOwnId($authorizationToken)
    {
        $json = $this->data['root'];
        $response = $this->json('POST', '/api/entity', $json, ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains($this->data['root'])
        ->seeStatusCode(200);
        $auth = json_decode($response->response->getContent(), true);
        $entity_id = $auth['id'];
        print("\nENTITY CREATED ON testCreateEntityWithOwnId:\nEntity Id: ".$entity_id."\n");
        return $entity_id;
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testCreateEntityWithOptionalParameters($authorizationToken)
    {
        $json = $this->data['home'];
        $response = $this->json('POST', '/api/entity', $json, ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains($this->data['home'])
        ->seeStatusCode(200);
        $auth = json_decode($response->response->getContent(), true);
        $entity_id = $auth['id'];
        $review = $this->json('GET', '/api/entity/home', ['with'=>'entitiesRelated'], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['kind'=>'ancestor', 'depth'=>1])
        ->seeJsonContains(['kind'=>'ancestor', 'depth'=>2])
        ->seeStatusCode(200);
        print("\nENTITY CREATED ON testCreateEntityWithOptionalParameters:\nEntity Id: ".$entity_id."\n");
        return $entity_id;
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testCreateEntityWithContents($authorizationToken)
    {
        $json = $this->data['page_with_content'];
        $response = $this->json('POST', '/api/entity', $json, ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeStatusCode(200);
        $auth = json_decode($response->response->getContent(), true);
        $entity_id = $auth['id'];
        $review = $this->json('GET', '/api/entities/page', ['with'=>'entityContents'], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['lang'=>'en', 'field'=>'title', 'text'=>'The page'])
        ->seeJsonContains(['lang'=>'es', 'field'=>'title', 'text'=>'La pagina'])
        ->seeStatusCode(200);
        print("\nENTITY CREATED ON testCreateEntityWithOptionalParameters:\nEntity Id: ".$entity_id."\n");
        return $entity_id;
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testCreateEntityWithContentSlug($authorizationToken)
    {
        $json = $this->data['section_with_content_slug'];
        $response = $this->json('POST', '/api/entity', $json, ['HTTP_Authorization' => 'Bearer '.$authorizationToken])->seeStatusCode(200);
        $auth = json_decode($response->response->getContent(), true);
        $entity_id = $auth['id'];
        $review = $this->json('GET', '/api/entities/section', ['with'=>'routes'], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['path'=>'/Hello', 'lang'=>'es'])
        ->seeJsonContains(['path'=>'/Hola', 'lang'=>'en'])
        ->seeStatusCode(200);
        print("\nENTITY CREATED ON testCreateEntityWithOptionalParameters:\nEntity Id: ".$entity_id."\n");
        return $entity_id;
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testCreateEntityWithRelationsDB($authorizationToken)
    {
        $json = $this->data['medium'];
        $response = $this->json('POST', '/api/entity', $json, ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains($this->data['medium'])
        ->seeStatusCode(200);
        $auth = json_decode($response->response->getContent(), true);
        $entity_id = $auth['id'];
        $this->seeInDatabase('relations', ['caller_entity_id'=>'medium', 'kind'=>'ancestor', 'called_entity_id'=>'medium', 'depth'=>1]);
        $this->seeInDatabase('relations', ['caller_entity_id'=>'medium', 'kind'=>'ancestor', 'called_entity_id'=>'medium', 'depth'=>2]);
        print("\nENTITY CREATED ON testCreateEntityWithOptionalParameters:\nEntity Id: ".$entity_id."\n");
        return $entity_id;
    }
}
