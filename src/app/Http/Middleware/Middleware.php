<?php  namespace App\Http\Middleware; 

use Closure;
use Illuminate\Http\Request;

class Middleware {

    use MiddlewareRoutes;

    protected $except = [];

}