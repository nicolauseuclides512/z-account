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

Route::get('/setup', 'Gateway\Store\StoreGateController@setup')->middleware('can:store-init-setup');

#ASSET_ACCOUNT
Route::group(['prefix' => 'accounts'],
    function () {
        Route::get('', ['uses' => 'Gateway\Store\AssetAccountGateController@index'])
            ->middleware('can:store-read-account');

        Route::get('/create', ['uses' => 'Gateway\Store\AssetAccountGateController@create'])
            ->middleware('can:store-store-account');

        Route::get('/{id}', ['uses' => 'Gateway\Store\AssetAccountGateController@show'])
            ->middleware('can:store-read-detail-account');

        Route::get('/{id}/edit', ['uses' => 'Gateway\Store\AssetAccountGateController@edit'])
            ->middleware('can:store-update-account');

        Route::post('', ['uses' => 'Gateway\Store\AssetAccountGateController@store'])
            ->middleware('can:store-store-account');

        Route::post('/{id}/update', ['uses' => 'Gateway\Store\AssetAccountGateController@update'])
            ->middleware('can:store-update-account');

        Route::post('/mark_as/{status}', ['uses' => 'Gateway\Store\AssetAccountGateController@markAsBulk'])
            ->middleware('can:store-mark-as-bulk-account');

        Route::delete('', ['uses' => 'Gateway\Store\AssetAccountGateController@destroyBulk'])
            ->middleware('can:store-destroy-bulk-account');
    });

#ASSET_CATEGORIES
Route::group(['prefix' => 'categories'],
    function () {
        Route::get('', ['uses' => 'Gateway\Store\AssetCategoryGateController@index'])
            ->middleware('can:store-read-category');

        Route::get('/create', ['uses' => 'Gateway\Store\AssetCategoryGateController@create'])
            ->middleware('can:store-update-category');

        Route::get('/{id}', ['uses' => 'Gateway\Store\AssetCategoryGateController@show'])
            ->middleware('can:store-read-detail-category');

        Route::get('/{id}/edit', ['uses' => 'Gateway\Store\AssetCategoryGateController@edit'])
            ->middleware('can:store-update-category');

        Route::post('', ['uses' => 'Gateway\Store\AssetCategoryGateController@store'])
            ->middleware('can:store-store-category');

        Route::post('/{id}/update', ['uses' => 'Gateway\Store\AssetCategoryGateController@update'])
            ->middleware('can:store-update-category');

        Route::post('/mark_as/{status}', ['uses' => 'Gateway\Store\AssetCategoryGateController@markAsBulk'])
            ->middleware('can:store-mark-as-bulk-category');

        Route::delete('', ['uses' => 'Gateway\Store\AssetCategoryGateController@destroyBulk'])
            ->middleware('can:store-destroy-bulk-category');
    });

#ASSET_PAYMENT_TERM
Route::group(['prefix' => 'payment_terms'],
    function () {
        Route::get('', ['uses' => 'Gateway\Store\AssetPaymentTermGateController@index'])
            ->middleware('can:store-read-payment-term');

        Route::get('/create', ['uses' => 'Gateway\Store\AssetPaymentTermGateController@create'])
            ->middleware('can:store-store-payment-term');

        Route::get('/{id}', ['uses' => 'Gateway\Store\AssetPaymentTermGateController@show'])
            ->middleware('can:store-read-detail-payment-term');

        Route::get('/{id}/edit', ['uses' => 'Gateway\Store\AssetPaymentTermGateController@edit'])
            ->middleware('can:store-update-payment-term');

        Route::post('', ['uses' => 'Gateway\Store\AssetPaymentTermGateController@store'])
            ->middleware('can:store-store-payment-term');

        Route::post('/{id}/update', ['uses' => 'Gateway\Store\AssetPaymentTermGateController@update'])
            ->middleware('can:store-update-payment-term');

        Route::post('/mark_as/{status}', ['uses' => 'Gateway\Store\AssetPaymentTermGateController@markAsBulk'])
            ->middleware('can:store-mark-as-bulk-payment-term');

        Route::delete('', ['uses' => 'Gateway\Store\AssetPaymentTermGateController@destroyBulk'])
            ->middleware('can:store-destroy-bulk-payment-term');
    });

#ASSET_UOM
Route::group(['prefix' => 'uoms'],
    function () {
        Route::get('/{id}/set-default', ['uses' => 'Gateway\Store\AssetUomGateController@setDefault'])
            ->middleware('can:store-set-default-uom');

        Route::get('', ['uses' => 'Gateway\Store\AssetUomGateController@index'])
            ->middleware('can:store-read-uom');

        Route::get('/create', ['uses' => 'Gateway\Store\AssetUomGateController@create'])
            ->middleware('can:store-store-uom');

        Route::get('/{id}', ['uses' => 'Gateway\Store\AssetUomGateController@show'])
            ->middleware('can:store-read-detail-uom');

        Route::get('/{id}/edit', ['uses' => 'Gateway\Store\AssetUomGateController@edit'])
            ->middleware('can:store-update-uom');

        Route::post('', ['uses' => 'Gateway\Store\AssetUomGateController@store'])
            ->middleware('can:store-store-uom');

        Route::post('/{id}/update', ['uses' => 'Gateway\Store\AssetUomGateController@update'])
            ->middleware('can:store-update-uom');

        Route::post('/mark_as/{status}', ['uses' => 'Gateway\Store\AssetUomGateController@markAsBulk'])
            ->middleware('can:store-mark-as-bulk-uom');

        Route::delete('', ['uses' => 'Gateway\Store\AssetUomGateController@destroyBulk'])
            ->middleware('can:store-destroy-bulk-uom');
    });

#ASSET_TAX
Route::group(['prefix' => 'taxes'],
    function () {
        Route::get('', ['uses' => 'Gateway\Store\AssetTaxGateController@index'])
            ->middleware('can:store-read-tax');

        Route::get('/create', ['uses' => 'Gateway\Store\AssetTaxGateController@create'])
            ->middleware('can:store-store-tax');

        Route::get('/{id}', ['uses' => 'Gateway\Store\AssetTaxGateController@show'])
            ->middleware('can:store-read-detail-tax');

        Route::get('/{id}/edit', ['uses' => 'Gateway\Store\AssetTaxGateController@edit'])
            ->middleware('can:store-update-tax');

        Route::post('', ['uses' => 'Gateway\Store\AssetTaxGateController@store'])
            ->middleware('can:store-store-tax');

        Route::post('/{id}/update', ['uses' => 'Gateway\Store\AssetTaxGateController@update'])
            ->middleware('can:store-update-tax');

        Route::post('/mark_as/{status}', ['uses' => 'Gateway\Store\AssetTaxGateController@markAsBulk'])
            ->middleware('can:store-mark-as-bulk-tax');

        Route::delete('', ['uses' => 'Gateway\Store\AssetTaxGateController@destroyBulk'])
            ->middleware('can:store-destroy-bulk-tax');
    });

#ASSET_SALUTATION
Route::group(['prefix' => 'salutations'],
    function () {
        Route::get('', ['uses' => 'Gateway\Store\AssetSalutationGateController@index'])
            ->middleware('can:store-read-salutation');

        Route::get('/create', ['uses' => 'Gateway\Store\AssetSalutationGateController@create'])
            ->middleware('can:store-store-salutation');

        Route::get('/{id}', ['uses' => 'Gateway\Store\AssetSalutationGateController@show'])
            ->middleware('can:store-read-detail-salutation');

        Route::get('/{id}/edit', ['uses' => 'Gateway\Store\AssetSalutationGateController@edit'])
            ->middleware('can:store-update-salutation');

        Route::post('', ['uses' => 'Gateway\Store\AssetSalutationGateController@store'])
            ->middleware('can:store-store-salutation');

        Route::post('/{id}/update', ['uses' => 'Gateway\Store\AssetSalutationGateController@update'])
            ->middleware('can:store-update-salutation');

        Route::post('/mark_as/{status}', ['uses' => 'Gateway\Store\AssetSalutationGateController@markAsBulk'])
            ->middleware('can:store-mark-as-bulk-salutation');

        Route::delete('', ['uses' => 'Gateway\Store\AssetSalutationGateController@destroyBulk'])
            ->middleware('can:store-destroy-bulk-salutation');
    });

#ASSET_SALES_PERSON
Route::group(['prefix' => 'sales_persons'],
    function () {
        Route::get('', ['uses' => 'Gateway\Store\AssetSalesPersonGateController@index'])
            ->middleware('can:store-read-sales-person');

        Route::get('/create', ['uses' => 'Gateway\Store\AssetSalesPersonGateController@create'])
            ->middleware('can:store-store-sales-person');

        Route::get('/{id}', ['uses' => 'Gateway\Store\AssetSalesPersonGateController@show'])
            ->middleware('can:store-read-detail-sales-person');

        Route::get('/{id}/edit', ['uses' => 'Gateway\Store\AssetSalesPersonGateController@edit'])
            ->middleware('can:store-update-sales-person');

        Route::post('', ['uses' => 'Gateway\Store\AssetSalesPersonGateController@store'])
            ->middleware('can:store-store-sales-person');

        Route::post('/{id}/update', ['uses' => 'Gateway\Store\AssetSalesPersonGateController@update'])
            ->middleware('can:store-update-sales-person');

        Route::post('/mark_as/{status}', ['uses' => 'Gateway\Store\AssetSalesPersonGateController@markAsBulk'])
            ->middleware('can:store-mark-as-bulk-sales-person');

        Route::delete('', ['uses' => 'Gateway\Store\AssetSalesPersonGateController@destroyBulk'])
            ->middleware('can:store-destroy-bulk-sales-person');
    });

#ASSET_ATTRIBUTE
Route::group(['prefix' => 'attributes'],
    function () {
        Route::get('', ['uses' => 'Gateway\Store\AssetAttributeGateController@index'])
            ->middleware('can:store-read-attribute');

        Route::get('/create', ['uses' => 'Gateway\Store\AssetAttributeGateController@create'])
            ->middleware('can:store-store-attribute');

        Route::get('/{id}', ['uses' => 'Gateway\Store\AssetAttributeGateController@show'])
            ->middleware('can:store-read-detail-attribute');

        Route::get('/{id}/edit', ['uses' => 'Gateway\Store\AssetAttributeGateController@edit'])
            ->middleware('can:store-update-attribute');

        Route::post('', ['uses' => 'Gateway\Store\AssetAttributeGateController@store'])
            ->middleware('can:store-store-attribute');

        Route::post('/{id}/update', ['uses' => 'Gateway\Store\AssetAttributeGateController@update'])
            ->middleware('can:store-update-attribute');

        Route::post('/mark_as/{status}', ['uses' => 'Gateway\Store\AssetAttributeGateController@markAsBulk'])
            ->middleware('can:store-mark-as-bulk-attribute');

        Route::delete('', ['uses' => 'Gateway\Store\AssetAttributeGateController@destroyBulk'])
            ->middleware('can:store-destroy-bulk-attribute');
    });

#SETTING
Route::group(['prefix' => 'settings'], function () {
    Route::get('/edit', ['uses' => 'Gateway\Store\SettingGateController@getEdit'])
        ->middleware('can:store-edit-setting');

    Route::post('/store_detail', ['uses' => 'Gateway\Store\SettingGateController@storeDetail'])
        ->middleware('can:store-store-detail-setting');

    Route::post('/checkout', ['uses' => 'Gateway\Store\SettingGateController@setCheckout'])
        ->middleware('can:store-set-checkout-setting');

    Route::post('/shipping', ['uses' => 'Gateway\Store\SettingGateController@setShipping'])
        ->middleware('can:store-set-shipping-setting');

    Route::post('/tax', ['uses' => 'Gateway\Store\SettingGateController@setTaxes'])
        ->middleware('can:store-set-taxes-setting');

    Route::post('/payments', ['uses' => 'Gateway\Store\SettingGateController@addPaymentMethod'])
        ->middleware('can:store-add-payment-method-setting');

    Route::delete('/payments', ['uses' => 'Gateway\Store\SettingGateController@destroyPaymentMethod'])
        ->middleware('can:store-destroy-payment-method-setting');

    Route::post('/payments/{id}/add_detail', ['uses' => 'Gateway\Store\SettingGateController@addPaymentMethodDetail'])
        ->middleware('can:store-add-payment-method-detail-setting');

    Route::delete('/payments/remove_detail', ['uses' => 'Gateway\Store\SettingGateController@destroyPaymentMethodDetail'])
        ->middleware('can:store-destroy-payment-method-detail-setting');

    Route::post('/payments/bank_transfer/add', ['uses' => 'Gateway\Store\SettingGateController@addBankTransferPayment'])
        ->middleware('can:store-add-bank-transfer-payment-setting');

    Route::delete('/payments/bank_transfer/remove/{payment_id}', ['uses' => 'Gateway\Store\SettingGateController@removeBankTransferPayment'])
        ->middleware('can:store-destroy-bank-transfer-payment-setting');
});

#contact
Route::group(['prefix' => 'contacts'], function () {
    Route::get('', ['uses' => 'Gateway\Store\ContactGateController@index'])
        ->middleware('can:store-read-contact');

    Route::get('/create', ['uses' => 'Gateway\Store\ContactGateController@create'])
        ->middleware('can:store-store-contact');

    Route::get('/{id}', ['uses' => 'Gateway\Store\ContactGateController@show'])
        ->middleware('can:store-read-detail-contact');

    Route::get('/{id}/edit', ['uses' => 'Gateway\Store\ContactGateController@edit'])
        ->middleware('can:store-update-contact');

    Route::post('', ['uses' => 'Gateway\Store\ContactGateController@store'])
        ->middleware('can:store-store-contact');

    Route::post('/{id}/update', ['uses' => 'Gateway\Store\ContactGateController@update'])
        ->middleware('can:store-update-contact');

    Route::post('/mark_as/{status}', ['uses' => 'Gateway\Store\ContactGateController@markAsBulk'])
        ->middleware('can:store-mark-as-bulk-contact');

    Route::delete('', ['uses' => 'Gateway\Store\ContactGateController@destroyBulk'])
        ->middleware('can:store-destroy-bulk-contact');

    Route::post('/import-data', 'Gateway\Store\ContactGateController@importData')
        ->middleware('can:store-import-data-contact');
});

#ITEM
Route::group(['prefix' => 'items'], function () {

    //deprecated
//    Route::get('/get_upload_credential', 'Gateway\Store\ItemGateController@getUploadCredential')
//        ->middleware('can:store-read-item');

    Route::get('', ['uses' => 'Gateway\Store\ItemGateController@index'])
        ->middleware('can:store-read-item');

    Route::get('/create', ['uses' => 'Gateway\Store\ItemGateController@create'])
        ->middleware('can:store-store-item');

    Route::get('/{id}', ['uses' => 'Gateway\Store\ItemGateController@show'])
        ->middleware('can:store-read-detail-item');

    Route::get('/{id}/edit', ['uses' => 'Gateway\Store\ItemGateController@edit'])
        ->middleware('can:store-update-item');

    Route::post('', ['uses' => 'Gateway\Store\ItemGateController@storeV2'])
        ->middleware('can:store-update-item');

    Route::post('/{id}/update', ['uses' => 'Gateway\Store\ItemGateController@update'])
        ->middleware('can:store-update-item');

    Route::post('/mark_as/{status}', ['uses' => 'Gateway\Store\ItemGateController@markAsBulk'])
        ->middleware('can:store-mark-as-bulk-item');

    Route::delete('', ['uses' => 'Gateway\Store\ItemGateController@destroyBulk'])
        ->middleware('can:store-destroy-bulk-item');


    Route::post('/{id}/attributes/add', 'Gateway\Store\ItemGateController@addAttribute')
        ->middleware('can:store-add-attribute-item');

    Route::post('/{id}/attributes/update', 'Gateway\Store\ItemGateController@updateAttributeKey')
        ->middleware('can:store-update-attribute-key-item');

    Route::post('/{id}/attributes/delete', 'Gateway\Store\ItemGateController@destroyAttributeVal')
        ->middleware('can:store-destroy-attribute-value-item');

    Route::post('/{id}/update_price', 'Gateway\Store\ItemGateController@updatePrice')
        ->middleware('can:store-update-price-item');

    Route::post('/{id}/update_inventory_stock', 'Gateway\Store\ItemGateController@updateInventoryStock')
        ->middleware('can:store-update-inventory-stock-item');

    Route::post('/{id}/images/add', ['uses' => 'Gateway\Store\ItemGateController@addImageV2'])
        ->middleware('can:store-add-image-item');

    Route::delete('/{id}/images/remove/{imgId}', 'Gateway\Store\ItemGateController@removeImage')
        ->middleware('can:store-destroy-image-image-item');

    Route::get('/{id}/images/set_primary/{imgId}', 'Gateway\Store\ItemGateController@setPrimary')
        ->middleware('can:store-set-primary-item');

    Route::post('/import-mass', 'Gateway\Store\ItemGateController@importMass')
        ->middleware('can:store-import-mass-item');
});

Route::group(['prefix' => 'sales_orders'],
    function () {

        #PAYMENT
        Route::get(
            '/{soId}/invoices/{invId}/payments',
            'Gateway\Store\SalesOrderGateController@fetchPaymentByInvoiceId')
            ->middleware('can:store-read-by-invoice-payment');

        Route::get(
            '/{soId}/invoices/{invId}/payments/create',
            'Gateway\Store\SalesOrderGateController@createPayment')
            ->middleware('can:store-store-invoice-payment');

        Route::get(
            '/{soId}/invoices/{invId}/payments/{id}',
            'Gateway\Store\SalesOrderGateController@getPaymentByIdAndInvoiceId')
            ->middleware('can:store-read-by-invoice-payment');

        Route::get(
            '/{soId}/invoices/{invId}/payments/{id}/edit',
            'Gateway\Store\SalesOrderGateController@editPayment')
            ->middleware('can:store-update-by-invoice-payment');

        Route::post(
            '/{soId}/invoices/{invId}/payments',
            'Gateway\Store\SalesOrderGateController@storePayment')
            ->middleware('can:store-store-by-invoice-payment');

        Route::post(
            '/{soId}/invoices/{invId}/payments/{id}/update',
            'Gateway\Store\SalesOrderGateController@updatePayment')
            ->middleware('can:store-update-by-invoice-payment');

        Route::delete(
            '/{soId}/invoices/{invId}/payments',
            'Gateway\Store\SalesOrderGateController@destroyPayment')
            ->middleware('can:store-destroy-by-invoice-payment');


        //SO
        Route::get('', ['uses' => 'Gateway\Store\SalesOrderGateController@index'])
            ->middleware('can:store-read-sales-order');

        Route::get('/create', ['uses' => 'Gateway\Store\SalesOrderGateController@create'])
            ->middleware('can:store-store-sales-order');

        Route::get('/{id}', ['uses' => 'Gateway\Store\SalesOrderGateController@show'])
            ->middleware('can:store-read-detail-sales-order');

        Route::get('/{id}/edit', ['uses' => 'Gateway\Store\SalesOrderGateController@edit'])
            ->middleware('can:store-update-sales-order');

        Route::post('', ['uses' => 'Gateway\Store\SalesOrderGateController@store'])
            ->middleware('can:store-store-sales-order');

        Route::post('/{id}/update', ['uses' => 'Gateway\Store\SalesOrderGateController@update'])
            ->middleware('can:store-update-sales-order');

        Route::post('/mark_as/{status}', ['uses' => 'Gateway\Store\SalesOrderGateController@markAsBulk'])
            ->middleware('can:store-mark-as-bulk-sales-order');
//unused
//        Route::delete('', ['uses' => 'Gateway\Store\SalesOrderGateController@destroyBulk'])
//            ->middleware('can:store-destroy-sales-order');

        Route::get('/{soId}/details', 'Gateway\Store\SalesOrderGateController@getItemsBySoId')
            ->middleware('can:store-read-sales-order-detail-sales-order');

        Route::post('/{soId}/details/{detailId}',
            'Gateway\Store\SalesOrderGateController@updateDetail')
            ->middleware('can:store-update-detail-sales-order');


        #INVOICE
        Route::get('/{soId}/invoices',
            'Gateway\Store\SalesOrderGateController@getInvoiceBySoId'
        )->middleware('can:store-read-by-sales-order-invoice');

        Route::get('/{soId}/invoices/{invId}',
            'Gateway\Store\SalesOrderGateController@getInvoiceByIdAndSoId'
        )->middleware('can:store-read-detail-by-sales-order-invoice');

        Route::get('/{soId}/invoices/{invId}/pdf',
            'Gateway\Store\SalesOrderGateController@generatePDFInvoiceByIdAndSoId'
        )->middleware('can:store-generate-pdf-by-sales-order-and-invoice');

        Route::get('/invoices/bulk-pdf',
            'Gateway\Store\SalesOrderGateController@generateBulkPDF'
        )->middleware('can:store-generate-pdf-bulk-invoice');

        Route::post('/{soId}/invoices/{invId}/mark_as_sent',
            'Gateway\Store\SalesOrderGateController@markAsSentPost'
        )->middleware('can:store-mark-as-sent-invoice');

        Route::post('/{soId}/invoices/{invId}/mark_as_void',
            'Gateway\Store\SalesOrderGateController@markAsVoidPost'
        )->middleware('can:store-mark-as-void-invoice');

        Route::get('/{soId}/invoices/{invId}/email',
            'Gateway\Store\SalesOrderGateController@sendInvoiceEmailByIdAndSoIdInDetail'
        )->middleware('can:store-send-email-by-sales-order-in-detail-invoice');

        Route::post('/{soId}/invoices/{invId}/email',
            'Gateway\Store\SalesOrderGateController@sendInvoiceEmailByIdAndSoId'
        )->middleware('can:store-send-email-by-sales-order-and-invoice');


        #SHIPMENT
        Route::get(
            '/shipments/bulk-label',
            'Gateway\Store\SalesOrderGateController@generateShipmentLabelBulkPDF')
            ->middleware('can:store-generate-shipment-label-pdf-bulk-shipment');

        Route::get(
            '/{soId}/shipments',
            'Gateway\Store\SalesOrderGateController@fetchShipmentBySoId')
            ->middleware('can:store-read-by-sales-order-shipment');

        Route::get(
            '/{soId}/shipments/create',
            'Gateway\Store\SalesOrderGateController@createShipment')
            ->middleware('can:store-store-by-sales-order-shipment');

        Route::post(
            '/{soId}/shipments',
            'Gateway\Store\SalesOrderGateController@storeShipment')
            ->middleware('can:store-store-by-sales-order-shipment');

        Route::get(
            '/{soId}/shipments/{id}/edit',
            'Gateway\Store\SalesOrderGateController@editShipment')
            ->middleware('can:store-update-by-sales-order-shipment');

        Route::post(
            '/{soId}/shipments/{id}/update',
            'Gateway\Store\SalesOrderGateController@updateShipment')
            ->middleware('can:store-update-by-sales-order-shipment');

        Route::delete(
            '/{soId}/shipments',
            'Gateway\Store\SalesOrderGateController@destroyShipment')
            ->middleware('can:store-destroy-by-sales-order-shipment');


    });

#STOCK ADJUSTMENT
Route::group(['prefix' => 'stock_adjustments'], function () {
    Route::post('/setup', 'Gateway\Store\StockAdjustmentGateController@setupObject')
        ->middleware('can:store-setup-object-stock-adjustment');

    Route::get('/', 'Gateway\Store\StockAdjustmentGateController@index')
        ->middleware('can:store-read-stock-adjustment');

    Route::get('/create', 'Gateway\Store\StockAdjustmentGateController@getCreateResource')
        ->middleware('can:store-store-stock-adjustment');

    Route::post('/', 'Gateway\Store\StockAdjustmentGateController@storeStockAdjustment')
        ->middleware('can:store-store-stock-adjustment');

    Route::post('/{id}', 'Gateway\Store\StockAdjustmentGateController@updateStockAdjustment')
        ->middleware('can:store-update-stock-adjustment');

    Route::get('/{id}', 'Gateway\Store\StockAdjustmentGateController@getStockAdjustmentDetail')
        ->middleware('can:store-read-detail-stock-adjustment');

    Route::delete('/{id}', 'Gateway\Store\StockAdjustmentGateController@deleteStockAdjustment')
        ->middleware('can:store-destroy-stock-adjustment');

    Route::group(['prefix' => 'history'], function () {
        Route::get('/item', 'Gateway\Store\StockAdjustmentGateController@itemHistory')
            ->middleware('can:store-history-item-stock-adjustment');

        Route::get('/reason', 'Gateway\Store\StockAdjustmentGateController@reasonHistory')
            ->middleware('can:store-history-reason-stock-adjustment');

    });
});

#Stock
Route::group(['prefix' => 'stocks'], function () {
    Route::get('/detail', 'Gateway\Store\StockGateController@detail')
        ->middleware('can:store-read-detail-stock');

    Route::get('/', 'Gateway\Store\StockGateController@index')
        ->middleware('can:store-read-stock');

    Route::post('/free_adjust', 'Gateway\Store\StockGateController@freeAdjust')
        ->middleware('can:store-free-adjust-stock');
});

Route::group(['prefix' => 'integration'],
    function () {
        Route::group(['prefix' => 'lazada'],
            function () {
                Route::group(['prefix' => 'api-config'],
                    function () {
                        Route::post('/', 'Gateway\Store\LazadaAPIConfigGateController@store')
                            ->middleware('can:read-contact');

                        Route::get('/', 'Gateway\Store\LazadaAPIConfigGateController@detail')
                            ->middleware('can:read-contact');

                        Route::delete('/', 'Gateway\Store\LazadaAPIConfigGateController@delete')
                            ->middleware('can:read-contact');
                    });

                Route::group(['prefix' => 'item/{item_id}'],
                    function () {
                        Route::get('/aliases', 'Gateway\Store\LazadaItemAliasGateController@fetchItemAlias')
                            ->middleware('can:read-contact');

                        Route::post('/aliases', 'Gateway\Store\LazadaItemAliasGateController@storeItemAlias')
                            ->middleware('can:read-contact');

                        Route::get('/aliases/{id}', 'Gateway\Store\LazadaItemAliasGateController@detailItemAlias')
                            ->middleware('can:read-contact');

                        Route::post('/aliases/{id}', 'Gateway\Store\LazadaItemAliasGateController@updateItemAlias')
                            ->middleware('can:read-contact');

                        Route::delete('/aliases/{id}', 'Gateway\Store\LazadaItemAliasGateController@deleteItemAlias')
                            ->middleware('can:read-contact');
                    });
            });
    });

# My Sales Channel
Route::group(['prefix' => 'my_channels'], function () {
    Route::get('/create', 'Gateway\Store\MySalesChannelGateController@getCreateResource')
        ->middleware('can:store-store-my-channel');

    Route::get('/{id}', 'Gateway\Store\MySalesChannelGateController@getMySalesChannelDetail')
        ->middleware('can:store-read-detail-my-channel');

    Route::post('/{id}', 'Gateway\Store\MySalesChannelGateController@updateMySalesChannel')
        ->middleware('can:store-update-my-channel');

    Route::delete('/{id}', 'Gateway\Store\MySalesChannelGateController@deleteMySalesChannel')
        ->middleware('can:store-destroy-my-channel');

    Route::get('/', 'Gateway\Store\MySalesChannelGateController@index')
        ->middleware('can:store-read-my-channel');

    Route::post('/', 'Gateway\Store\MySalesChannelGateController@storeMySalesChannel')
        ->middleware('can:store-store-my-channel');
});

#reasons
Route::group(['prefix' => 'reasons'], function () {
    Route::get('/create', 'Gateway\Store\ReasonGateController@getCreateResource')
        ->middleware('can:store-store-reason');

    Route::get('/{id}', 'Gateway\Store\ReasonGateController@getReasonDetail')
        ->middleware('can:store-read-detail-reason');

    Route::post('/{id}', 'Gateway\Store\ReasonGateController@updateReason')
        ->middleware('can:store-update-reason');

    Route::delete('/{id}', 'Gateway\Store\ReasonGateController@deleteReason')
        ->middleware('can:store-destroy-reason');

    Route::get('/', 'Gateway\Store\ReasonGateController@fetch')
        ->middleware('can:store-read-reason');

    Route::post('/', 'Gateway\Store\ReasonGateController@storeReason')
        ->middleware('can:store-store-reason');
});

#quick reply category
Route::group(['prefix' => 'quick_reply_category'],
    function () {
        Route::get('', ['uses' => 'Gateway\Store\QuickReplyCategoryGateController@index'])
            ->middleware('can:store-read-quick-reply-category');

        Route::get('/create', ['uses' => 'Gateway\Store\QuickReplyCategoryGateController@create'])
            ->middleware('can:store-store-quick-reply-category');

        Route::get('/{id}', ['uses' => 'Gateway\Store\QuickReplyCategoryGateController@show'])
            ->middleware('can:store-read-detail-quick-reply-category');

        Route::get('/{id}/edit', ['uses' => 'Gateway\Store\QuickReplyCategoryGateController@edit'])
            ->middleware('can:store-update-quick-reply-category');

        Route::post('', ['uses' => 'Gateway\Store\QuickReplyCategoryGateController@store'])
            ->middleware('can:store-store-quick-reply-category');

        Route::post('/{id}/update', ['uses' => 'Gateway\Store\QuickReplyCategoryGateController@update'])
            ->middleware('can:store-update-quick-reply-category');

        Route::post('/mark_as/{status}', ['uses' => 'Gateway\Store\QuickReplyCategoryGateController@markAsBulk'])
            ->middleware('can:store-mark-as-bulk-quick-reply-category');

        Route::delete('', ['uses' => 'Gateway\Store\QuickReplyCategoryGateController@destroyBulk'])
            ->middleware('can:store-destroy-bulk-quick-reply-category');
    });

#quick reply
Route::group(['prefix' => 'quick_reply'],
    function () {
        Route::get('', ['uses' => 'Gateway\Store\QuickReplyGateController@index'])
            ->middleware('can:store-read-quick-reply');

        Route::get('/create', ['uses' => 'Gateway\Store\QuickReplyGateController@create'])
            ->middleware('can:store-store-quick-reply');

        Route::get('/{id}', ['uses' => 'Gateway\Store\QuickReplyGateController@show'])
            ->middleware('can:store-read-detail-quick-reply');

        Route::get('/{id}/edit', ['uses' => 'Gateway\Store\QuickReplyGateController@edit'])
            ->middleware('can:store-update-quick-reply');

        Route::post('', ['uses' => 'Gateway\Store\QuickReplyGateController@store'])
            ->middleware('can:store-store-quick-reply');

        Route::post('/{id}/update', ['uses' => 'Gateway\Store\QuickReplyGateController@update'])
            ->middleware('can:store-update-quick-reply');

        Route::post('/mark_as/{status}', ['uses' => 'Gateway\Store\QuickReplyGateController@markAsBulk'])
            ->middleware('can:store-mark-as-bulk-quick-reply');

        Route::delete('', ['uses' => 'Gateway\Store\QuickReplyGateController@destroyBulk'])
            ->middleware('can:store-destroy-bulk-quick-reply');
    });

#sales-channel
//Route::group(['prefix' => 'sales_channel'],
//    function () {
//        Route::get('', ['uses' => 'Gateway\Store\SalesChannelGateController@index'])
//            ->middleware('can:store-read-sales-channel');
//
//        Route::get('/create', ['uses' => 'Gateway\Store\SalesChannelGateController@create'])
//            ->middleware('can:store-store-sales-channel');
//
//        Route::get('/{id}', ['uses' => 'Gateway\Store\SalesChannelGateController@show'])
//            ->middleware('can:store-read-detail-sales-channel');
//
//        Route::get('/{id}/edit', ['uses' => 'Gateway\Store\SalesChannelGateController@edit'])
//            ->middleware('can:store-update-sales-channel');
//
//        Route::post('', ['uses' => 'Gateway\Store\SalesChannelGateController@store'])
//            ->middleware('can:store-store-sales-channel');
//
//        Route::post('/{id}/update', ['uses' => 'Gateway\Store\SalesChannelGateController@update'])
//            ->middleware('can:store-update-sales-channel');
//
//        Route::post('/mark_as/{status}', ['uses' => 'Gateway\Store\SalesChannelGateController@markAsBulk'])
//            ->middleware('can:store-mark-as-bulk-sales-channel');
//
//        Route::delete('', ['uses' => 'Gateway\Store\SalesChannelGateController@destroyBulk'])
//            ->middleware('can:store-destroy-bulk-sales-channel');
//    });
