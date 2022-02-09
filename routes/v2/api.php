<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function () {
    return 'Zuragan Account API ' . env('APP_VERSION') . ' [' . env('APP_ENV') . ']';
});

Route::get('/lang-data', 'HomeController@langData');

//TODO(*): replace with zuragan ongkir api
Route::group(['prefix' => 'pub'],
    function () {
        Route::post('ongkir/domestic-cost', 'Gateway\RajaOngkir\RajaOngkirGateController@getDomesticCost');
        Route::post('ongkir/domestic-costs', 'Gateway\RajaOngkir\RajaOngkirGateController@getDomesticCosts');
        Route::post('ongkir/international-cost', 'Gateway\RajaOngkir\RajaOngkirGateController@getInternationalCost');
        Route::get('cities', 'Gateway\RajaOngkir\RajaOngkirGateController@searchCity');
        Route::get('districts', 'Gateway\RajaOngkir\RajaOngkirGateController@searchDistrict');
        Route::get('carriers', 'Gateway\RajaOngkir\RajaOngkirGateController@getCarrierList');
        Route::get('countries/nested_list', 'Gateway\Asset\Pub\AssetCountryPubGateController@nestedList');
        Route::get('countries/list', 'Gateway\Asset\Pub\AssetCountryPubGateController@list');
        Route::get('provinces/list', 'Gateway\Asset\Pub\AssetProvincePubGateController@list');
        Route::get('districts/list', 'Gateway\Asset\Pub\AssetDistrictPubGateController@list');
        Route::get('regions/list', 'Gateway\Asset\Pub\AssetRegionPubGateController@list');
        Route::post('check-waybill', 'Gateway\RajaOngkir\RajaOngkirGateController@getWaybill');
    });

#login
Route::post('/oauth/token', [
    'uses' => 'Auth\AccessTokenController@issueUserToken',
    'middleware' => 'throttle'
]);

#google Login
Route::post('/oauth/social', [
    'uses' => 'Auth\AccessTokenController@handleSocialLogin',
    'middleware' => 'throttle'
]);

Route::get('/register/check_avail_portal', 'OrganizationController@checkNameAvailability');
Route::post('/register', 'Auth\RegisterController@register');
Route::post('/register/check_avail_email', 'UserController@checkAvailabilityEmailInApp');
Route::post('/register/organization', 'Auth\RegisterController@registerOrganization');
Route::post('/register/resend_verification_email', 'Auth\RegisterController@resendVerificationEmail');
Route::post('/password/request_reset', 'Auth\ForgotPasswordController@getResetToken');
Route::post('/logout', 'Auth\LoginController@logout');
//        Route::get('/invites/accept/{token}', 'Auth\LoginController@logout');

Route::group([
    'middleware' => ['auth:api', 'org']],
    function () {

        Route::get('/ping', function () {
            return 'pong.';
        });

        //PROFILE
        Route::get('/profile', 'UserController@getMyProfile');
        Route::get('/profile/edit', 'UserController@edit');
        Route::put('/profile/update', 'UserController@updateMyProfile');
        Route::post('/profile/upload_photo', 'UserController@uploadPhoto');
        Route::delete('/profile/remove_photo', 'UserController@removeMyPhoto');
        Route::post('/profile/change_password', 'UserController@changeMyPassword');

        Route::get('/me/organizations',
            'UserController@getMyOrganizations');

        Route::get('/me/organizations/users',
            'OrganizationController@getUsersInMyOrganization');

        Route::post('/me/organizations/users/{id}/set-status/{status}',
            'OrganizationController@setUserStatusInMyOrganization');

//                Route::get('/me/organizations/invites',
//                    'OrganizationUserInvitationController@invitedUserInOrganization');

        Route::post('/me/organizations/invites',
            'OrganizationUserInvitationController@inviteUserToOrganization');

        Route::post('/me/organizations/invites/{id}/resend_verification_email',
            'OrganizationUserInvitationController@resendInvitationEmail');

        //ORGANIZATION
//                Route::get('organizations', 'OrganizationController@index');
        Route::get('organizations/create', 'OrganizationController@create');
        Route::get('organizations/{organization}', 'OrganizationController@show');
        Route::get('organizations/{organization}/edit', 'OrganizationController@edit');
//                Route::post('organizations', 'OrganizationController@store');
        Route::put('organizations/{organization}', 'OrganizationController@update');
//                Route::delete('organizations/{organization}', 'OrganizationController@destroy');
        Route::put('/organizations/current/update', 'OrganizationController@updatePrimaryOrganization');
        Route::post('/organizations/{organization}/upload_logo', 'OrganizationController@uploadLogo');
        Route::delete('/organizations/{organization}/remove_logo', 'OrganizationController@removeLogo');
        Route::get('/organizations/{organization}/contacts', 'OrganizationContactController@index');
        Route::get('/organizations/{organization}/contacts/{id}', 'OrganizationContactController@show');
        Route::post('/organizations/{organization}/contacts', 'OrganizationContactController@store');
        Route::put('/organizations/{organization}/contacts/{id}', 'OrganizationContactController@update');
        Route::delete('/organizations/{organization}/contacts/{id}',
            'OrganizationContactController@destroy');
        Route::get('/organizations/{organization}/contacts/set_primary/{id}',
            'OrganizationContactController@setPrimary');
        Route::get('/organizations/{organization}/contacts/resend_verification/{id}',
            'OrganizationContactController@resendVerification');
    });

//use authToken
Route::group([
    'middleware' => 'authToken',
    'prefix' => 'sys'
], function () {

    Route::group(['prefix' => 'users'], function () {
        Route::get('', 'UserController@indexAdmin');
        Route::get('/list', 'UserController@list');
    });

    Route::group(['prefix' => 'organizations'], function () {
        Route::get('', 'OrganizationController@indexAdmin');
        // Route::get('', 'OrganizationController@index');
        Route::get('/list', 'OrganizationController@list');
    });

});
