<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if ($user && $user->google2fa_secret && !session()->has('2fa_passed')) {
            // Allow request, but show modal
            session(['show_2fa_modal' => true]);
        }

        return $next($request);
    }
}
