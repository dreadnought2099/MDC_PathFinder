<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TwoFactorController extends Controller
{
    public function showEnablePage()
    {
        $user = auth()->user();

        if ($user->google2fa_secret) {
            return view('pages.admin.profile.index', ['enabled' => true]);
        }

        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        // Save secret temporarily until confirmed
        session(['2fa_secret' => $secret]);

        $qrText = $google2fa->getQRCodeUrl(
            config('app.name'), // App name
            $user->email,       // User identifier
            $secret
        );

        // Generate QR with Simple QrCode
        $qrCode = QrCode::size(200)->generate($qrText);

        return view('pages.admin.profile.index', [
            'enabled' => false,
            'qrCode' => $qrCode,
            'secret' => $secret,
        ]);
    }

    // Enable 2FA (confirm OTP)
    public function enable(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $google2fa = new Google2FA();
        $secret = session('2fa_secret'); // only session secret is used
        $user = auth()->user();

        if (!$secret) {
            return back()->withErrors(['otp' => 'No secret found. Try regenerating QR.']);
        }

        if ($google2fa->verifyKey($secret, $request->otp)) {
            $user->google2fa_secret = $secret;
            $user->save();

            session()->forget('2fa_secret');

            return back()->with('success', 'Two-Factor Authentication enabled successfully.');
        }

        return back()->withErrors(['otp' => 'Invalid verification code.']);
    }

    // Disable 2FA
    public function disable()
    {
        $user = auth()->user();
        $user->google2fa_secret = null;
        $user->save();

        return back()->with('success', 'Two-Factor Authentication disabled.');
    }

    // Regenerate QR (but do not save yet)
    public function regenerate()
    {
        $google2fa = new Google2FA();
        $user = auth()->user();

        $secret = $google2fa->generateSecretKey();
        session(['2fa_secret' => $secret]);

        $qrText = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        $qrCode = QrCode::size(200)->generate($qrText);

        return back()->with([
            'qrCode' => $qrCode,
            'secret' => $secret,
            'message' => 'Scan the new QR code to reconfigure your authenticator app.'
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $google2fa = new Google2FA();
        $user = auth()->user();

        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->otp);

        if ($valid) {
            session(['2fa_passed' => true]);
            session()->forget('show_2fa_modal');

            // AJAX request → return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => '2FA verified successfully.',
                ]);
            }

            // Fallback: normal POST → redirect
            return redirect()->route('admin.dashboard')
                ->with('success', '2FA verified successfully.');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid code. Please try again.',
            ], 422);
        }

        return back()->withErrors(['otp' => 'Invalid code. Please try again.']);
    }
}
