<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Illuminate\Database\Seeder;
use Illuminate\Eloquent\Model;
use App\Models\Entity;

class ApiTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    public function setUp() :void {
        parent::setUp();
        $this->artisan('migrate:reset');
        $this->artisan('migrate');
        (new DatabaseSeeder())->call(ApiTestSeeder::class);
    }

    public function testLoginWithCorrectData()
    {
        global $argv, $argc;
        $json = [
            'email' => 'admin@example.com',
            'password' => 'Hello123'
        ];
        $user = $this->POST('/api/user/login', $json)->seeStatusCode(200)->response->getContent();
        $auth = json_decode($user, true);
        $authorizationToken = $auth['token'];
        return $authorizationToken;
    }

    public function testLoginWithIncorrectData()
    {
        $json = [
            'email' => 'kusikusi',
            'password' => 'IncorrectPassword'
        ];
        $this->POST('/api/user/login', $json)->seeStatusCode(401);

    }
    
}
