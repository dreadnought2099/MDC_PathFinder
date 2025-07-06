<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function index()
    {

        $user = Auth::user();
        return view('pages.admin.profile.index', compact('user'));
    }

    public function updateImage(Request $request)
    {
        $request->validate([
            'cropped_image' => 'required|string',
        ]);

        $imageData = $request->input('cropped_image');
        $image = str_replace('data:image/png;base64,', '', $imageData);
        $image = str_replace(' ', '+', $image);
        $imageName = Str::random(10) . '.png';

        Storage::disk('public')->put("profile_images/{$imageName}", base64_decode($image));

        $user = auth()->user(); // or Auth::guard('admin')->user();
        $user->profile_photo_path = "profile_images/{$imageName}";
        $user->save();

        return back()->with('success', 'Profile image updated!');
    }
}
