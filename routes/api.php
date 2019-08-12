<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

/********************************
 *    publisher rouer
 *      API
 * ******************************/

Route::group( [
    'prefix' => 'publisher',
    'namespace'=>"API",
    'middleware'=>[
        'CheckLang'
    ]
], function()
{
    Route::post('sign-in','PublisherController@signIn');

    Route::post('login', 'PublisherController@login');

    Route::post('get','PublisherController@getUser');

    Route::post('update-profile', 'PublisherController@updateProfile');

    Route::post('change-password', 'PublisherController@changePassword');

    Route::post('change-lang','PublisherController@changeLang');


});

/*=========================*/

/********************************
 *    store rouer
 *      API
 * ******************************/

Route::group( [
    'prefix' => 'store',
    'namespace'=>"API",
    'middleware'=>[
        'CheckLang'
    ]
], function()
{
    Route::post('sign-in','StoreController@signIn');

    Route::post('login', 'StoreController@login');

    Route::post('get','StoreController@getUser');

    Route::post('update-profile', 'StoreController@updateProfile');

    Route::post('change-password', 'StoreController@changePassword');

    Route::post('change-lang','StoreController@changeLang');

    Route::post('sendPasswordReset','StoreController@passwordReset');



});

/*=========================*/

/********************************
 *    store places
 *      API
 * ******************************/

Route::group( [
    'prefix' => 'store-places',
    'namespace'=>"API",
    'middleware'=>[
        'CheckLang'
    ]
], function()
{
    Route::post('index','storePlacesController@index');

    Route::post('add-store-place','storePlacesController@addStorePlace');

    Route::post('delete-store-place','storePlacesController@deleteStorePlace');

    Route::post('update-store-place','storePlacesController@updateStorePlace');





});

/*=========================*/

/********************************
 *      basic info
 *      API
 * ******************************/

Route::group( [
    'prefix' => 'info',
    'namespace'=>"API",
    'middleware'=>[
        'CheckLang'
    ]
], function()
{
      Route::post('get-storeTypes', 'BasicInfoController@getStoresTypes');
});

/*=========================*/

/********************************
 *      trip routes
 *      API
 * ******************************/

Route::group( [
    'prefix' => 'trip',
    'namespace'=>"API",
    'middleware'=>[
        'CheckLang'
    ]
], function()
{
    Route::post('start-trip', 'TripController@startTrip');

    Route::post('end-trip', 'TripController@endTrip');

    Route::post('share-trip', 'TripController@shareTrip');

    Route::post('delete-trip', 'TripController@deleteTrip');

    Route::post('update-trip', 'TripController@updateTrip');

    Route::post('get-currentTrip', 'TripController@currentTrip');

    Route::post('upload-resource', 'TripController@uploadResource');

    Route::post('get-resource','TripController@getTripResources');

    Route::post('delete-resource', 'TripController@deleteResource');

    Route::post('update-resource', 'TripController@updateResource');

    Route::post('get-publishing', 'TripController@getPublishing');

    Route::post('delete-share', 'TripController@deleteShare');
});

/*=========================*/



/********************************
 *      trip routes
 *      API
 * ******************************/

Route::group( [
    'prefix' => 'risks',
    'namespace'=>"API",
    'middleware'=>[
        'CheckLang'
    ]
], function()
{
    Route::post('add-risk','RisksController@addRisk');


});

/*=========================*/