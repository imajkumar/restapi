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

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->get('user/verify/{verification_code}', 'AuthController@verifyUser');

$router->post('register', 'AuthController@register');
$router->post('getoffer', 'ApiController@getOffer');
$router->post('login', 'AuthController@login');


$router->post('recover-request', 'AuthController@recoverRequest');
$router->post('verify-temp-password', 'AuthController@verifyTempPassword');
$router->post('set-new-password', 'AuthController@setNewPassword');

Route::group(['middleware' => ['jwt.auth']], function() {
    Route::get('logout', 'AuthController@logout');

    Route::get('allUser', 'TestController@allUser');

});
