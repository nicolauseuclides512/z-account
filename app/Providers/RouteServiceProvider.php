<?php

namespace App\Providers;

//Hack php version compatibility
if (version_compare(PHP_VERSION, '7.2.0', '>=')) {
// Ignores notices and reports all other kinds... and warnings
    //   error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
    error_reporting(E_ALL ^ E_WARNING); // Maybe this is enough
}

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;


class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapStoreApiGatewayRoutes();
        $this->mapStoreApiGatewayRoutesOpen();

        $this->mapAssetApiGatewayRoutes();

        $this->mapRajaOngkirApiGatewayRoutes();

        $this->mapReportApiGatewayRoutes();

        $this->mapOngkirApiGatewayRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/web.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::group([
            'middleware' => ['api', 'cors'],
            'namespace' => $this->namespace,
            'prefix' => 'api/v1',
        ], function ($router) {
            require base_path('routes/api.php');
        });

        Route::group([
            'middleware' => ['api', 'cors'],
            'namespace' => $this->namespace,
            'prefix' => 'api/v2',
        ], function ($router) {
            require base_path('routes/v2/api.php');
        });

        //v3
        Route::group([
            'namespace' => $this->namespace,
            'middleware' => ['api', 'cors'],
            'prefix' => 'api/v3',
        ], function ($router) {
            require base_path('routes/v3/api.php');
        });
    }

    protected function mapStoreApiGatewayRoutes()
    {
        Route::group([
            'middleware' => ['api', 'cors', 'auth:api', 'org'],
            'namespace' => $this->namespace,
            'prefix' => 'api',
        ], function ($router) {
            require base_path('routes/gateway/storeapirouter.php');
        });

        Route::group([
            'middleware' => ['api', 'cors', 'auth:api', 'org'],
            'namespace' => $this->namespace,
            'prefix' => 'api/v2',
        ], function ($router) {
            require base_path('routes/v2/gateway/storeapirouter.php');
        });

        //v3
        Route::group([
            'middleware' => ['api', 'cors', 'auth:api', 'org'],
            'namespace' => $this->namespace,
            'prefix' => 'api/v3',
        ], function ($router) {
            require base_path('routes/v3/gateway/storeapirouter.php');
        });
    }

    protected function mapStoreApiGatewayRoutesOpen()
    {
        Route::group([
            'middleware' => ['api', 'cors'],
            'namespace' => $this->namespace,
            'prefix' => 'api/v2',
        ], function ($router) {
            require base_path('routes/v2/open/storeapirouter.php');
        });
    }

    protected function mapAssetApiGatewayRoutes()
    {
        Route::group([
            'middleware' => ['api', 'auth:api', 'cors', 'org'],
            'namespace' => $this->namespace,
            'prefix' => 'api/' . env('APP_VERSION', 'v1'),
        ], function ($router) {
            require base_path('routes/gateway/assetapirouter.php');
        });

        Route::group([
            'middleware' => ['api', 'auth:api', 'cors', 'org'],
            'namespace' => $this->namespace,
            'prefix' => 'api/v2',
        ], function ($router) {
            require base_path('routes/v2/gateway/assetapirouter.php');
        });

        //v3
        Route::group([
            'middleware' => ['api', 'auth:api', 'cors', 'org'],
            'namespace' => $this->namespace,
            'prefix' => 'api/v3',
        ], function ($router) {
            require base_path('routes/v3/gateway/assetapirouter.php');
        });




    }

    protected function mapReportApiGatewayRoutes()
    {
        Route::group([
            'middleware' => ['api', 'auth:api', 'cors', 'org'],
            'namespace' => $this->namespace,
            'prefix' => 'api/' . env('APP_VERSION', 'v1'),
        ], function ($router) {
            require base_path('routes/gateway/reportapirouter.php');
        });

        Route::group([
            'middleware' => ['api', 'auth:api', 'cors', 'org'],
            'namespace' => $this->namespace,
            'prefix' => 'api/v2',
        ], function ($router) {
            require base_path('routes/v2/gateway/reportapirouter.php');
        });

        //v3
        Route::group([
            'middleware' => ['api', 'auth:api', 'cors', 'org'],
            'namespace' => $this->namespace,
            'prefix' => 'api/v3',
        ], function ($router) {
            require base_path('routes/v3/gateway/reportapirouter.php');
        });
    }

    protected function mapOngkirApiGatewayRoutes()
    {
        Route::group([
            'middleware' => ['api', 'auth:api', 'cors', 'org'],
            'namespace' => $this->namespace,
            'prefix' => 'api/' . env('APP_VERSION', 'v1'),
        ], function ($router) {
            require base_path('routes/gateway/ongkirapirouter.php');
        });

        Route::group([
            'middleware' => ['api', 'auth:api', 'cors', 'org'],
            'namespace' => $this->namespace,
            'prefix' => 'api/v2',
        ], function ($router) {
            require base_path('routes/v2/gateway/ongkirapirouter.php');
        });

        //v3
        Route::group([
            'middleware' => ['api', 'auth:api', 'cors', 'org'],
            'namespace' => $this->namespace,
            'prefix' => 'api/v3',
        ], function ($router) {
            require base_path('routes/v3/gateway/ongkirapirouter.php');
        });
    }

    /**deprecated*/
    protected function mapRajaOngkirApiGatewayRoutes()
    {
        Route::group([
            'middleware' => ['api', 'auth:api', 'cors', 'org'],
            'namespace' => $this->namespace,
            'prefix' => 'api/' . env('APP_VERSION', 'v1'),
        ], function ($router) {
            require base_path('routes/gateway/rajaongkirapirouter.php');
        });
    }
}
