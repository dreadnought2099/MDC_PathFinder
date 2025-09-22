<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckHttpMethod
{
    public function handle($request, Closure $next)
    {
        $route = $request->route();

        if ($route) {
            $allowedMethods = $route->methods(); // GET, POST, PUT, PATCH, DELETE

            if (!in_array($request->method(), $allowedMethods)) {
                // Show a custom 405 error page
                abort(405, 'Method not allowed.');
            }
        }

        return $next($request);
    }
}
