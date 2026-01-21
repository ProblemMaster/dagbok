<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// Activity rutter
$router->get('/activities', 'ActivityController@index');
$router->post('/activities', 'ActivityController@store');
$router->put('/activities/{id}', 'ActivityController@update');
$router->delete('/activities/{id}', 'ActivityController@destroy');

// Workouts rutter
$router->get('/workouts', 'WorkoutController@index');
$router->post('/workouts', 'WorkoutController@store');
$router->put('/workouts/{id}', 'WorkoutController@update');
$router->delete('/workouts/{id}', 'WorkoutController@destroy');

//Diagram rutter
$router->get('/statistics/activity/{id}', 'StatisticsController@activity');
$router->get('/statistics/all', 'StatisticsController@all');
