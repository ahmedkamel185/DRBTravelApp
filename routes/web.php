<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/','adminController@home');

//Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

/**********************************
 *
 * login routes
 *
 ***********************************/
//=============================================================
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');


//===========================================================



/********************************
 *    store routes
 *
 * ******************************/

Route::group( [
    'prefix' => 'store',
    'namespace'=>"WEB",
    'middleware'=>[
        'CheckLang'
    ]
], function()
{
    Route::get('index','StoreController@index')->name('store.index');

    Route::get('show/{id}','StoreController@show')->name('store.show');

    Route::get('delete-address/{id}','StoreController@deleteAddress')->name('store.delete');

    Route::get('edit-address/{id}','StoreController@editAddress')->name('store.edit');

    Route::post('update-address/{id}','StoreController@updateAddress')->name('store.update');

    Route::get('add-store','StoreController@addStore')->name('store.addStore');

    Route::post('save-store','StoreController@store')->name('store.store');

    Route::get('address-store/{id}','StoreController@storeAddress')->name('store.address');

    Route::get('store-type','StoreController@storeType')->name('store.addStoreType');

    Route::post('add-type','StoreController@addType')->name('store.store.type');

    Route::post('save-store-address/{id}','StoreController@saveAddress')->name('store.save');

    Route::get('changeStatus', 'StoreController@changeStatus');

    Route::get('address-change', 'StoreController@changeStatusAddress');

    Route::get('changeVerified', 'StoreController@changeVerified');




});

/***********
 *
 * publisher routes
 *
 * ******/

Route::group( [
    'prefix' => 'user',
    'namespace'=>"WEB",
    'middleware'=>[
        'CheckLang'
    ]
], function()
{
    Route::get('index','UsersController@index')->name('user.index');

    Route::get('show/{id}','UsersController@show')->name('user.show');

    Route::get('changeStatus', 'UsersController@changeStatus');

    Route::get('changeVerified', 'UsersController@changeVerified');


});
/*******
 *
 * Places routes
 *
 * ***********/

Route::group( [
    'prefix' => 'places',
    'namespace'=>"WEB",
    'middleware'=>[
        'CheckLang'
    ]
], function()
{
    Route::get('index','SuggestedPlacesController@index')->name('place.index');

    Route::get('add','SuggestedPlacesController@add')->name('place.add');

    Route::post('store','SuggestedPlacesController@store')->name('place.store');

    Route::get('edit/{id}','SuggestedPlacesController@edit')->name('place.edit');

    Route::post('update/{id}','SuggestedPlacesController@update')->name('place.update');

    Route::get('delete/{id}','SuggestedPlacesController@delete')->name('place.delete');



});

/*******
 *
 * manage routes
 *
 * ***********/

Route::group( [
    'prefix' => 'setting',
    'namespace'=>"WEB",
    'middleware'=>[
        'CheckLang'
    ]
], function()
{
    Route::get('index','ManageController@index')->name('manage.index');


    Route::get('terms-edit/{id}','ManageController@editTerms')->name('manage.terms.edit');


    Route::post('terms-update/{id}','ManageController@updateTerms')->name('manage.terms.update');

    Route::get('contact-edit/{id}','ManageController@editContacts')->name('manage.contacts.edit');

    Route::post('contact-update/{id}','ManageController@updateContacts')->name('manage.contacts.update');

    Route::get('about-edit/{id}','ManageController@editAbout')->name('manage.about.edit');

    Route::post('about-update/{id}','ManageController@updateAbout')->name('manage.about.update');

    Route::get('list-message','ManageController@allMessages')->name('manage.messages');

    Route::get('single-message/{id}','ManageController@singleMessage')->name('manage.single');




});


/*******
 *
 * notification routes
 *
 * ***********/

Route::group( [
    'prefix' => 'notification',
    'namespace'=>"WEB",
    'middleware'=>[
        'CheckLang'
    ]
], function()
{
    Route::get('index','NotificationsController@index')->name('notification.index');

    Route::get('send-notification/{id}','NotificationsController@sendNotification')->name('notification.send');

    Route::get('edit-notification/{id}','NotificationsController@editNotification')->name('notification.edit');

    Route::post('update-notification/{id}','NotificationsController@updateNotification')->name('notification.update');

    Route::get('add-notification','NotificationsController@addNotification')->name('notification.add');

    Route::post('store-notification','NotificationsController@storeNotification')->name('notification.store');



});


/*******
 *
 * notification routes
 *
 * ***********/

Route::group( [
    'prefix' => 'feedback',
    'namespace'=>"WEB",
    'middleware'=>[
        'CheckLang'
    ]
], function()
{
    Route::get('index','FeedbackController@index')->name('feedback.index');



});


/*******
 *
 * images routes
 *
 * ***********/

Route::group( [
    'prefix' => 'image',
    'namespace'=>"WEB",
    'middleware'=>[
        'CheckLang'
    ]
], function()
{
    Route::get('index','ImagesController@index')->name('image.index');



});



/*******
 *
 * videos routes
 *
 * ***********/

Route::group( [
    'prefix' => 'video',
    'namespace'=>"WEB",
    'middleware'=>[
        'CheckLang'
    ]
], function()
{
    Route::get('index','VideosController@index')->name('video.index');



});


/*******
 *
 * road routes
 *
 * ***********/

Route::group( [
    'prefix' => 'road',
    'namespace'=>"WEB",
    'middleware'=>[
        'CheckLang'
    ]
], function()
{
    Route::get('index','RoadRisksController@index')->name('road.index');

    Route::get('changeStatus','RoadRisksController@changeStatus')->name('road.status');

    Route::get('risk-list','RoadRisksController@listRisks')->name('road.risks');

    Route::get('add-risk','RoadRisksController@addRisk')->name('road.risks.add');

    Route::post('store-risk','RoadRisksController@storeRisk')->name('road.risks.store');

    Route::get('edit-risk/{id}','RoadRisksController@editRisk')->name('risk.edit');

    Route::post('update-risk/{id}','RoadRisksController@updateRisk')->name('risk.update');

    Route::get('delete-risk/{id}','RoadRisksController@deleteRisk')->name('risk.delete');




});

/*******
 *
 * trip routes
 *
 * ***********/

Route::group( [
    'prefix' => 'trip',
    'namespace'=>"WEB",
    'middleware'=>[
        'CheckLang'
    ]
], function()
{
    Route::get('index','tripController@index')->name('trip.index');

    Route::get('delete/{id}','tripController@destroy')->name('trip.delete');



});


