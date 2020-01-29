<?php

use Illuminate\Http\Request;
//header("Connection: Keep-alive");

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

    Route::post('update-token', 'PublisherController@updateToken');

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

    Route::post('random-publisher','PublisherController@randomUsers');

    Route::post('logout','PublisherController@logout');

    Route::post('send-emailReset','PublisherController@restPasswordMail');

    Route::post('check-tempPassword','PublisherController@checkTemploaryPassword');

    Route::post('reset-password','PublisherController@resetPassword');

    Route::post('get-notifications','PublisherController@getNotifications');

    Route::post('delte-notification','PublisherController@deleteNotifications');

    Route::post('log-action','PublisherController@logger');

    Route::post('echo','PublisherController@test');

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

    Route::post('store-profile','StoreController@getprofile');

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

    Route::post('get-place','storePlacesController@getPlace');

    Route::post('add-store-place','storePlacesController@addStorePlace');

    Route::post('delete-store-place','storePlacesController@deleteStorePlace');

    Route::post('update-store-place','storePlacesController@updateStorePlace');

    Route::post('store-near','storePlacesController@nearPlaces');

    Route::post('get-three','storePlacesController@getThree');

    Route::post('get-three-near','storePlacesController@getThreeNear');



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

    Route::post('add-suggest-without-image','SuggestController@suggest_without_image');

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

    Route::post('suggest-near','SuggestController@nearSuggest');



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

      Route::post('get-riskTypes', 'BasicInfoController@getRiskTypes');


     Route::post('get-settings', 'BasicInfoController@getSettings');
//      Route::post('get-terms', 'BasicInfoController@getTerms');

//      Route::post('get-about', 'BasicInfoController@getAbout');
//
//      Route::post('get-mobile', 'BasicInfoController@getMobile');
//
//      Route::post('get-whats', 'BasicInfoController@getWhats');
//
//      Route::post('get-youtube', 'BasicInfoController@getYoutube');
//
//      Route::post('get-facebook', 'BasicInfoController@getFacebook');
//
//      Route::post('get-twitter', 'BasicInfoController@getTwitter');
//
//      Route::post('get-linked', 'BasicInfoController@getLinked');

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

    Route::post('create-spot', 'TripController@createSpot');

    Route::post('update-spot', 'TripController@updateSpot');

    Route::post('delete-spot', 'TripController@deleteSpot');

    Route::post('get-spots', 'TripController@getSpots');


    Route::post('get-spot', 'TripController@getSpot');

    Route::post('get-spots-count', 'TripController@getSpotsCount');

    Route::post('searched-spots', 'TripController@SearchedSpots');

    Route::post('search-query', 'TripController@multipleSearch');

    Route::post('get-user', 'TripController@userProfile');

    Route::post('get-user-spots', 'TripController@getUserSpots');

    Route::post('like-spot', 'TripController@likeSpot');

    Route::post('add-comment', 'TripController@addComment');

    Route::post('update-spot-comment', 'TripController@updateSpotComment');

    Route::post('delete-spot-comment', 'TripController@deleteSpotComment');

    Route::post('get-spot-comments', 'TripController@getSpotComments');

    Route::post('favourite-add', 'TripController@addToFavourite');

    Route::post('follows-spots', 'TripController@followsSpots');

    Route::post('favourite-spots', 'TripController@favouriteSpots');

    Route::post('tags-search', 'TripController@tagsSearch');

    Route::post('create-journey', 'TripController@createJourney');

    Route::post('get-journeys', 'TripController@getJourneys');

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

    Route::post('test', 'TripController@test');

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

    Route::post('add-risks-without-image','RisksController@addRisk_without_image');

    Route::post('show-risks','RisksController@showRisks');

    Route::post('get-risk','RisksController@getRisk');

    Route::post('edit-risks','RisksController@editRisks');

    Route::post('delete-risks','RisksController@deleteRisks');

    Route::post('show-types-risks','RisksController@showTypeRisks');

    Route::post('risk-near','RisksController@nearRisks');


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
 *      setting comment routes
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
