<?php

/*
|--------------------------------------------------------------------------
| Raja Ongkir API Routes
|--------------------------------------------------------------------------
| path: host/api/{version}/endpoint
*/

Route::post('/ongkir/domestic-cost', 'Gateway\RajaOngkir\RajaOngkirGateController@getDomesticCost');

Route::post('/ongkir/domestic-costs', 'Gateway\RajaOngkir\RajaOngkirGateController@getDomesticCosts');

Route::post('/ongkir/international-cost', 'Gateway\RajaOngkir\RajaOngkirGateController@getInternationalCost');