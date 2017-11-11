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

$api = $app->make(Dingo\Api\Routing\Router::class);

$api->version('v1', function ($api) {
    $api->post('/auth/login', [
        'as' => 'api.auth.login',
        'uses' => 'App\Http\Controllers\Auth\AuthController@postLogin',
    ]);
    $api->post('/auth/forgotpassword',    ['uses' => 'App\Http\Controllers\Auth\AuthController@postForgotpassword', 'as' => 'api.auth.forgotpassword']);

    $api->group([
        'middleware' => 'api.auth',
    ], function ($api) {
        $api->get('/', [
            'uses' => 'App\Http\Controllers\APIController@getIndex',
            'as' => 'api.index'
        ]);
        $api->get('/auth/user', [
            'uses' => 'App\Http\Controllers\Auth\AuthController@getUser',
            'as' => 'api.auth.user'
        ]);
        $api->patch('/auth/refresh', [
            'uses' => 'App\Http\Controllers\Auth\AuthController@patchRefresh',
            'as' => 'api.auth.refresh'
        ]);
        $api->delete('/auth/invalidate', [
            'uses' => 'App\Http\Controllers\Auth\AuthController@deleteInvalidate',
            'as' => 'api.auth.invalidate'
        ]);
        $api->get('/user', ['uses' => 'App\Http\Controllers\ApiController@getUser','as' => 'api.user']);

        // CLIENT module routes starts
        $api->get('/clients[/{id}]',            ['uses' => 'App\Http\Controllers\ClientController@getClients', 'as' => 'api.clients']);
        $api->post('/clients',                  ['uses' => 'App\Http\Controllers\ClientController@postCreateclient', 'as' => 'api.client']);
        $api->put('/clients[/{id}]',            ['uses' => 'App\Http\Controllers\ClientController@putClients', 'as' => 'api.clients']);
        $api->delete('/clients[/{id}]',         ['uses' => 'App\Http\Controllers\ClientController@deleteClients', 'as' => 'api.clients']);
        $api->get('/clients/{id}/integrations', ['uses' => 'App\Http\Controllers\ClientController@getClientsintegrations', 'as' => 'api.clients.integrations']);
        $api->get('/clients/{id}/branding',     ['uses' => 'App\Http\Controllers\ClientController@getClientbranding', 'as' => 'api.clients.branding']);
        $api->post('/clients/{id}/branding',    ['uses' => 'App\Http\Controllers\ClientController@postCreateclientbranding', 'as' => 'api.clients.createbranding']);

        // PACKAGE module routes starts
        $api->get('/packages[/{id}]',            ['uses' => 'App\Http\Controllers\PackageController@getPackages', 'as' => 'api.packages']);
    });
});

$api->version('v2', function ($api) {
    $api->post('/auth/login', [
        'as' => 'api.auth.login',
        'uses' => 'App\Http\Controllers\Auth\AuthController@postLogin',
    ]);

    $api->group([
        'middleware' => 'api.auth',
    ], function ($api) {
        $api->get('/', [
            'uses' => 'App\Http\Controllers\APIController@getIndex',
            'as' => 'api.index'
        ]);
        $api->get('/auth/user', [
            'uses' => 'App\Http\Controllers\Auth\AuthControllerv2@getUser',
            'as' => 'api.auth.user'
        ]);
        $api->patch('/auth/refresh', [
            'uses' => 'App\Http\Controllers\Auth\AuthController@patchRefresh',
            'as' => 'api.auth.refresh'
        ]);
        $api->delete('/auth/invalidate', [
            'uses' => 'App\Http\Controllers\Auth\AuthController@deleteInvalidate',
            'as' => 'api.auth.invalidate'
        ]);
    });
});
