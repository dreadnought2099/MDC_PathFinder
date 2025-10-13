<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ResetPasswordController extends Controller
{
    /**
     * Show "Forgot Password" form
     */
    public function showRequestForm()
    {
        return view('pages.admin.auth.forgot-password');
    }

    /**
     * Handle sending of reset email
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Validate email
        $request->validate([
            'email' => 'required|email',
        ]);

        // Find the user
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->with('error', 'We could not find a user with that email.');
        }

        // Generate a token
        $token = Str::random(64);

        // Store or update the reset token
        DB::table('reset_password')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $token,
                'created_at' => Carbon::now(),
            ]
        );

        // Generate reset link using named route
        $resetLink = route('password.reset', ['token' => $token]);

        // Send the email
        Mail::send('pages.admin.auth.reset-email', [
            'user' => $user,
            'resetLink' => $resetLink,
        ], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Password Reset Request');
        });

        return back()->with('success', 'We have emailed your password reset link!');
    }

    /**
     * Show the password reset form
     */
    public function showResetForm(Request $request, $token)
    {
        $email = $request->query('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request')->withErrors(['email' => 'Invalid or expired password reset link.']);
        }

        return view('pages.admin.auth.reset-password', compact('token', 'email', 'user'));
    }

    /**
     * Handle actual password reset
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $reset = DB::table('reset_password')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            return back()->with('error', 'Invalid or expired token.');
        }

        $user = User::where('email', $reset->email)->first();

        if (!$user) {
            return back()->with('error', 'We could not find a user with that email.');
        }

        $user->update([
            'password' => Hash::make($request->password),
            'google2fa_secret' => null,
            'two_factor_recovery_codes' => null,
        ]);

        DB::table('reset_password')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Your password has been reset!');
    }
}
