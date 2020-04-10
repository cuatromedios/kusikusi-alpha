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
    $router->get('/', function () use ($router) {
        return ["version" => '4.0'];
    });
    /**
     * Authenticated routes
     */
    $router->group(["prefix" => "entity", 'middleware' => 'auth'], function () use ($router) {
        $router->get('/', ['uses' => 'EntityController@index']);
        $router->get('/{entity_id}', ['uses' => 'EntityController@show']);
        $router->get('/{entity_id}/children', ['uses' => 'EntityController@children']);
    });
});

