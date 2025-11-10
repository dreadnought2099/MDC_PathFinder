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
        $attemptsKey = $key . '_attempts';
        $firstAttemptKey = $key . '_first';

        $maxAttempts = 5;
        $decayMinutes = 60; // 1 hour

        $attempts = Cache::get($attemptsKey, 0);
        $firstAttemptTime = Cache::get($firstAttemptKey);

        if ($attempts >= $maxAttempts) {
            $minutesLeft = $decayMinutes - now()->diffInMinutes($firstAttemptTime);
            return back()->with('error', "Too many feedback submissions. Please try again in {$minutesLeft} minutes.");
        }

        // Store first attempt time if this is the first attempt
        if ($attempts === 0) {
            Cache::put($firstAttemptKey, now(), now()->addMinutes($decayMinutes));
        }

        Cache::put($attemptsKey, $attempts + 1, now()->addMinutes($decayMinutes));

        return $next($request);
    }
}
