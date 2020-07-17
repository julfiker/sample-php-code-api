<?php

namespace App\Http\Middleware;
use Closure;

class AddHeaders extends Middleware {

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        return $response
            ->header('Access-Control-Allow-Origin' , '*')
            ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
            ->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization, X-Requested-With')
            ->header('Access-Control-Expose-Headers', 'X-Auth-Token, Authorization')
            ->header('Access-Control-Max-Age', '3600')
            ;
    }
}
