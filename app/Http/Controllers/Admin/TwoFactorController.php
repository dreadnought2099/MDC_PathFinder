<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function enable(Request $request)
    {
        $user = Auth::user();
        $google2fa = new Google2FA();

        // Generate a secret
        $secret = $google2fa->generateSecretKey();

        // Save it
        $user->google2fa_secret = $secret;
        $user->save();

        // Generate QR code URL
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return view('profile.2fa.enable', compact('qrCodeUrl', 'secret'));
    }

    public function disable()
    {
        $user = Auth::user();
        $user->google2fa_secret = null;
        $user->save();

        return redirect()->back()->with('status', 'Two-Factor Authentication disabled.');
    }
}
