<?php

Route::get('/', function () {
    return 'Zuragan Gateway API V3';
});

Route::get('/lang-data', 'HomeController@langData');

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

Route::group([
    'middleware' => ['auth:api', 'org']],
    function () {

        Route::get('/ping', function () {
            return 'pong.';
        });

        //PROFILE
        Route::get('/profile', 'UserController@getMyProfile')
            ->middleware('can:account-read-profile-user');

        Route::get('/profile/edit', 'UserController@edit')
            ->middleware('can:account-update-profile-user');

        Route::put('/profile/update', 'UserController@updateMyProfile')
            ->middleware('can:account-update-profile-user');

        Route::post('/profile/upload_photo', 'UserController@uploadPhoto')
            ->middleware('can:account-upload-photo-profile-user');

        Route::delete('/profile/remove_photo', 'UserController@removeMyPhoto')
            ->middleware('can:account-remove-photo-profile-user');

        Route::post('/profile/change_password', 'UserController@changeMyPassword')
            ->middleware('can:account-change-password-profile-user');

        Route::get('/me/organizations',
            'OrganizationController@getMyOrganizations')
            ->middleware('can:account-read-my-organization-user');

        Route::get('/me/organizations/users',
            'OrganizationController@getUsersInMyOrganization')
            ->middleware('can:account-read-users-my-organization-user');

        Route::post('/me/organizations/users/{id}/set-status/{status}',
            'OrganizationController@setUserStatusInMyOrganization')
            ->middleware('can:account-set-user-status-in-my-organization-user');

        Route::get('/me/organizations/{id}/switch',
            'OrganizationController@switchOrganization')
            ->middleware('can:account-switch-organization-user');

        Route::get('/me/organizations/invites',
            'OrganizationUserInvitationController@fetchInvitedUserInOrganization')
            ->middleware('can:account-read-invited-user-my-organization-user');

        Route::post('/me/organizations/invites',
            'OrganizationUserInvitationController@inviteUserToOrganization')
            ->middleware('can:account-invite-user-to-my-organization-user');

        Route::post('/me/organizations/invites/{id}/resend_verification_email',
            'OrganizationUserInvitationController@resendInvitationEmail')
            ->middleware('can:account-invite-resend-email-my-organization-user');


        //ORGANIZATION
//        Route::get('organizations', 'OrganizationController@index')
//            ->middleware('can:account-read-organization');

        Route::get('organizations/create', 'OrganizationController@create')
            ->middleware('can:account-store-organization');

        Route::get('organizations/{organization}', 'OrganizationController@show')
            ->middleware('can:account-read-detail-organization');

        Route::get('organizations/{organization}/edit', 'OrganizationController@edit')
            ->middleware('can:account-update-organization');

        Route::post('organizations', 'OrganizationController@store')
            ->middleware('can:account-store-organization');

        Route::put('organizations/{organization}', 'OrganizationController@update')
            ->middleware('can:account-read-detail-organization');

//        Route::delete('organizations/{organization}', 'OrganizationController@destroy')
//            ->middleware('can:asset-destroy-currency');

        Route::put('/organizations/current/update', 'OrganizationController@updatePrimaryOrganization')
            ->middleware('can:account-update-primary-organization');

        Route::post('/organizations/{organization}/upload_logo', 'OrganizationController@uploadLogo')
            ->middleware('can:account-upload-logo-organization');

        Route::delete('/organizations/{organization}/remove_logo', 'OrganizationController@removeLogo')
            ->middleware('can:account-remove-logo-organization');

        Route::get('/organizations/{organization}/contacts', 'OrganizationContactController@index')
            ->middleware('can:account-read-contact-organization');

        Route::get('/organizations/{organization}/contacts/{id}', 'OrganizationContactController@show')
            ->middleware('can:account-read-contact-detail-organization');

        Route::post('/organizations/{organization}/contacts', 'OrganizationContactController@store')
            ->middleware('can:account-store-contact-organization');

        Route::put('/organizations/{organization}/contacts/{id}', 'OrganizationContactController@update')
            ->middleware('can:account-update-contact-organization');

        Route::delete('/organizations/{organization}/contacts/{id}',
            'OrganizationContactController@destroy')
            ->middleware('can:account-destroy-contact-organization');

        Route::get('/organizations/{organization}/contacts/set_primary/{id}',
            'OrganizationContactController@setPrimary')
            ->middleware('can:account-set-primary-contact-organization');

        Route::get('/organizations/{organization}/contacts/resend_verification/{id}',
            'OrganizationContactController@resendVerification')
            ->middleware('can:account-resend-verification-contact-organization');

    });

//for admin
Route::group([
    'middleware' => 'authToken',
    'prefix' => 'sys'
], function () {
    Route::group(['prefix' => 'users'], function () {
        Route::get('', 'UserController@indexAdmin');
        Route::get('/list', 'UserController@list');
    });

    Route::group(['prefix' => 'organizations'], function () {
        Route::get('', 'OrganizationController@index');
        Route::get('/list', 'OrganizationController@list');
    });

});


