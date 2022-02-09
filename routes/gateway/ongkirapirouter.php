<?php

/*
|--------------------------------------------------------------------------
| Ongkir API Routes
|--------------------------------------------------------------------------
| path: host/api/{version}/endpoint
*/

Route::group(['prefix' => 'ongkir'],
    function () {
        Route::post('/shipping-costs', 'Gateway\Ongkir\OngkirBrokerGateController@shippingCosts');
        Route::post('/track-shipment', 'Gateway\Ongkir\OngkirBrokerGateController@trackShipment');
    });