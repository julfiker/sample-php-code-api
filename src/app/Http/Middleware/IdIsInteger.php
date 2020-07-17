<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class IdIsInteger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if ($request->route('id') && !is_numeric($request->route('id')))
        {
            throw new NotFoundHttpException('Not found...');
        }

        return $next($request);
    }
}
