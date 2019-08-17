<?php

use Illuminate\Http\Request;
header("Connection: Keep-alive");

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

    Route::post('get-logs','PublisherController@logActivity');

    Route::post('delete-logs','PublisherController@deleteLogActivity');

    Route::post('action-block','PublisherController@blockAction');

    Route::post('block-list','PublisherController@block_list');

    Route::post('action-follow','PublisherController@followAction');

    Route::post('follows-list','PublisherController@follow_list');

    Route::post('followers-list','PublisherController@follower_list');

    Route::post('search-publisher','PublisherController@searchPublisher');

    Route::post('logout','PublisherController@logout');

    Route::post('send-emailReset','PublisherController@restPasswordMail');

    Route::post('ceheck-tempPassword','PublisherController@checkTemploaryPassword');

    Route::post('reset-password','PublisherController@resetPassword');

    Route::post('get-notifications','PublisherController@getNotifications');

    Route::post('delte-notification','PublisherController@deleteNotifications');

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

    Route::post('logout','StoreController@logout');


    Route::post('send-emailReset','StoreController@restPasswordMail');

    Route::post('ceheck-tempPassword','StoreController@checkTemploaryPassword');

    Route::post('reset-password','StoreController@resetPassword');

    Route::post('get-notifications','StoreController@getNotifications');

    Route::post('delte-notification','StoreController@deleteNotifications');


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

    Route::post('store-near','storePlacesController@nearPlaces');





});

/*=========================*/
Route::group( [
    'prefix' => 'suggest',
    'namespace'=>"API",
    'middleware'=>[
        'CheckLang'
    ]
], function()
{

    Route::post('add-suggest','SuggestController@addSuugest');

    Route::post('edit-suggest','SuggestController@editSuugest');

    Route::post('delte-suggest','SuggestController@delteSuugest');

    Route::post('get-suggest','SuggestController@getSuugest');

    Route::post('list-suggest','SuggestController@getSuugests');

    Route::post('user-suggest','SuggestController@getUserSuggest');

    Route::post('comment-suggest', 'SuggestController@saveComment');

    Route::post('get-comment', 'SuggestController@getComment');

    Route::post('get-comments', 'SuggestController@getComments');

    Route::post('delete-comment', 'SuggestController@deleteComment');

    Route::post('update-comment', 'SuggestController@updateComment');

    Route::post('like-action', 'SuggestController@likeAction');

    Route::post('list-likes', 'SuggestController@getLikes');


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

      Route::post('get-terms', 'BasicInfoController@getTerms');

      Route::post('get-about', 'BasicInfoController@getAbout');

      Route::post('get-contact', 'BasicInfoController@getContact');
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

    Route::post('get-trip', 'TripController@getTrip');

    Route::post('end-trip', 'TripController@endTrip');

    Route::post('share-trip', 'TripController@shareTrip');

    Route::post('delete-trip', 'TripController@deleteTrip');

    Route::post('update-trip', 'TripController@updateTrip');

    Route::post('change-descTrip', 'TripController@changeDescTrip');

    Route::post('get-currentTrip', 'TripController@currentTrip');

    Route::post('upload-resource', 'TripController@uploadResource');

    Route::post('get-resource','TripController@getTripResources');

    Route::post('delete-resource', 'TripController@deleteResource');

    Route::post('update-resource', 'TripController@updateResource');

    Route::post('get-publishing', 'TripController@getPublishing');

    Route::post('delete-share', 'TripController@deleteShare');

    Route::post('change-privacy', 'TripController@changePrivacy');

    Route::post('comment-publishing', 'TripController@saveComment');

    Route::post('get-comment', 'TripController@getComment');

    Route::post('get-comments', 'TripController@getComments');

    Route::post('delete-comment', 'TripController@deleteComment');

    Route::post('update-comment', 'TripController@updateComment');

    Route::post('like-action', 'TripController@likeAction');

    Route::post('list-likes', 'TripController@getLikes');

    Route::post('list-shares', 'TripController@getShares');

    Route::post('fav-action', 'TripController@favAction');

    Route::post('list-favourit', 'TripController@getFavs');

    Route::post('change-statusPublisher', 'TripController@ChangeStatusPublisher');

    Route::post('get-public', 'TripController@publicTrip');

    Route::post('get-follower', 'TripController@followerTrip');

    Route::post('get-profile', 'TripController@publisherProfile');

    Route::post('list-trips', 'TripController@getPublishingUser');

    Route::get('test', 'TripController@test');

});

/*=========================*/



/********************************
 *      Risk routes
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
    Route::post('add-risks','RisksController@addRisk');

    Route::post('show-risks','RisksController@showRisks');

    Route::post('edit-risks','RisksController@editRisks');

    Route::post('delete-risks','RisksController@deleteRisks');

    Route::post('show-types-risks','RisksController@showTypeRisks');

});

/*=========================*/

/********************************
 *      Risk comment routes
 *      API
 * ******************************/

Route::group( [
    'prefix' => 'risk-comment',
    'namespace'=>"API",
    'middleware'=>[
        'CheckLang'
    ]
], function()
{
    Route::post('add-comment','RiskCommentController@addRiskComment');

    Route::post('remove-comment','RiskCommentController@removeComment');


});

/*=========================*/


/********************************
 *      Risk comment routes
 *      API
 * ******************************/

Route::group( [
    'prefix' => 'setting',
    'namespace'=>"API",
    'middleware'=>[
        'CheckLang'
    ]
], function()
{
    Route::post('contact','SettingsController@contact');



});

/*=========================*/