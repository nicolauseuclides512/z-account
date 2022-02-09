<?php

Route::group(['prefix' => 'report'],
    function () {
        Route::group(['prefix' => 'salesorder'],
            function () {
                Route::get('by-month', 'Gateway\Report\SalesOrderReportGateController@byMonth');
                Route::get('by-item', 'Gateway\Report\SalesOrderReportGateController@byItem');
                Route::get('by-customer', 'Gateway\Report\SalesOrderReportGateController@byCustomer');
                Route::get('total', 'Gateway\Report\SalesOrderReportGateController@total');
            });
    });
