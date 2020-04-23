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
        'home' => ['id'=>'home', 'model'=>'home', 'view' =>'home', 'parent_entity_id'=>'home', 'properties'=>'"price":50.4','published_at'=>'2015-05-02T00:00:00', 'unpublished_at'=>'2018-05-01T00:00:00']
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
        $user = $this->POST('/api/user/login', $json)->seeStatusCode(200)->response->getContent();
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
        $this->POST('/api/user/login', $json)->seeStatusCode(401);

    }

    public function testCreateEntityWithInvalidToken()
    {
        $json = ['model'=>'root'];
        $response = $this->json('POST', '/api/entity', $json, ['HTTP_Authorization' => 'Bearer '])->seeStatusCode(401);
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testCreateEntityWithOnlyModel($authorizationToken)
    {
        $json = ['model'=>'root'];
        $response = $this->json('POST', '/api/entity', $json, ['HTTP_Authorization' => 'Bearer '.$authorizationToken])->seeStatusCode(200);
        $auth = json_decode($response->response->getContent(), true);
        $entity_id = $auth['id'];
         print("\nENTITY CREATED ON testCreateEntityWithOnlyModel:\nId: ".$entity_id."\n");
        return $entity_id;
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testCreateEntityWithoutOnlyModel($authorizationToken)
    {
        $json = ['id'=>'root'];
        $response = $this->json('POST', '/api/entity', $json, ['HTTP_Authorization' => 'Bearer '.$authorizationToken])->seeStatusCode(422);
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testCreateEntityWithOwnId($authorizationToken)
    {
        $json = $this->data['root'];
        $response = $this->json('POST', '/api/entity', $json, ['HTTP_Authorization' => 'Bearer '.$authorizationToken])->seeStatusCode(200);
        $auth = json_decode($response->response->getContent(), true);
        $entity_id = $auth['id'];
        print("\nENTITY CREATED ON testCreateEntityWithOwnId:\nId: ".$entity_id."\n");
        return $entity_id;
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testCreateEntityWithOptionalParameters($authorizationToken)
    {
        $json = $this->data['home'];
        $response = $this->json('POST', '/api/entity', $json, ['HTTP_Authorization' => 'Bearer '.$authorizationToken])->seeStatusCode(200);
        $auth = json_decode($response->response->getContent(), true);
        $entity_id = $auth['id'];
        print("\nENTITY CREATED ON testCreateEntityWithOptionalParameters:\nId: ".$entity_id."\n");
        return $entity_id;
    }
    
}
