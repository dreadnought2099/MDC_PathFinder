<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
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
        $key = 'feedback_limit_' . hash('sha256', $ip . config('app.key'));

        $maxAttempts = 5;
        $decayMinutes = 60; // 1 hour

        // Use Cache::lock for atomic operations to prevent race conditions
        $lock = Cache::lock($key . '_lock', 10);

        try {
            // Try to acquire lock
            if (!$lock->block(5)) {
                return $this->rateLimitResponse($request, 'System busy, please try again.');
            }

            // Get current attempts
            $attempts = (int) Cache::get($key, 0);
            $expiresAt = Cache::get($key . '_expires');

            // Check if rate limit window has expired
            if ($expiresAt && now()->greaterThan($expiresAt)) {
                // Reset counter
                Cache::forget($key);
                Cache::forget($key . '_expires');
                $attempts = 0;
            }

            // Check if limit exceeded
            if ($attempts >= $maxAttempts) {
                $minutesLeft = $expiresAt
                    ? max(1, now()->diffInMinutes($expiresAt, false))
                    : $decayMinutes;

                Log::warning('Feedback rate limit exceeded', [
                    'ip' => $ip,
                    'attempts' => $attempts,
                    'minutes_left' => $minutesLeft
                ]);

                return $this->rateLimitResponse(
                    $request,
                    "Too many feedback submissions. Please try again in {$minutesLeft} minute" .
                        ($minutesLeft != 1 ? 's' : '') . "."
                );
            }

            // Increment attempts
            $newAttempts = $attempts + 1;
            $expiresAt = $expiresAt ?? now()->addMinutes($decayMinutes);

            Cache::put($key, $newAttempts, $expiresAt);
            Cache::put($key . '_expires', $expiresAt, $expiresAt);

            // Add remaining attempts to response headers
            $response = $next($request);
            $response->headers->set('X-RateLimit-Limit', $maxAttempts);
            $response->headers->set('X-RateLimit-Remaining', max(0, $maxAttempts - $newAttempts));

            return $response;
        } finally {
            // Always release the lock
            optional($lock)->release();
        }
    }

    /**
     * Generate rate limit response
     */
    private function rateLimitResponse(Request $request, string $message): Response
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message
            ], 429);
        }

        return redirect()->back()
            ->withInput()
            ->with('error', $message);
    }
}