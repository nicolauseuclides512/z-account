<?php

Route::group([
    'middleware' => ['auth:api', 'org'],
    'prefix' => 'report'],
    function () {
        Route::group(['prefix' => 'salesorder'], function () {
            Route::get('by-month', 'Gateway\Report\SalesOrderReportGateController@byMonth')
                ->middleware('can:report-by-month');

            Route::get('by-item', 'Gateway\Report\SalesOrderReportGateController@byItem')
                ->middleware('can:report-by-item');

            Route::get('by-customer', 'Gateway\Report\SalesOrderReportGateController@byCustomer')
                ->middleware('can:report-by-customer');

            Route::get('total', 'Gateway\Report\SalesOrderReportGateController@total')
                ->middleware('can:report-by-total');

        });
    });
