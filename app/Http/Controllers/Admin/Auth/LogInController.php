<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogInController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        // If already logged in, go straight to dashboard
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('pages.admin.auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required',
            'password' => 'required'
        ]);

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Find user first
        $user = User::where($loginField, $request->login)->first();

        if (!$user) {
            return back()->with('error', 'Invalid credentials.');
        }

        if (!$user->is_active) {
            return back()->with('error', 'Your account has been disabled.');
        }

        // Prepare credentials without is_active
        $credentials = [
            $loginField => $request->login,
            'password' => $request->password,
        ];

        if (!Auth::attempt($credentials)) {
            return back()->with('error', 'Invalid credentials.');
        }

        $request->session()->regenerate();

        $userName = $user->name ?? $user->username;

        return redirect()->route('admin.dashboard')
            ->with('success', "Login successful! Welcome, {$userName}.");
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Just in case, explicitly clear 2FA flags
        session()->forget(['2fa_passed', 'show_2fa_modal', '2fa_attempts', 'recovery_codes', '2fa_secret']);

        return redirect('/admin');
    }

    /**
     * Custom credentials logic
     * This ensures we always check "is_active = true"
     */
    public function credentials(Request $request)
    {
        // Check if input is email or username
        $field = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        return [
            $field     => $request->input('login'),
            'password' => $request->input('password'),
            'is_active' => true,
        ];
    }
}
