<?php

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

$router->group(
    [
        'middleware' => 'CorsMiddleware'
    ],
    function () use ($router)
    {
        $router->post('/auth/login', 'AuthController@postLogin');
    }
);


$router->options(
    '/{any:.*}',
    [
        'middleware' => ['CorsMiddleware'],
        function (){
            return response(['status' => 'success']);
        }
    ]
);

$router->group(['middleware' => ['jwt-auth']], function($router)
{
    $router->get('/me', 'AuthController@getAuthenticatedUser');
});