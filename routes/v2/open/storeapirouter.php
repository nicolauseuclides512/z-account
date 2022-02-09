<?php

/*
|--------------------------------------------------------------------------
| SAHITO API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

# My Sales Channel
Route::group(['prefix' => 'open', 'middleware' => 'portal'],
    function () {
        Route::group(['prefix' => 'my-channels'],
            function () {
                Route::get('/{id}', 'Open\Store\MySalesChannelOpenController@detail');
                Route::get('/', 'Open\Store\MySalesChannelOpenController@fetch');
            });

        Route::group(['prefix' => 'organization'],
            function () {
//                Route::get('/', 'UserController@getMyOrganizations');
                Route::get('/', 'Open\Store\OrganizationOpenController@getMyOrganizations');
            });

        Route::group(['prefix' => 'shop'],
            function () {
                Route::get('/', 'Open\Store\ShopOpenController@fetch');
            });
    });
