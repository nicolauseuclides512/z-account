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

use GuzzleHttp\Client;

if (!function_exists('_rest')) {
    function _rest($path, $controller)
    {
        // global $app;
        Route::get($path, ['uses' => $controller . '@index']);
        Route::get($path . '/create', ['uses' => $controller . '@create']);
        Route::get($path . '/{id}', ['uses' => $controller . '@show']);
        Route::get($path . '/{id}/edit', ['uses' => $controller . '@edit']);
        Route::post($path, ['uses' => $controller . '@store']);
        Route::post($path . '/{id}/update', ['uses' => $controller . '@update']);
        Route::post($path . '/mark_as/{status}', ['uses' => $controller . '@markAsBulk']);
        Route::delete($path, ['uses' => $controller . '@destroyBulk']);
    }
}


Route::get('/ping_gate_1', 'Gateway\Store\PingGateController@index');

Route::get('/setup', 'Gateway\Store\StoreGateController@setup');

Route::get('/accounts/user', 'Gateway\Store\AssetAccountGateController@getUser');

Route::get('/download_pdf', function () {
    try {
        $client = new Client();
        $response = $client->get('http://192.168.1.4:9000/invoices/pdf')// in case your resource is under protection
        ->getBody()->getContents();

        $binary = base64_decode($response);
        header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename="my.pdf"');
        return $binary;
    } catch (Exception $e) {
        // Log the error or something
        dd($e->getMessage());
    }
});

#function for ASSET_ACCOUNT
_rest('/accounts', 'Gateway\Store\AssetAccountGateController');

#function for ASSET_CATEGORIES
_rest('/categories', 'Gateway\Store\AssetCategoryGateController');

#function for ASSET_PAYMENT_TERM
_rest('/payment_terms', 'Gateway\Store\AssetPaymentTermGateController');

#function for ASSET_UOM
Route::get('/uoms/{id}/set-default', ['uses' => 'Gateway\Store\AssetUomGateController@setDefault']);
_rest('/uoms', 'Gateway\Store\AssetUomGateController');

#function for ASSET_TAX
_rest('/taxes', 'Gateway\Store\AssetTaxGateController');

#function for ASSET_SALUTATION
_rest('/salutations', 'Gateway\Store\AssetSalutationGateController');

#function for ASSET_SALES_PERSON
_rest('/sales_persons', 'Gateway\Store\AssetSalesPersonGateController');

#function for ASSET_ATTRIBUTE
_rest('/attributes', 'Gateway\Store\AssetAttributeGateController');

#SETTING
Route::get('/settings/edit', ['uses' => 'Gateway\Store\SettingGateController@getEdit']);
Route::post('/settings/store_detail', ['uses' => 'Gateway\Store\SettingGateController@storeDetail']);
Route::post('/settings/checkout', ['uses' => 'Gateway\Store\SettingGateController@setCheckout']);
Route::post('/settings/shipping', ['uses' => 'Gateway\Store\SettingGateController@setShipping']);
Route::post('/settings/tax', ['uses' => 'Gateway\Store\SettingGateController@setTaxes']);
Route::post('/settings/payments', ['uses' => 'Gateway\Store\SettingGateController@addPaymentMethod']);
Route::delete('/settings/payments', ['uses' => 'Gateway\Store\SettingGateController@destroyPaymentMethod']);
Route::post('/settings/payments/{id}/add_detail', ['uses' => 'Gateway\Store\SettingGateController@addPaymentMethodDetail']);
Route::delete('/settings/payments/remove_detail', ['uses' => 'Gateway\Store\SettingGateController@destroyPaymentMethodDetail']);
Route::post('/settings/payments/bank_transfer/add', ['uses' => 'Gateway\Store\SettingGateController@addBankTransferPayment']);
Route::delete('/settings/payments/bank_transfer/remove/{payment_id}', ['uses' => 'Gateway\Store\SettingGateController@removeBankTransferPayment']);

#CONTACT
_rest('/contacts', 'Gateway\Store\ContactGateController');
Route::post('/contacts/import-data', 'Gateway\Store\ContactGateController@importData');
//
//#ITEM

Route::group(['prefix' => 'items'], function () {
    Route::post('/images/upload', ['uses' => 'Gateway\Store\ItemGateController@uploadItemImageToS3']);
    Route::post('', ['uses' => 'Gateway\Store\ItemGateController@storeV2']);
    Route::post('/{id}/images/add', ['uses' => 'Gateway\Store\ItemGateController@addImageV2']);
});
Route::group(['prefix' => 'items'], function () {
    Route::get('/get_upload_credential', 'Gateway\Store\ItemGateController@getUploadCredential');
//        _rest('', 'Gateway\Store\ItemGateController');


    Route::get('', ['uses' => 'Gateway\Store\ItemGateController@index']);
    Route::get('/create', ['uses' => 'Gateway\Store\ItemGateController@create']);
    Route::get('/{id}', ['uses' => 'Gateway\Store\ItemGateController@show']);
    Route::get('/{id}/edit', ['uses' => 'Gateway\Store\ItemGateController@edit']);
//    Route::post('', ['uses' => 'Gateway\Store\ItemGateController@store']);
    Route::post('', ['uses' => 'Gateway\Store\ItemGateController@storeV2']);
    Route::post('/{id}/update', ['uses' => 'Gateway\Store\ItemGateController@update']);
    Route::post('/mark_as/{status}', ['uses' => 'Gateway\Store\ItemGateController@markAsBulk']);
    Route::delete('', ['uses' => 'Gateway\Store\ItemGateController@destroyBulk']);

    Route::post('/{id}/attributes/add', 'Gateway\Store\ItemGateController@addAttribute');
    Route::post('/{id}/attributes/update', 'Gateway\Store\ItemGateController@updateAttributeKey');
    Route::post('/{id}/attributes/delete', 'Gateway\Store\ItemGateController@destroyAttributeVal');
    Route::post('/{id}/update_price', 'Gateway\Store\ItemGateController@updatePrice');
    Route::post('/{id}/update_inventory_stock', 'Gateway\Store\ItemGateController@updateInventoryStock');
    Route::post('/images/upload', ['uses' => 'Gateway\Store\ItemGateController@uploadItemImageToS3']);
    //newv2
    Route::post('/{id}/images/add', ['uses' => 'Gateway\Store\ItemGateController@addImageV2']);
    Route::delete('/{id}/images/remove/{imgId}', 'Gateway\Store\ItemGateController@removeImage');
//    Route::post('/{id}/images/add', 'Gateway\Store\ItemGateController@addImage');
    //endnew
    Route::get('/{id}/images/set_primary/{imgId}', 'Gateway\Store\ItemGateController@setPrimary');
    Route::post('/import-mass', 'Gateway\Store\ItemGateController@importMass');
});

//#COLLECTTION
//Route::get('/collections/get_upload_credential', 'Gateway\Store\ItemCollectionGateController@getUploadCredential');
//_rest('/collections', 'Gateway\Store\ItemCollectionGateController');
//Route::post('/collections/{id}/update_image', 'Gateway\Store\ItemCollectionGateController@updateImage');
//Route::get('/collections/{id}/items', 'Gateway\Store\ItemCollectionGateController@getItems');
//Route::delete('/collections/{id}/remove_image', 'Gateway\Store\ItemCollectionGateController@removeImage');
//
//Route::get('/discounts', 'Gateway\Store\DiscountGateController@index');
//Route::get('/discounts/create', 'Gateway\Store\DiscountGateController@create');
//Route::post('/discounts/{type}', 'Gateway\Store\DiscountGateController@store');
//Route::post('/discounts/mark_as/{status}', 'Gateway\Store\DiscountGateController@markAs');
//Route::delete('/discounts', 'Gateway\Store\DiscountGateController@destroy');
//
//#SALES ORDER
//Route::get('/sales_orders/get_credential', 'Gateway\Store\SalesOrderGateController@getCredential');

#PAYMENT
Route::get('/sales_orders/{soId}/invoices/{invId}/payments', 'Gateway\Store\SalesOrderGateController@fetchPaymentByInvoiceId');
Route::get('/sales_orders/{soId}/invoices/{invId}/payments/create', 'Gateway\Store\SalesOrderGateController@createPayment');
Route::get('/sales_orders/{soId}/invoices/{invId}/payments/{id}', 'Gateway\Store\SalesOrderGateController@getPaymentByIdAndInvoiceId');
Route::get('/sales_orders/{soId}/invoices/{invId}/payments/{id}/edit', 'Gateway\Store\SalesOrderGateController@editPayment');
Route::post('/sales_orders/{soId}/invoices/{invId}/payments', 'Gateway\Store\SalesOrderGateController@storePayment');
Route::post('/sales_orders/{soId}/invoices/{invId}/payments/{id}/update', 'Gateway\Store\SalesOrderGateController@updatePayment');
Route::delete('/sales_orders/{soId}/invoices/{invId}/payments', 'Gateway\Store\SalesOrderGateController@destroyPayment');

#SO
_rest('/sales_orders', 'Gateway\Store\SalesOrderGateController');
Route::get('/sales_orders/{soId}/details', 'Gateway\Store\SalesOrderGateController@getItemsBySoId');
Route::post('/sales_orders/{soId}/details/{detailId}',
    'Gateway\Store\SalesOrderGateController@updateDetail');

#INVOICE
Route::get('/sales_orders/{soId}/invoices', 'Gateway\Store\SalesOrderGateController@getInvoiceBySoId');
Route::get('/sales_orders/{soId}/invoices/{invId}', 'Gateway\Store\SalesOrderGateController@getInvoiceByIdAndSoId');
Route::get('/sales_orders/{soId}/invoices/{invId}/pdf', 'Gateway\Store\SalesOrderGateController@generatePDFInvoiceByIdAndSoId');
Route::get('/sales_orders/invoices/bulk-pdf', 'Gateway\Store\SalesOrderGateController@generateBulkPDF');
Route::post('/sales_orders/{soId}/invoices/{invId}/mark_as_sent', 'Gateway\Store\SalesOrderGateController@markAsSentPost');
Route::post('/sales_orders/{soId}/invoices/{invId}/mark_as_void', 'Gateway\Store\SalesOrderGateController@markAsVoidPost');
Route::get('/sales_orders/{soId}/invoices/{invId}/email', 'Gateway\Store\SalesOrderGateController@sendInvoiceEmailByIdAndSoIdInDetail');
Route::post('/sales_orders/{soId}/invoices/{invId}/email', 'Gateway\Store\SalesOrderGateController@sendInvoiceEmailByIdAndSoId');

#SHIPMENT
//    Route::get('/sales_orders/shipments/download-labels', 'Gateway\Store\SalesOrderGateController@downloadShipmentLabel');
Route::get('/sales_orders/shipments/bulk-label', 'Gateway\Store\SalesOrderGateController@generateShipmentLabelBulkPDF');
Route::get('/sales_orders/{soId}/shipments', 'Gateway\Store\SalesOrderGateController@fetchShipmentBySoId');
Route::get('/sales_orders/{soId}/shipments/create', 'Gateway\Store\SalesOrderGateController@createShipment');
Route::post('/sales_orders/{soId}/shipments', 'Gateway\Store\SalesOrderGateController@storeShipment');
Route::get('/sales_orders/{soId}/shipments/{id}/edit', 'Gateway\Store\SalesOrderGateController@editShipment');
Route::post('/sales_orders/{soId}/shipments/{id}/update', 'Gateway\Store\SalesOrderGateController@updateShipment');
Route::delete('/sales_orders/{soId}/shipments', 'Gateway\Store\SalesOrderGateController@destroyShipment');

Route::get('guzzle_test', 'Gateway\Store\StoreGateController@guzzleTest');
////   Route::get('/invoices/pdf', 'Gateway\Store\InvoiceGateController@generatePDFInvoice');
////   Route::get('/invoices/email', 'Gateway\Store\InvoiceGateController@sendInvoiceEmail');

# STOCK ADJUSTMTENT
Route::group(['prefix' => 'stock_adjustments'], function () {
    Route::post('/setup', 'Gateway\Store\StockAdjustmentGateController@setupObject');
    Route::get('/create', 'Gateway\Store\StockAdjustmentGateController@getCreateResource');
    Route::post('/', 'Gateway\Store\StockAdjustmentGateController@storeStockAdjustment');
    Route::get('/', 'Gateway\Store\StockAdjustmentGateController@index');
    Route::post('/{id}', 'Gateway\Store\StockAdjustmentGateController@updateStockAdjustment');
    Route::get('/{id}', 'Gateway\Store\StockAdjustmentGateController@getStockAdjustmentDetail');
    Route::delete('/{id}', 'Gateway\Store\StockAdjustmentGateController@deleteStockAdjustment');

    Route::group(['prefix' => 'history'], function () {
        Route::get('/item', 'Gateway\Store\StockAdjustmentGateController@itemHistory');
        Route::get('/reason', 'Gateway\Store\StockAdjustmentGateController@reasonHistory');
    });
});

# Stock
Route::group(['prefix' => 'stocks'], function () {
    Route::get('/detail', 'Gateway\Store\StockGateController@detail');
    Route::get('/', 'Gateway\Store\StockGateController@index');
    Route::post('/free_adjust', 'Gateway\Store\StockGateController@freeAdjust');
});

Route::group(['prefix' => 'integration'], function () {
    Route::group(['prefix' => 'lazada'], function () {
        Route::group(['prefix' => 'api-config'], function () {
            Route::post('/', 'Gateway\Store\LazadaAPIConfigGateController@store');
            Route::get('/', 'Gateway\Store\LazadaAPIConfigGateController@detail');
            Route::delete('/', 'Gateway\Store\LazadaAPIConfigGateController@delete');
        });
        Route::group(['prefix' => 'item/{item_id}'], function () {
            Route::get('/aliases', 'Gateway\Store\LazadaItemAliasGateController@fetchItemAlias');
            Route::post('/aliases', 'Gateway\Store\LazadaItemAliasGateController@storeItemAlias');
            Route::get('/aliases/{id}', 'Gateway\Store\LazadaItemAliasGateController@detailItemAlias');
            Route::post('/aliases/{id}', 'Gateway\Store\LazadaItemAliasGateController@updateItemAlias');
            Route::delete('/aliases/{id}', 'Gateway\Store\LazadaItemAliasGateController@deleteItemAlias');
        });
    });
});

# My Sales Channel
Route::group(['prefix' => 'my_channels'], function () {
    Route::get('/create', 'Gateway\Store\MySalesChannelGateController@getCreateResource');
    Route::get('/{id}', 'Gateway\Store\MySalesChannelGateController@getMySalesChannelDetail');
    Route::post('/{id}', 'Gateway\Store\MySalesChannelGateController@updateMySalesChannel');
    Route::delete('/{id}', 'Gateway\Store\MySalesChannelGateController@deleteMySalesChannel');
    Route::get('/', 'Gateway\Store\MySalesChannelGateController@index');
    Route::post('/', 'Gateway\Store\MySalesChannelGateController@storeMySalesChannel');
});

Route::group(['prefix' => 'reasons'], function () {
    Route::get('/create', 'Gateway\Store\ReasonGateController@getCreateResource');
    Route::get('/{id}', 'Gateway\Store\ReasonGateController@getReasonDetail');
    Route::post('/{id}', 'Gateway\Store\ReasonGateController@updateReason');
    Route::delete('/{id}', 'Gateway\Store\ReasonGateController@deleteReason');
    Route::get('/', 'Gateway\Store\ReasonGateController@fetch');
    Route::post('/', 'Gateway\Store\ReasonGateController@storeReason');
});

_rest('/quick_reply_category', 'Gateway\Store\QuickReplyCategoryGateController');

_rest('/quick_reply', 'Gateway\Store\QuickReplyGateController');

//_rest('/sales_channel', 'Gateway\Store\SalesChannelGateController');
