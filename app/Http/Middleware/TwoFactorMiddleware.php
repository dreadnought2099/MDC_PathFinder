<?php

namespace App\Http\Middleware;

use Closure;

class TwoFactorMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if ($user && $user->google2fa_secret && !session()->has('2fa_passed')) {

            // Allow only 2FA verification routes and logout
            $allowed = [
                'admin/2fa/verify*',
                'admin/2fa/recovery*',
                'logout'
            ];

            foreach ($allowed as $route) {
                if ($request->is($route)) {
                    return $next($request);
                }
            }

            // Redirect everything else to 2FA verify page
            return redirect()->route('admin.2fa.showVerifyForm')
                ->with('show_2fa_modal', true)
                ->withErrors(['otp' => 'Please verify 2FA before continuing.']);
        }

        return $next($request);
    }
}
