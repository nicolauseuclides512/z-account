<?php

namespace App\Http\Middleware;

use Closure;

class Localization
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
        //define header X-localization
        $local = $request->hasHeader('X-localization')
            ? $request->header('X-localization')
            : 'id';

        // set localization
        app()->setLocale($local);
        // continue request
        return $next($request);
    }
}
