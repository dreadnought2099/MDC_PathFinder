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

        $user = Auth::user();

        // Delete old image if exists
        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        // Decode base64 image
        $base64Image = $request->input('cropped_image');
        preg_match("/^data:image\/(\w+);base64,/", $base64Image, $type);
        $image = substr($base64Image, strpos($base64Image, ',') + 1);
        $image = base64_decode($image);
        $extension = $type[1]; // jpg, png, etc.

        $cleanName = Str::slug($user->name);
        $filename = $cleanName . '-' . time() . '.' . $extension;
        $path = 'profile_images/' . $filename;

        Storage::disk('public')->put($path, $image);

        $user->profile_photo_path = $path;
        $user->save();

        return back()->with('success', 'Profile image updated successfully.');
    }
}
