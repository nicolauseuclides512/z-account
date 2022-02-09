<?php

namespace App\Providers;

//Hack php version compatibility
if (version_compare(PHP_VERSION, '7.2.0', '>=')) {
// Ignores notices and reports all other kinds... and warnings
//    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
    error_reporting(E_ALL ^ E_WARNING); // Maybe this is enough
}

use App\Models\Permission;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @param GateContract $gate
     * @param Request $request
     * @return void
     */
    public function boot(GateContract $gate, Request $request)
    {
        $this->registerPolicies($gate);

        Route::group([
            'middleware' => 'cors',
            'prefix' => 'api/' . env('APP_VERSION', 'v1')
        ], function () {
            Passport::routes();
        });

        Passport::tokensCan([]);
        Passport::tokensExpireIn(Carbon::now()->addDays(15));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));

        //Dynamically register permissions with Laravel's Gate.
        foreach ($this->getPermissions() as $permission) {
            $gate->define($permission->name, function ($user) use ($permission, $request) {
                $user->setCurrentOrganization($request->header('X-Header-Organization-Id'));
                return $user->hasPermissionInOrganization($permission);
            });
        }
    }

    protected function getPermissions()
    {
        return Permission::with(['roles'])->get();
    }
}
