<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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
        $user = Auth::user();

        if ($request->hasFile('cropped_image')) {
            // Delete old profile image if exists
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            // OPTION 1: By User ID (recommended)
            // $folderPath = 'profiles/' . $user->id;

            // OPTION 2: By Username (if username is unique and URL-safe)
            $folderPath = 'profiles/' . Str::slug($user->username);

            // OPTION 3: By Email domain (group users by organization)
            // $emailDomain = substr(strrchr($user->email, "@"), 1);
            // $folderPath = 'profiles/' . $emailDomain . '/' . $user->id;

            // OPTION 4: By User Role (if you have roles)
            // $folderPath = 'profiles/' . $user->role . '/' . $user->id;

            // OPTION 5: By Year/Month for time-based organization
            // $folderPath = 'profiles/' . date('Y/m') . '/' . $user->id;

            // OPTION 6: Hierarchical by user ID (for large user bases)
            // $userId = str_pad($user->id, 6, '0', STR_PAD_LEFT); // e.g., 000123
            // $folderPath = 'profiles/' . substr($userId, 0, 2) . '/' . substr($userId, 2, 2) . '/' . $user->id;

            $path = $request->file('cropped_image')->store($folderPath, 'public');
            $user->profile_photo_path = $path;
            $user->save();

            return response()->json([
                'success' => true,
                'imageUrl' => Storage::url($user->profile_photo_path),
            ]);
        }

        return response()->json(['success' => false], 422);
    }

    public function destroy()
    {
        $user = Auth::user();  // Get the authenticated user

        // Delete the profile photo if it exists
        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        // Soft delete the user record (marks it as deleted without removing it)
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Profile deleted successfully. Your data has been archived.',
        ]);
    }

    public function restore($userId)
    {
        $user = User::withTrashed()->find($userId);  // Find the soft-deleted user by ID

        if ($user) {
            $user->restore();  // Restore the soft-deleted user

            return response()->json([
                'success' => true,
                'message' => 'Profile restored successfully.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'User not found or not deleted.',
        ], 404);
    }

    // Force delete method
    public function forceDelete()
    {
        $user = Auth::user();  // Get the authenticated user

        // Ensure the user exists and is soft-deleted
        if ($user->trashed()) {

            // Permanently delete the user's profile photo
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            // Force delete the user record (permanent removal)
            $user->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Profile has been permanently deleted.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'User has not been soft deleted or does not exist.',
        ], 404);
    }
}
