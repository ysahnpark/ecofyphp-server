<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('auth/google', 'Auth\AuthGoogleController@redirectToProvider');
Route::get('auth/google/callback', 'Auth\AuthGoogleController@handleProviderCallback');

Route::get('auth/facebook', 'Auth\AuthFacebookController@redirectToProvider');
Route::get('auth/facebook/callback', 'Auth\AuthFacebookController@handleProviderCallback');

Route::get('auth/linkedin', 'Auth\AuthLinkedInController@redirectToProvider');
Route::get('auth/linkedin/callback', 'Auth\AuthLinkedInController@handleProviderCallback');


// You can also do: Route::group(['prefix' => 'api'], function() {}

Route::group(['middleware' => 'ecofyauth'], function () {
    Route::resource('api/accounts', 'AccountApiController');
    Route::resource('api/auths', 'AuthApiController');
    Route::get('api/myaccount', 'Auth\AuthApiController@myaccount');

    Route::post('api/import', 'ImportApiController@process');
});
// @todo include as part of protected API
Route::resource('api/accounts.relations', 'RelationApiController');

Route::post('api/signup', 'Auth\AuthApiController@signup');
Route::post('api/signin', 'Auth\AuthApiController@signin');
Route::post('api/signout', 'Auth\AuthApiController@signout');
