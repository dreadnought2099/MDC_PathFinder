<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class RateLimitFeedback
{
    /**
     * Handle an incoming request.
     * Limit: 5 feedback submissions per hour per IP
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $key = 'feedback_limit_' . md5($ip);
        $maxAttempts = 5;
        $decayMinutes = 60; // 1 hour

        $attempts = Cache::get($key, 0);

        if ($attempts >= $maxAttempts) {
            return back()->with('error', 'Too many feedback submissions. Please try again later.');
        }

        Cache::put($key, $attempts + 1, now()->addMinutes($decayMinutes));

        return $next($request);
    }
}
