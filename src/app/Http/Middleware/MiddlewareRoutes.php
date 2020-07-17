<?php  namespace App\Http\Middleware; 

use Closure;

trait MiddlewareRoutes {

    protected function routeExcluded($routePath, $routeMethod)
    {

        if (
            array_key_exists($routePath, $this->except) &&
            in_array($routeMethod, $this->except[$routePath]))
        {
            return true;
        }

        return false;
    }

}