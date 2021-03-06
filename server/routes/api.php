<?php

/*
|--------------------------------------------------------------------------
| Api Routes
|--------------------------------------------------------------------------
*/
$router->group(["prefix" => "api"], function () use ($router) {
    /**
     * Unauthenticated routes
    */
    $router->get('/', function () use ($router) {return ["version" => '4.0'];});
    $router->get('/cms/config', ['uses' => 'CmsController@showConfig']);
    $router->post('/user/login', ['uses' => 'UserController@authenticate']);

    /**
     * Authenticated routes
     */
    $router->group(['middleware' => 'auth'], function () use ($router) {
        $router->get('/user/me', ['uses' => 'UserController@showMe']);
        $router->get('/entities[/{model_name}]', ['uses' => 'EntityController@index']);
        $router->patch('/entities/relations/reorder', ['uses' => 'EntityController@reorderRelations']);
        $router->get('/entity/{entity_id}', ['uses' => 'EntityController@show']);
        $router->post('/entity', ['uses' => 'EntityController@create']);
        $router->patch('/entity/{entity_id}', ['uses' => 'EntityController@update']);
        $router->delete('/entity/{entity_id}', ['uses' => 'EntityController@delete']);
        $router->post('/entity/{caller_entity_id}/relation', ['uses' => 'EntityController@createRelation']);
        $router->delete('/entity/{caller_entity_id}/relation/{called_entity_id}/{kind}', ['uses' => 'EntityController@deleteRelation']);
        $router->post('/medium/{entity_id}/upload', ['uses' => 'MediaController@upload']);
        $router->post('/entity/{caller_entity_id}/create_and_relate', ['uses' => 'EntityController@createAndAddRelation']);
    });

    $router->get('/{path:.*}', function () use ($router) {
        return response(['error'=>'Api route not found'], 404);
    });
});

