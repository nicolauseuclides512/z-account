<?php

/*
|--------------------------------------------------------------------------
| Asset API Routes
|--------------------------------------------------------------------------
| path: host/api/{version}/endpoint
*/

Route::group(['prefix' => 'countries'], function () {
    Route::get('/nested_list', 'Gateway\Asset\AssetCountryGateController@nestedList');
    Route::get('/list', 'Gateway\Asset\AssetCountryGateController@list');
});
Route::resource('countries', 'Gateway\Asset\AssetCountryGateController');

Route::group(['prefix' => 'provinces'], function () {
    Route::get('/list', 'Gateway\Asset\AssetProvinceGateController@list');
    Route::get('/countries/{id}', 'Gateway\Asset\AssetProvinceGateController@getByCountry');
});
Route::resource('provinces', 'Gateway\Asset\AssetProvinceGateController');

Route::group(['prefix' => 'districts'], function () {
    Route::get('/list', 'Gateway\Asset\AssetDistrictGateController@list');
    Route::get('/provinces/{id}', 'Gateway\Asset\AssetDistrictGateController@getByProvince');
});
Route::resource('districts', 'Gateway\Asset\AssetDistrictGateController');

Route::group(['prefix' => 'regions'], function () {
    Route::get('/list', 'Gateway\Asset\AssetRegionGateController@list');
    Route::get('/districts/{id}', 'Gateway\Asset\AssetRegionGateController@getByDistrict');
    Route::get('/search-cities', 'Gateway\Asset\AssetRegionGateController@searchCity');
});
Route::resource('regions', 'Gateway\Asset\AssetRegionGateController');

Route::group(['prefix' => 'carriers'], function () {
    Route::get('/list', 'Gateway\Asset\AssetCarrierGateController@list');
});
Route::resource('carriers', 'Gateway\Asset\AssetCarrierGateController');

Route::group(['prefix' => 'banks'], function () {
    Route::get('/list', 'Gateway\Asset\AssetBankGateController@list');
});
Route::resource('banks', 'Gateway\Asset\AssetBankGateController');

Route::group(['prefix' => 'payment_methods'], function () {
    Route::get('/list', 'Gateway\Asset\AssetPaymentMethodGateController@list');
});
Route::resource('payment_methods', 'Gateway\Asset\AssetPaymentMethodGateController');

Route::group(['prefix' => 'weight_units'], function () {
    Route::get('/list', 'Gateway\Asset\AssetWeightUnitGateController@list');
    Route::get('/code/{code}', 'Gateway\Asset\AssetWeightUnitGateController@getByCode');
});
Route::resource('weight_units', 'Gateway\Asset\AssetWeightUnitGateController');

Route::group(['prefix' => 'languages'], function () {
    Route::get('/list', 'Gateway\Asset\AssetLanguageGateController@list');
});
Route::resource('languages', 'Gateway\Asset\AssetLanguageGateController');

Route::group(['prefix' => 'timezones'], function () {
    Route::get('/list', 'Gateway\Asset\AssetTimezoneGateController@list');
});
Route::resource('timezones', 'Gateway\Asset\AssetTimezoneGateController');

Route::group(['prefix' => 'currencies'], function () {
    Route::get('/list', 'Gateway\Asset\AssetCurrencyGateController@list');
});
Route::resource('currencies', 'Gateway\Asset\AssetCurrencyGateController');

