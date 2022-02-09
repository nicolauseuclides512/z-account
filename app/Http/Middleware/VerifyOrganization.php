<?php

namespace App\Http\Middleware;

use App\Cores\Jsonable;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class VerifyOrganization
{
    use Jsonable;

    public function handle(Request $request, Closure $next)
    {
        $route = Route::current();

        if ($this->_check($request->header('X-Header-Organization-Id'))) {
            if ($route->getParameter('organization')) {
                if ($this->_check($route->getParameter('organization')))
                    return $next($request);
            } else
                return $next($request);
        }

        return $this->json(400, trans('messages.no_access'));
    }

    private function _check($id)
    {
        if (Auth::User()->hasOrganization($id)) {
            //set current organization
            Auth::User()->setCurrentOrganization($id);
            return true;
        }

        return false;
    }
}
