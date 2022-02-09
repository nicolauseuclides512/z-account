<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Credential' => 'true',
            'Access-Control-Allow-Methods' => 'POST, PUT, DELETE, OPTIONS, HEAD, GET',
            'Access-Control-Allow-Headers' => 'Content-type, Origin, Accept, Authorization, X-Header-Organization-Id, X-Header-Portal, X-localization'
        ];

        if ($request->getMethod() == "OPTIONS") {
            $response = new Response();
            foreach ($headers as $key => $value)
                $response->headers->set($key, $value);

            return $response;
        }

        $response = $next($request);

        foreach ($headers as $key => $value)
            $response->headers->set($key, $value);

        return $response;
    }
}
