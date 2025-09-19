<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
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

        session(['2fa_secret' => $secret]);

        $qrText = $google2fa->getQRCodeUrl(config('app.name'), $user->email, $secret);
        $qrCode = QrCode::size(200)->generate($qrText);

        return view('pages.admin.profile.index', [
            'enabled' => false,
            'qrCode'  => $qrCode,
            'secret'  => $secret,
        ]);
    }

    /**
     * Enable 2FA (confirm OTP and generate recovery codes).
     */
    public function enable(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $google2fa = new Google2FA();
        $secret    = session('2fa_secret');
        $user      = auth()->user();

        if (! $secret) {
            return back()->withErrors(['otp' => 'No secret found. Try regenerating QR.']);
        }

        // Verify OTP
        if ($google2fa->verifyKey($secret, $request->otp)) {
            // Save secret to user
            $user->google2fa_secret = $secret;

            // Generate recovery codes automatically
            $recoveryCodes = $user->generateRecoveryCodes();

            $user->save();

            // Clear the temporary secret from session
            session()->forget('2fa_secret');

            // IMPORTANT: Mark 2FA as passed for this session to avoid modal
            session(['2fa_passed' => true]);

            // Clear any 2FA modal flags
            session()->forget('show_2fa_modal');

            // Store recovery codes in session (so we can show once)
            session(['recovery_codes' => $recoveryCodes]);

            return redirect()->route('admin.profile')
                ->with('success', 'Two-Factor Authentication enabled successfully! Save your recovery codes.');
        }

        return back()->withErrors(['otp' => 'Invalid verification code.']);
    }

    /**
     * Disable 2FA and clear recovery codes.
     */
    public function disable()
    {
        $user = auth()->user();
        $user->google2fa_secret = null;
        $user->clearRecoveryCodes();
        $user->save();

        // Clear any 2FA session flags for safety
        session()->forget(['2fa_passed', 'show_2fa_modal', 'recovery_codes', '2fa_secret']);

        return back()->with('success', 'Two-Factor Authentication disabled.');
    }

    /**
     * Regenerate QR (before confirming 2FA).
     */
    public function regenerate()
    {
        $google2fa = new Google2FA();
        $user = auth()->user();

        $secret = $google2fa->generateSecretKey();
        session(['2fa_secret' => $secret]);

        $qrText = $google2fa->getQRCodeUrl(config('app.name'), $user->email, $secret);
        $qrCode = QrCode::size(200)->generate($qrText);

        // Use session so the profile page can display it
        session(['qrCode' => $qrCode, 'secret' => $secret]);

        return back()->with('message', 'Scan the new QR code to reconfigure your authenticator app.');
    }

    public function verify(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $user = auth()->user();
        $google2fa = new Google2FA();

        if ($google2fa->verifyKey($user->google2fa_secret, $request->otp)) {
            session(['2fa_passed' => true]);
            session()->forget('show_2fa_modal');

            return redirect()->route('admin.dashboard')->with('success', '2FA verified successfully.');
        }

        return back()->withErrors(['otp' => 'Invalid code. Please try again.']);
    }


    public function verifyRecoveryCode(Request $request)
    {
        $request->validate(['recovery_code' => 'required|string']);

        $user = auth()->user();

        if ($user->verifyRecoveryCode($request->recovery_code)) {
            session(['2fa_passed' => true]);
            session()->forget('show_2fa_modal');

            return redirect()->route('admin.dashboard')->with('success', 'Recovery successful.');
        }
        
        // Failure: make sure recovery modal opens, NOT OTP
        session()->flash('show_recovery_modal', true);

        return back()->withErrors(['recovery_code' => 'Invalid or expired recovery code.']);
    }


    /**
     * Download the one-time plain recovery codes (only right after generation).
     */
    public function downloadRecoveryCodes()
    {
        $codes = session('recovery_codes');

        if (!$codes || empty($codes)) {
            return redirect()->route('admin.profile')
                ->with('error', 'No recovery codes available. Please regenerate recovery codes.');
        }

        $content = "Your 2FA Recovery Codes for " . config('app.name') . "\n";
        $content .= "Generated on: " . now()->format('Y-m-d H:i:s') . "\n\n";
        $content .= "IMPORTANT: Store these codes in a secure location.\n";
        $content .= "Each code can be used only once if you lose access to your authenticator app.\n\n";

        foreach ($codes as $index => $code) {
            $content .= ($index + 1) . ". " . $code . "\n";
        }

        $content .= "\nThese codes will not be shown again. Keep them safe!\n";

        // Clear after download
        session()->forget('recovery_codes');

        return response($content, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="' . config('app.name') . '-' . 'Recovery Code' . '.txt"',
        ]);
    }

    /**
     * Regenerate recovery codes (invalidate old hashed ones and issue new).
     */
    public function regenerateRecoveryCodes()
    {
        $user = auth()->user();

        if (!$user->google2fa_secret) {
            return back()->with('error', 'Two-Factor Authentication must be enabled first.');
        }

        $user->clearRecoveryCodes();
        $codes = $user->generateRecoveryCodes();

        // keep recovery codes until page reload
        session(['recovery_codes' => $codes]);

        return redirect()->route('admin.profile')
            ->with('success', 'New recovery codes generated. Download them now - they will not be shown again!');
    }
}
