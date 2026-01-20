<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\ExampleController;

$router->post('/login', 'AuthController@login');
$router -> get('/refresh', 'AuthController@refresh');
$router -> delete('/refresh', 'AuthController@logout');
$router -> delete('/refresh/all', 'AuthController@logoutAll');

$router -> group(['middleware' => 'auth.jwt'], function() use ($router) {
    $router -> get('/protected', 'ExampleController@protected');
});
$router -> get('/open', 'ExampleController@open');
