<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:51200',
        ]);

        $user = Auth::user(); // adjust guard if needed

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Delete old image if needed
        if ($user->profile_photo_path && Storage::exists($user->profile_photo_path)) {
            Storage::delete($user->profile_photo_path);
        }

        // Use original filename
        $file = $request->file('profile_image');
        $filename = $file->getClientOriginalName();

        // Store using the original filename in a user-specific folder
        $path = $file->storeAs("public/profile-images/{$user->id}", $filename);

        $user->profile_photo_path = $path;
        $user->save();

        return back()->with('success', 'Profile image updated.');
    }
}
