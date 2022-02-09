<?php

namespace App\Http\Middleware;

use App\Cores\Jsonable;
use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class VerifyPortal
{
    use Jsonable;

    public function handle(Request $request, Closure $next)
    {
        if ($request->hasHeader('X-Header-Portal')) {
            $portal = $request->header('X-Header-Portal');
            if ($portal) {
                $org = Organization::getOrgUserByPortal($portal);

                if ($org) {
                    Auth::login($org->organizationUser[0]->user);
                    Auth::user()->setCurrentOrganization($org->id);
                    return $next($request);
                }
            }
        }

        return $this->json(
            Response::HTTP_UNAUTHORIZED,
            trans('messages.no_access'));
    }
}
