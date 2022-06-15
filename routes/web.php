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

#Auth
$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('/login', [ 
        'as' => 'auth.login',
        'uses' => 'UserController@login' 
    ]);
    
    $router->group(['middleware' => 'auth'], function () use ($router) {
        $router->get('/profile', [ 
            'as' => 'auth.profile',
            'uses' => 'UserController@profile' 
        ]);
    
        $router->get('/logout', [ 
            'as' => 'auth.logout',
            'uses' => 'UserController@logout' 
        ]);
    });
});

# User
$router->group(['prefix' => 'usuarios'], function() use ($router) {
    $router->get('/', 'UserController@index');
    $router->post('/create', 'UserController@store');
    $router->get('/confirm/{code}', 'UserController@confirm');
});