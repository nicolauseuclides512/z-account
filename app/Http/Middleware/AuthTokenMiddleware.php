<?php

namespace App\Http\Middleware;

use App\Cores\Jsonable;
use App\Cores\AuthToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class AuthTokenMiddleware
{
    use Jsonable;

    protected $authToken;

    public function __construct(AuthToken $authToken)
    {
        $this->authToken = $authToken;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @internal param AuthToken $authToken
     */
    public function handle(Request $request, Closure $next)
    {
        try {

            if (env('APP_ENV' !== 'production')) {
//                Log::info('Bearer ' . $request->bearerToken() . ' status ' . $this->authToken->verify($request->bearerToken()));
            }

            if ($this->authToken->verify($request->bearerToken())) {
                return $next($request);
            }

            Log::error('Unauthorized Token.');
            return $this->json(Response::HTTP_BAD_REQUEST, 'Unauthorized.');
        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }
}
