<?php

/*
|--------------------------------------------------------------------------
| Api Routes
|--------------------------------------------------------------------------
*/
$router->group(["prefix" => "api"], function () use ($router) {
    $router->get('/', function () use ($router) {
        return 'Kusikusi API';
    });
    $router->group(["prefix" => "entity"], function () use ($router) {
        $router->get('/', ['uses' => 'EntityController@index']);
        $router->get('/{entity_id}', ['uses' => 'EntityController@show']);
        $router->get('/{entity_id}/children', ['uses' => 'EntityController@children']);
    });
});

