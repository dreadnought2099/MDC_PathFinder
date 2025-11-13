<?php

namespace App\Http\Controllers\Admin\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\ImageManager;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Drivers\Gd\Driver;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $qrCode = null;
        $secret = null;

        if (!$user->google2fa_secret) {
            $google2fa = new Google2FA();

            $secret = $google2fa->generateSecretKey();

            session(['2fa_secret' => $secret]);

            $qrText = $google2fa->getQRCodeUrl(
                config('app.name'),  // app name shown in authenticator
                $user->email,        // identifier shown in authenticator
                $secret              // the generated secret
            );

            $qrCode = QrCode::size(200)->generate($qrText);
        }

        return view('pages.admin.profile.index', compact('user', 'qrCode', 'secret'));
    }

    public function updateImage(Request $request)
    {
        $user = Auth::user();

        if ($request->hasFile('cropped_image')) {
            // Delete old profile image if exists
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            // OPTION 2: By Username (if username is unique and URL-safe)
            $folderPath = 'profiles/' . Str::slug($user->username);

            $manager = new ImageManager(new Driver());

            $baseName = uniqid('', true);
            $webpPath = "{$folderPath}/{$baseName}.webp";

            // Read uploaded image and encode as WebP (quality 90)
            $image = $manager->read($request->file('cropped_image'))->encode(new WebpEncoder(90));

            // Save to storage
            Storage::disk('public')->put($webpPath, (string) $image);

            // Update user profile path
            $user->profile_photo_path = $webpPath;
            $user->save();

            return response()->json([
                'success' => true,
                'imageUrl' => Storage::url($user->profile_photo_path),
            ]);
        }

        return response()->json(['success' => false], 422);
    }
}
