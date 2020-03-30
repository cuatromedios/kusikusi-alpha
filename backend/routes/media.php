<?php

/*
|--------------------------------------------------------------------------
| Api Routes
|--------------------------------------------------------------------------
*/
$router->group(["prefix" => "media"], function () use ($router) {
    $router->get('/', function () use ($router) {
        return 'Kusikusi Media';
    });
});

