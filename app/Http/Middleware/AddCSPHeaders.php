<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddCSPHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $csp = "
            default-src 'self';
            script-src 'self' https://www.google.com/recaptcha/ https://www.gstatic.com/recaptcha/ 'unsafe-inline';
            frame-src https://www.google.com/recaptcha/;
            connect-src https://www.google.com/recaptcha/ https://www.gstatic.com/recaptcha/;
        ";

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
