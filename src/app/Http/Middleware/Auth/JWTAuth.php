<?php

namespace App\Http\Middleware\Auth;

use App\Contracts\Auth\AuthJwtInterface;
use App\Contracts\User\UserInterface;
use App\Http\Middleware\Middleware;
use Closure;
use Exception;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class JWTAuth extends Middleware
{

    protected $except = [
        'auth/login' => ['POST', 'OPTIONS'],
        'auth/facebook/login' => ['POST', 'OPTIONS'],
        'auth/google/login' => ['POST', 'OPTIONS'],
        'auth/twitter/login' => ['POST', 'OPTIONS'],
        'user' => ['POST', 'OPTIONS'],
        'send-notification' => ['GET'],
        'passwords/reset' => ['POST'],
    ];

    public function __construct(AuthJwtInterface $auth, UserInterface $user)
    {
        $this->auth = $auth;
        $this->user = $user;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (!$this->routeExcluded($request->path(), $request->method())
            && ! $request->isMethod('options')
            && ! preg_match('/files\/image\/(profile_photo|cover_photo)\/(\d+)/', $request->path())
        )
        {
            try {

                $decodedJWT = $this->auth->validate($request);

                Auth::loginUsingId($decodedJWT->data->userId);

            } catch (Exception $e) {

                throw new HttpException(403, 'Authorization failed', $e);

            }
        }

        return $next($request);

    }

}
