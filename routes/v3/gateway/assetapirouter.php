<?php

/*
|--------------------------------------------------------------------------
| Asset API Routes
|--------------------------------------------------------------------------
| path: host/api/{version}/endpoint
*/


//GET           /users                      index   users.index
//GET           /users/create               create  users.create
//POST          /users                      store   users.store
//GET           /users/{user}               show    users.show
//GET           /users/{user}/edit          edit    users.edit
//PUT|PATCH     /users/{user}               update  users.update
//DELETE        /users/{user}               destroy users.destroy

Route::group(['prefix' => 'countries'], function () {
    Route::get('/nested_list', 'Gateway\Asset\AssetCountryGateController@nestedList')
        ->middleware('can:asset-nested-list-country');

    Route::get('/list', 'Gateway\Asset\AssetCountryGateController@list')
        ->middleware('can:asset-list-country');


    Route::get('', 'Gateway\Asset\AssetCountryGateController@index')
        ->middleware('can:asset-read-country');

    Route::get('/create', 'Gateway\Asset\AssetCountryGateController@create')
        ->middleware('can:asset-store-country');

    Route::post('', 'Gateway\Asset\AssetCountryGateController@store')
        ->middleware('can:asset-store-country');

    Route::get('/{id}', 'Gateway\Asset\AssetCountryGateController@show')
        ->middleware('can:asset-read-detail-country');

    Route::get('/{id}/edit', 'Gateway\Asset\AssetCountryGateController@edit')
        ->middleware('can:asset-update-country');

    Route::put('/{id}', 'Gateway\Asset\AssetCountryGateController@update')
        ->middleware('can:asset-update-country');

    Route::delete('/{id}', 'Gateway\Asset\AssetCountryGateController@destroy')
        ->middleware('can:asset-destroy-country');

});

Route::group(['prefix' => 'provinces'], function () {
    Route::get('/list', 'Gateway\Asset\AssetProvinceGateController@list')
        ->middleware('can:asset-list-province');

    Route::get('/countries/{id}', 'Gateway\Asset\AssetProvinceGateController@getByCountry')
        ->middleware('can:asset-read-by-country-province');


    Route::get('', 'Gateway\Asset\AssetProvinceGateController@index')
        ->middleware('can:asset-read-province');

    Route::get('/create', 'Gateway\Asset\AssetProvinceGateController@create')
        ->middleware('can:asset-read-detail-province');

    Route::post('', 'Gateway\Asset\AssetProvinceGateController@store')
        ->middleware('can:asset-store-province');

    Route::get('/{id}', 'Gateway\Asset\AssetProvinceGateController@show')
        ->middleware('can:asset-store-province');

    Route::get('/{id}/edit', 'Gateway\Asset\AssetProvinceGateController@edit')
        ->middleware('can:asset-update-province');

    Route::put('/{id}', 'Gateway\Asset\AssetProvinceGateController@update')
        ->middleware('can:asset-update-province');

    Route::delete('/{id}', 'Gateway\Asset\AssetProvinceGateController@destroy')
        ->middleware('can:asset-destroy-province');

});

Route::group(['prefix' => 'districts'], function () {
    Route::get('/list', 'Gateway\Asset\AssetDistrictGateController@list')
        ->middleware('can:asset-list-district');

    Route::get('/provinces/{id}', 'Gateway\Asset\AssetDistrictGateController@getByProvince')
        ->middleware('can:asset-read-by-province-district');


    Route::get('', 'Gateway\Asset\AssetDistrictGateController@index')
        ->middleware('can:asset-read-district');

    Route::get('/create', 'Gateway\Asset\AssetDistrictGateController@create')
        ->middleware('can:asset-store-district');

    Route::post('', 'Gateway\Asset\AssetDistrictGateController@store')
        ->middleware('can:asset-store-district');

    Route::get('/{id}', 'Gateway\Asset\AssetDistrictGateController@show')
        ->middleware('can:asset-read-detail-district');

    Route::get('/{id}/edit', 'Gateway\Asset\AssetDistrictGateController@edit')
        ->middleware('can:asset-update-district');

    Route::put('/{id}', 'Gateway\Asset\AssetDistrictGateController@update')
        ->middleware('can:asset-update-district');

    Route::delete('/{id}', 'Gateway\Asset\AssetDistrictGateController@destroy')
        ->middleware('can:asset-destroy-district');

});

Route::group(['prefix' => 'regions'], function () {
    Route::get('/list', 'Gateway\Asset\AssetRegionGateController@list')
        ->middleware('can:asset-list-region');

    Route::get('/districts/{id}', 'Gateway\Asset\AssetRegionGateController@getByDistrict')
        ->middleware('can:asset-read-by-district-region');

    Route::get('/search-cities', 'Gateway\Asset\AssetRegionGateController@searchCity')
        ->middleware('can:asset-search-cities-region');


    Route::get('', 'Gateway\Asset\AssetRegionGateController@index')
        ->middleware('can:asset-read-region');

    Route::get('/create', 'Gateway\Asset\AssetRegionGateController@create')
        ->middleware('can:asset-store-region');

    Route::post('', 'Gateway\Asset\AssetRegionGateController@store')
        ->middleware('can:asset-store-region');

    Route::get('/{id}', 'Gateway\Asset\AssetRegionGateController@show')
        ->middleware('can:asset-read-detail-region');

    Route::get('/{id}/edit', 'Gateway\Asset\AssetRegionGateController@edit')
        ->middleware('can:asset-update-region');

    Route::put('/{id}', 'Gateway\Asset\AssetRegionGateController@update')
        ->middleware('can:asset-update-region');

    Route::delete('/{id}', 'Gateway\Asset\AssetRegionGateController@destroy')
        ->middleware('can:asset-destroy-region');

});

Route::group(['prefix' => 'carriers'], function () {
    Route::get('/list', 'Gateway\Asset\AssetCarrierGateController@list')
        ->middleware('can:asset-list-carrier');


    Route::get('', 'Gateway\Asset\AssetCarrierGateController@index')
        ->middleware('can:asset-read-carrier');

    Route::get('/create', 'Gateway\Asset\AssetCarrierGateController@create')
        ->middleware('can:asset-store-carrier');

    Route::post('', 'Gateway\Asset\AssetCarrierGateController@store')
        ->middleware('can:asset-store-carrier');

    Route::get('/{id}', 'Gateway\Asset\AssetCarrierGateController@show')
        ->middleware('can:asset-read-detail-carrier');

    Route::get('/{id}/edit', 'Gateway\Asset\AssetCarrierGateController@edit')
        ->middleware('can:asset-update-carrier');

    Route::put('/{id}', 'Gateway\Asset\AssetCarrierGateController@update')
        ->middleware('can:asset-update-carrier');

    Route::delete('/{id}', 'Gateway\Asset\AssetCarrierGateController@destroy')
        ->middleware('can:asset-destroy-carrier');

});

Route::group(['prefix' => 'banks'], function () {
    Route::get('/list', 'Gateway\Asset\AssetBankGateController@list')
        ->middleware('can:asset-list-bank');


    Route::get('', 'Gateway\Asset\AssetBankGateController@index')
        ->middleware('can:asset-read-bank');

    Route::get('/create', 'Gateway\Asset\AssetBankGateController@create')
        ->middleware('can:asset-store-bank');

    Route::post('', 'Gateway\Asset\AssetBankGateController@store')
        ->middleware('can:asset-store-bank');

    Route::get('/{id}', 'Gateway\Asset\AssetBankGateController@show')
        ->middleware('can:asset-read-detail-bank');

    Route::get('/{id}/edit', 'Gateway\Asset\AssetBankGateController@edit')
        ->middleware('can:asset-update-bank');

    Route::put('/{id}', 'Gateway\Asset\AssetBankGateController@update')
        ->middleware('can:asset-update-bank');

    Route::delete('/{id}', 'Gateway\Asset\AssetBankGateController@destroy')
        ->middleware('can:asset-destroy-bank');

});

Route::group(['prefix' => 'payment_methods'], function () {
    Route::get('/list', 'Gateway\Asset\AssetPaymentMethodGateController@list')
        ->middleware('can:asset-list-payment-method');

    Route::get('', 'Gateway\Asset\AssetPaymentMethodGateController@index')
        ->middleware('can:asset-read-payment-method');

    Route::get('/create', 'Gateway\Asset\AssetPaymentMethodGateController@create')
        ->middleware('can:asset-store-payment-method');

    Route::post('', 'Gateway\Asset\AssetPaymentMethodGateController@store')
        ->middleware('can:asset-store-payment-method');

    Route::get('/{id}', 'Gateway\Asset\AssetPaymentMethodGateController@show')
        ->middleware('can:asset-read-detail-payment-method');

    Route::get('/{id}/edit', 'Gateway\Asset\AssetPaymentMethodGateController@edit')
        ->middleware('can:asset-update-payment-method');

    Route::put('/{id}', 'Gateway\Asset\AssetPaymentMethodGateController@update')
        ->middleware('can:asset-update-payment-method');

    Route::delete('/{id}', 'Gateway\Asset\AssetPaymentMethodGateController@destroy')
        ->middleware('can:asset-destroy-payment-method');

});

Route::group(['prefix' => 'weight_units'], function () {
    Route::get('/list', 'Gateway\Asset\AssetWeightUnitGateController@list')
        ->middleware('can:asset-list-weight-unit');

    Route::get('/code/{code}', 'Gateway\Asset\AssetWeightUnitGateController@getByCode')
        ->middleware('can:asset-read-by-code-weight-unit');


    Route::get('', 'Gateway\Asset\AssetWeightUnitGateController@index')
        ->middleware('can:asset-read-weight-unit');

    Route::get('/create', 'Gateway\Asset\AssetWeightUnitGateController@create')
        ->middleware('can:asset-store-weight-unit');

    Route::post('', 'Gateway\Asset\AssetWeightUnitGateController@store')
        ->middleware('can:asset-store-weight-unit');

    Route::get('/{id}', 'Gateway\Asset\AssetWeightUnitGateController@show')
        ->middleware('can:asset-read-detail-weight-unit');

    Route::get('/{id}/edit', 'Gateway\Asset\AssetWeightUnitGateController@edit')
        ->middleware('can:asset-update-weight-unit');

    Route::put('/{id}', 'Gateway\Asset\AssetWeightUnitGateController@update')
        ->middleware('can:asset-update-weight-unit');

    Route::delete('/{id}', 'Gateway\Asset\AssetWeightUnitGateController@destroy')
        ->middleware('can:asset-destroy-weight-unit');

});

Route::group(['prefix' => 'languages'], function () {
    Route::get('/list', 'Gateway\Asset\AssetLanguageGateController@list')
        ->middleware('can:asset-list-language');

    Route::get('', 'Gateway\Asset\AssetLanguageGateController@index')
        ->middleware('can:asset-read-language');

    Route::get('/create', 'Gateway\Asset\AssetLanguageGateController@create')
        ->middleware('can:asset-store-language');

    Route::post('', 'Gateway\Asset\AssetLanguageGateController@store')
        ->middleware('can:asset-store-language');

    Route::get('/{id}', 'Gateway\Asset\AssetLanguageGateController@show')
        ->middleware('can:asset-read-detail-language');

    Route::get('/{id}/edit', 'Gateway\Asset\AssetLanguageGateController@edit')
        ->middleware('can:asset-update-language');

    Route::put('/{id}', 'Gateway\Asset\AssetLanguageGateController@update')
        ->middleware('can:asset-update-language');

    Route::delete('/{id}', 'Gateway\Asset\AssetLanguageGateController@destroy')
        ->middleware('can:asset-destroy-language');

});

Route::group(['prefix' => 'timezones'], function () {
    Route::get('/list', 'Gateway\Asset\AssetTimezoneGateController@list')
        ->middleware('can:asset-list-timezone');

    Route::get('', 'Gateway\Asset\AssetTimezoneGateController@index')
        ->middleware('can:asset-read-timezone');

    Route::get('/create', 'Gateway\Asset\AssetTimezoneGateController@create')
        ->middleware('can:asset-store-timezone');

    Route::post('', 'Gateway\Asset\AssetTimezoneGateController@store')
        ->middleware('can:asset-store-timezone');

    Route::get('/{id}', 'Gateway\Asset\AssetTimezoneGateController@show')
        ->middleware('can:asset-read-detail-timezone');

    Route::get('/{id}/edit', 'Gateway\Asset\AssetTimezoneGateController@edit')
        ->middleware('can:asset-update-timezone');

    Route::put('/{id}', 'Gateway\Asset\AssetTimezoneGateController@update')
        ->middleware('can:asset-update-timezone');

    Route::delete('/{id}', 'Gateway\Asset\AssetTimezoneGateController@destroy')
        ->middleware('can:asset-destroy-timezone');

});

Route::group(['prefix' => 'currencies'], function () {
    Route::get('/list', 'Gateway\Asset\AssetCurrencyGateController@list')
        ->middleware('can:asset-list-currency');

    Route::get('', 'Gateway\Asset\AssetCurrencyGateController@index')
        ->middleware('can:asset-read-currency');

    Route::get('/create', 'Gateway\Asset\AssetCurrencyGateController@create')
        ->middleware('can:asset-store-currency');

    Route::post('', 'Gateway\Asset\AssetCurrencyGateController@store')
        ->middleware('can:asset-store-currency');

    Route::get('/{id}', 'Gateway\Asset\AssetCurrencyGateController@show')
        ->middleware('can:asset-read-detail-currency');

    Route::get('/{id}/edit', 'Gateway\Asset\AssetCurrencyGateController@edit')
        ->middleware('can:asset-update-currency');

    Route::put('/{id}', 'Gateway\Asset\AssetCurrencyGateController@update')
        ->middleware('can:asset-update-currency');

    Route::delete('/{id}', 'Gateway\Asset\AssetCurrencyGateController@destroy')
        ->middleware('can:asset-destroy-currency');

});


//Route::resource('countries', 'Gateway\Asset\AssetCountryGateController');
//Route::resource('provinces', 'Gateway\Asset\AssetProvinceGateController');
//Route::resource('districts', 'Gateway\Asset\AssetDistrictGateController');
//Route::resource('regions', 'Gateway\Asset\AssetRegionGateController');
//Route::resource('carriers', 'Gateway\Asset\AssetCarrierGateController');
//Route::resource('banks', 'Gateway\Asset\AssetBankGateController');
//Route::resource('payment_methods', 'Gateway\Asset\AssetPaymentMethodGateController');
//Route::resource('weight_units', 'Gateway\Asset\AssetWeightUnitGateController');
//Route::resource('languages', 'Gateway\Asset\AssetLanguageGateController');
//Route::resource('timezones', 'Gateway\Asset\AssetTimezoneGateController');
//Route::resource('currencies', 'Gateway\Asset\AssetCurrencyGateController');

