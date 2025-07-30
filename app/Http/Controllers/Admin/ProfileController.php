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
        $user = Auth::user();

        if ($request->hasFile('cropped_image')) {
            $path = $request->file('cropped_image')->store('profiles', 'public');
            $user->profile_photo_path = str_replace('public/', '', $path);
            $user->save();

            return response()->json([
                'success' => true,
                'imageUrl' => Storage::url($user->profile_photo_path),
            ]);
        }

        return response()->json(['success' => false], 422);
    }
}
