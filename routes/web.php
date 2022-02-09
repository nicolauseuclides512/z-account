<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

//Hack php version compatibility
if (version_compare(PHP_VERSION, '7.2.0', '>=')) {
// Ignores notices and reports all other kinds... and warnings
  //  error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
    error_reporting(E_ALL ^ E_WARNING); // Maybe this is enough
}

use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    return 'Zuragan Account ' . env('APP_VERSION');
});

Route::get('/verify_contact', 'OrganizationContactController@verification');
Route::get('/register/verification', 'Auth\RegisterController@verifyUser');
Route::get('/password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
Route::get('/invites/accept/{token}', 'OrganizationUserInvitationController@verifyInvitation');
Route::post('/password/reset', 'Auth\ResetPasswordController@reset');
Route::get('/messages', 'AppPageController@message');

Auth::routes();

Route::group(['prefix' => 'admin', 'middleware' => 'auth'],
    function () {
        Route::get('/home', 'HomeController@index');
        Route::get('/users', 'UserController@index');
        Route::get('/users/{id}', 'UserController@show');
        Route::get('/users/organizations/{id}', 'UserController@getByOrganizationId');
        Route::get('/organizations', 'OrganizationController@index');
        Route::get('/organizations/{id}', 'OrganizationController@show');
        Route::get('/organizations/users/{id}', 'OrganizationController@getByuserId');
        Route::get('/organizations/districts-by-province-id/{id}', 'OrganizationController@getDistrictByProvinceId');
        Route::get('/organizations/regions-by-province-id/{id}', 'OrganizationController@getRegionByDistrictId');
    });

Route::get('/test/mail', function (\Illuminate\Http\Request $request) {
    Illuminate\Support\Facades\Mail::to($request->get('email'))->send(new App\Mail\Main());
});

Route::get('/auth/google/login', function () {
    return view('pages.google');
});

Route::get('/auth/stateless', function () {
    return Socialite::driver('google')->stateless()->redirect();
});






